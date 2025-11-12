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

// Controllo sessione (essenziale per recuperare l'ID utente)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$idUtente = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT id, nome, descrizione FROM liste WHERE id_utente = ?");
$stmt->execute([$idUtente]);
$liste = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Dati da passare a Twig
$data = [
    'page_title' => 'Le tue liste',
    'liste' => $liste,
];

// --- RENDERING DEL TEMPLATE ---
echo $twig->render('liste.twig', $data);