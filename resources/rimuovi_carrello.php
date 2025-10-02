<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$idCarrello = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idCarrello > 0) {
    $stmt = $pdo->prepare("DELETE FROM item_carrello WHERE id = ? AND id_utente = ?");
    $stmt->execute([$idCarrello, $_SESSION['user_id']]);
}

header("Location: ../cart.php");
exit;