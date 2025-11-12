<?php
require_once __DIR__ . '/resources/helpers.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/twig_loader.php';
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// Inizializzazione Twig e caricamento del layout
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new \Twig\Environment($loader, ['cache' => false]);

// --- LOGICA DI CONTROLLO E RECUPERO DATI ---

// Avvia la sessione (se non è già attiva)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Controllo ID Prodotto
if (!isset($_GET['id'])) {
    header('Location: products.php'); exit;
}
$idProdotto = (int) $_GET['id'];

// Array di dati da passare a Twig
$data = [];
$data['is_logged_in'] = isset($_SESSION['user_id']);
$data['product_id'] = $idProdotto;

// 1. Recupero Prodotto
$stmt = $pdo->prepare("SELECT * FROM prodotti WHERE id = ?");
$stmt->execute([$idProdotto]);
$prodotto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prodotto) {
    die("Prodotto non trovato");
}
$data['prodotto'] = $prodotto;
$data['page_title'] = $prodotto['nome'];

// 2. Recupero Venditore
// Nota: La query originale usava $prodotto[id_venditore] senza bind,
// qui si usa bind o interpolazione sicura per recuperare il nome del venditore.
$stmt = $pdo->prepare("SELECT nome FROM venditori WHERE id = ?");
$stmt->execute([$prodotto['id_venditore']]);
$data['venditore'] = $stmt->fetch(PDO::FETCH_ASSOC);

// 3. Recupero Specifiche (Assumendo che getSpecificheProdotto esista e sia incluso)
if (function_exists('getSpecificheProdotto')) {
    $data['specifiche'] = getSpecificheProdotto($pdo, $idProdotto);
} else {
    $data['specifiche'] = []; // Fallback
}

// 4. Recupero Liste Utente (se loggato)
$data['liste'] = [];
if ($data['is_logged_in']) {
    $idUtente = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT id, nome FROM liste WHERE id_utente = ?");
    $stmt->execute([$idUtente]);
    $data['liste'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 5. Recupero Recensioni (Assumendo che getRecensioniProdotto esista e sia incluso)
if (function_exists('getRecensioniProdotto')) {
    $data['recensioni'] = getRecensioniProdotto($pdo, $idProdotto);
} else {
    $data['recensioni'] = []; // Fallback
}


// --- RENDERING DEL TEMPLATE ---

echo $twig->render('product.twig', $data);