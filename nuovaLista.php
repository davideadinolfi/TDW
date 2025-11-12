<?php
// Avvia la sessione se necessario
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclusione librerie come da tua richiesta
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/twig_loader.php'; 

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// Controllo sessione (l'utente deve essere loggato per creare una lista)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// --- LOGICA DI ELABORAZIONE POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Pulizia e acquisizione dati
    $nome = trim($_POST['nome'] ?? '');
    $descrizione = trim($_POST['descrizione'] ?? '');

    if (!empty($nome)) {
        // 2. Inserimento nel database
        $stmt = $pdo->prepare("INSERT INTO liste (id_utente, nome, descrizione) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $nome, $descrizione]);
        
        // 3. Reindirizzamento
        header("Location: liste.php");
        exit;
    }
    // Nota: Aggiungeresti qui la gestione degli errori se l'inserimento non fosse valido.
}

// --- RENDERING DEL TEMPLATE (se non è una richiesta POST valida) ---

// Inizializzo Twig
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, ['cache' => false]);

$data = [
    'page_title' => 'Crea nuova lista',
    // Puoi passare qui eventuali errori o valori pre-compilati se la POST fallisce
];

echo $twig->render('nuovaLista.twig', $data);