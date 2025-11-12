<?php
// Avvia la sessione
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclusione librerie e setup di Twig (come nei precedenti esempi)
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/twig_loader.php';
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
// Inizializzo Twig
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, ['cache' => false]);
// --- LOGICA DI CONTROLLO E RECUPERO DATI ---

// Controllo autenticazione
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$idUtente = $_SESSION['user_id'];
$data = [];
$data['page_title'] = "Il tuo carrello";

// Recupera i prodotti nel carrello
$sql = "
    SELECT c.id AS id_carrello, p.id AS id_prodotto, p.nome, p.prezzo, p.immagine
    FROM item_carrello c
    JOIN prodotti p ON c.id_prodotto = p.id
    WHERE c.id_utente = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$idUtente]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcola totale
$totale = 0;
foreach ($items as $item) {
    $totale += $item['prezzo'];
}

// Passa i dati a Twig
$data['items'] = $items;
$data['totale'] = $totale;

// --- RENDERING DEL TEMPLATE ---
echo $twig->render('cart.twig', $data);