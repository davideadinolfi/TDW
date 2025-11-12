<?php
// Avvia la sessione se necessario
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'resources/helpers.php'; 
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/twig_loader.php';
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
// Inizializzo Twig
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, ['cache' => false]);
// 🔒 Controlli di sicurezza (la logica DEVE restare qui)
if (!isset($_SESSION['user_id'])) {
    flash_set('error', 'Devi effettuare il login per accedere a questa pagina.');
    header('Location: login.php');
    exit;
}

$idUtente = $_SESSION['user_id'];

$stmt = $pdo->prepare('SELECT id_gruppo FROM utenti_gruppi WHERE id_utente = ? LIMIT 1');
$stmt->execute([$idUtente]);
$idGruppo = $stmt->fetchColumn();

// 🔐 Controlla se è admin (ad esempio gruppo = 2)
if ($idGruppo != 2) {
    flash_set('error', 'Non hai i permessi per accedere a questa pagina.');
    header('Location: index.php');
    exit;
}

// ✅ Se i controlli sono superati, l'utente è un admin.
// Si prepara la visualizzazione.

$data = [
    'page_title' => 'Area Amministratore',
    'username' => $_SESSION['user_name'] ?? 'Admin' // Assumendo che il nome sia in sessione
];

// --- RENDERING DEL TEMPLATE ---
echo $twig->render('admin.twig', $data);