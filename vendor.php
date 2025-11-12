<?php
// Avvia la sessione se necessario (per controllo login)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclusione librerie come da tua richiesta
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/twig_loader.php'; 

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// Inizializzo Twig
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, ['cache' => false]);

// --- LOGICA DI CONTROLLO E RECUPERO DATI ---

$idVenditore = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$data = [];
$data['is_logged_in'] = isset($_SESSION['user_id']); // Passa lo stato di login

// 1. Recupero dati venditore
$stmt = $pdo->prepare("SELECT * FROM venditori WHERE id = ?");
$stmt->execute([$idVenditore]);
$venditore = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venditore) {
    // Gestione errore (muoviamo la visualizzazione degli errori fuori da Twig se usiamo die/exit)
    echo $twig->render('error.twig', ['message' => "Venditore non trovato"]);
    exit;
}
$data['venditore'] = $venditore;
$data['page_title'] = $venditore['nome'];

// 2. Recupero i prodotti del venditore
$stmt = $pdo->prepare("
    SELECT id, nome, prezzo, immagine
    FROM prodotti
    WHERE id_venditore = ?
");
$stmt->execute([$idVenditore]);
$data['prodotti'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Recupero recensioni venditore (Assumendo che getRecensioniVenditore esista e sia incluso)
if (function_exists('getRecensioniVenditore')) {
    $data['recensioni_venditore'] = getRecensioniVenditore($pdo, $idVenditore);
} else {
    $data['recensioni_venditore'] = []; // Fallback
}


// --- RENDERING DEL TEMPLATE ---
echo $twig->render('vendor.twig', $data);