<?php
// Avvia la sessione se necessario (necessario per CSRF e flash messages)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'resources/helpers.php';
require_once 'config/db.php';
require_once __DIR__ . '/twig_loader.php';
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
// Inizializzo Twig
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, ['cache' => false]);
// La logica di POST e autenticazione rimane interamente qui.
$errors = [];
$redirect = false; // Flag per evitare il rendering se c'è un reindirizzamento

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... (Tutta la logica di validazione e autenticazione esattamente come l'hai scritta) ...
    // Esempio:
    if (!csrf_check($_POST['csrf'] ?? '')) {
        $errors[] = 'Token CSRF non valido.';
    }

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email non valida.';

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id, nome, email, password FROM utenti WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // login OK
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nome'];
            $_SESSION['user_email'] = $user['email'];
            $stmt=$pdo->prepare('Select id_gruppo from utenti_gruppi where id_utente=?');
            $stmt->execute([$user['id']]);
            $gruppo=$stmt->fetchAll(PDO::FETCH_COLUMN);
            flash_set('success', 'Login effettuato con successo.');
            
            // Imposta il flag per evitare il rendering Twig
            $redirect = true; 
            
            if($gruppo[0]==1)
                header('Location: index.php');
            if($gruppo[0]==2)
                header('Location: admin.php');
            exit;
        } else {
            $errors[] = 'Email o password errata.';
        }
    }
}

// Se non c'è stato reindirizzamento, renderizza la pagina.
if (!$redirect) {
    // Dati da passare a Twig
    $data = [
        'page_title' => 'Login',
        'errors' => $errors,
        'flash_success' => flash_get('success'),
        // Per il token CSRF e l'email precompilata, Twig necessita dei valori passati dal Controller.
        'csrf_token' => csrf_token(),
        'email_value' => $_POST['email'] ?? '' 
    ];

    // Rendering del template
    echo $twig->render('login.twig', $data);
}