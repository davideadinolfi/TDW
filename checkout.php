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

// --- LOGICA DI CONTROLLO E TRANSAZIONE (Nessun cambiamento qui) ---

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$idUtente = $_SESSION['user_id'];
$messaggio = ""; // Inizializzazione del messaggio

$sql = "SELECT id_prodotto FROM item_carrello WHERE id_utente = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$idUtente]);
$prodotti = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($prodotti) > 0) {
    $pdo->beginTransaction();

    try {
        // Logica per l'inserimento dell'ordine e dei dettagli
        $stmt = $pdo->query("SELECT id FROM corrieri ORDER BY RAND() LIMIT 1");
        $idCorriere = $stmt->fetchColumn();
        $insert = $pdo->prepare("INSERT INTO ordini (id_utente, id_corriere) VALUES (?, ?)");
        $insert->execute([$idUtente, $idCorriere]);
        $idOrdine = $pdo->lastInsertId();
        
        $stmt = $pdo->prepare("INSERT INTO item_ordini (id_ordine, id_prodotto) VALUES (?, ?)");
        foreach ($prodotti as $p) {
            $stmt->execute([$idOrdine, $p['id_prodotto']]);
        }

        // Svuota il carrello
        $delete = $pdo->prepare("DELETE FROM item_carrello WHERE id_utente = ?");
        $delete->execute([$idUtente]);

        $pdo->commit();

        $messaggio = "Ordine completato con successo! I tuoi prodotti sono stati salvati.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $messaggio = "Errore durante il checkout: " . $e->getMessage();
    }
} else {
    $messaggio = "Il carrello è vuoto.";
}

// --- RENDERING DEL TEMPLATE ---
$data = [
    'page_title' => 'Checkout',
    'messaggio' => $messaggio,
];

echo $twig->render('checkout.twig', $data);