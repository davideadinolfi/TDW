<?php
// Avvia la sessione se necessario
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/twig_loader.php';
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
// Inizializzo Twig
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, ['cache' => false]);


// --- LOGICA DI CONTROLLO E RECUPERO DATI ---

// Controllo sessione (assunto dal codice originale che include l'header)
if (!isset($_SESSION['user_id'])) {
    // Gestione tipica: reindirizzamento o errore
    header('Location: login.php');
    exit;
}

$idLista = $_GET['id'] ?? 0;
$idUtente = $_SESSION['user_id'];

// 1. Recupera i dettagli della lista e verifica l'appartenenza all'utente
$stmt = $pdo->prepare("SELECT * FROM liste WHERE id = ? AND id_utente = ?");
$stmt->execute([$idLista, $idUtente]);
$lista = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lista) {
    // Se la lista non esiste o non appartiene all'utente
    die("Lista non trovata o non autorizzata.");
}

// 2. Recupera i prodotti all'interno della lista
$stmt = $pdo->prepare("
    SELECT p.*
    FROM liste_prodotti lp
    JOIN prodotti p ON lp.id_prodotto = p.id
    WHERE lp.id_lista = ?");
$stmt->execute([$idLista]);
$prodotti = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Recupera TUTTI i prodotti (per la funzionalità 'aggiungi a lista' futura, se necessaria)
$allProducts = $pdo->query("SELECT * FROM prodotti")->fetchAll(PDO::FETCH_ASSOC);

// Dati da passare a Twig
$data = [
    'page_title' => $lista['nome'],
    'lista' => $lista,
    'prodotti_in_lista' => $prodotti,
    'all_products' => $allProducts, // Puoi non passarlo se non lo usi nella vista
];

// --- RENDERING DEL TEMPLATE ---
echo $twig->render('dettagliLista.twig', $data);