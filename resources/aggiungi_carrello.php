<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$idUtente = $_SESSION['user_id'];
$idProdotto = isset($_POST['id_prodotto']) ? (int)$_POST['id_prodotto'] : 0;

if ($idProdotto > 0) {
    // Inserisci il prodotto nel carrello
    $stmt = $pdo->prepare("INSERT INTO item_carrello (id_utente, id_prodotto) VALUES (?, ?)");
    $stmt->execute([$idUtente, $idProdotto]);
}

// Torna alla pagina del carrello
header("Location: ../cart.php");
exit;