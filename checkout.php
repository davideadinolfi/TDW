<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$idUtente = $_SESSION['user_id'];

// Prendi i prodotti nel carrello
$sql = "SELECT id_prodotto FROM item_carrello WHERE id_utente = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$idUtente]);
$prodotti = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($prodotti) > 0) {
    // Inizia una transazione per sicurezza
    $pdo->beginTransaction();

    try {
        // Inserisci i prodotti negli ordini
        $stmt = $pdo->query("SELECT id FROM corrieri ORDER BY RAND() LIMIT 1");
        $idCorriere = $stmt->fetchColumn();
        $insert = $pdo->prepare("INSERT INTO ordini (id_utente, id_corriere) VALUES (?, ?)");
        $insert->execute([$idUtente, $idCorriere]);
        $idOrdine = $pdo->lastInsertId();
        $stmt = $pdo->prepare("INSERT INTO item_ordini (id_ordine, id_prodotto) VALUES (?, ?)");
        foreach ($prodotti as $p) {
            $stmt->execute([$idOrdine,$p['id_prodotto']]);
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

include 'templates/header.php';
?>

<h2>Checkout</h2>
<p><?= htmlspecialchars($messaggio) ?></p>
<a href="orders.php" class="btn">Vai ai tuoi ordini</a>

<?php include 'templates/footer.php'; ?>