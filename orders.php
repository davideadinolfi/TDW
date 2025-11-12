<?php
session_start();

// Inclusione librerie come da tua richiesta
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/twig_loader.php'; 

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// Inizializzo Twig (necessario per l'output)
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, ['cache' => false]);


// --- LOGICA DI CONTROLLO E RECUPERO DATI ---

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$idUtente = $_SESSION['user_id'];

// Recupera ordini con prodotti (SQL e raggruppamento invariati)
$sql = "
    SELECT 
        o.id AS id_ordine, 
        o.created_at, 
        c.nome AS corriere,
        p.nome AS prodotto, 
        p.prezzo, 
        p.immagine
    FROM ordini o
    JOIN item_ordini io ON o.id = io.id_ordine
    JOIN prodotti p ON io.id_prodotto = p.id
    LEFT JOIN corrieri c ON o.id_corriere = c.id
    WHERE o.id_utente = ?
    ORDER BY o.created_at DESC, o.id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$idUtente]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Raggruppa per ordine (Logica essenziale di business/presentazione in PHP)
$ordini = [];
foreach ($rows as $row) {
    $id = $row['id_ordine'];
    if (!isset($ordini[$id])) {
        // Calcolo totale qui per coerenza, sebbene possa essere fatto in Twig
        $ordini[$id] = [
            'id' => $id, // Aggiunto l'ID per facilità in Twig
            'data' => $row['created_at'],
            'corriere' => $row['corriere'],
            'prodotti' => [],
            'totale' => 0.0
        ];
    }
    $ordini[$id]['prodotti'][] = [
        'nome' => $row['prodotto'],
        'prezzo' => (float)$row['prezzo'],
        'immagine' => $row['immagine']
    ];
    $ordini[$id]['totale'] += (float)$row['prezzo'];
}

// Dati da passare a Twig
$data = [
    'page_title' => 'I miei ordini',
    // Usiamo array_values per convertire l'array associativo in numerico, più semplice per Twig
    'ordini' => array_values($ordini),
];

// --- RENDERING DEL TEMPLATE ---
echo $twig->render('orders.twig', $data);