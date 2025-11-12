Certamente. Il codice per la Registrazione mantiene tutta la logica di validazione e inserimento database nel Controller PHP e utilizza Twig solo per visualizzare il modulo, i messaggi di errore e i valori pre-compilati.

Userò la tua struttura di inizializzazione Twig preferita.

1. Il File PHP (Controller)
Questo file (es. register.php) gestisce l'input del form, la validazione, la logica di business (email unica, hashing password) e il reindirizzamento.

PHP

<?php
// Avvia la sessione se necessario
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclusione librerie come da tua richiesta
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/twig_loader.php'; 
require_once 'resources/helpers.php'; // Mantengo helpers.php per CSRF e flash_set

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// Inizializzo Twig
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, ['cache' => false]);

// --- LOGICA DI ELABORAZIONE POST E REGISTRAZIONE ---
$errors = [];
$redirect = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Tutta la logica di validazione e inserimento rimane QUI.
    
    if (!csrf_check($_POST['csrf'] ?? '')) {
        $errors[] = 'Token CSRF non valido.';
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validazione
    if ($name === '') $errors[] = 'Il nome è obbligatorio.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email non valida.';
    if (strlen($password) < 6) $errors[] = 'La password deve essere di almeno 6 caratteri.';
    if ($password !== $password_confirm) $errors[] = 'Le password non corrispondono.';

    if (empty($errors)) {
        // Verifica email unica
        $stmt = $pdo->prepare('SELECT id FROM utenti WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Esiste già un account con questa email.';
        } else {
            // Inserimento
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO utenti (nome, email, password) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, $hash]);
            $id = $pdo->lastInsertId();
            
            // Assegnazione Gruppo
            $stmt = $pdo->prepare('INSERT INTO utenti_gruppi (id_utente, id_gruppo) VALUES (?, ?)');
            $stmt->execute([$id, 1]); // Assegna al gruppo 1 (Utente Standard)

            flash_set('success', 'Registrazione completata. Ora puoi effettuare il login.');
            header('Location: login.php');
            exit;
        }
    }
}

// --- RENDERING DEL TEMPLATE ---

// Dati da passare a Twig
$data = [
    'page_title' => 'Registrazione',
    'errors' => $errors,
    'flash_success' => flash_get('success'),
    // Precompilazione dei campi dopo un errore POST
    'name_value' => $_POST['name'] ?? '',
    'email_value' => $_POST['email'] ?? '',
    // Token CSRF (passato come variabile)
    'csrf_token' => csrf_token()
];

echo $twig->render('register.twig', $data);