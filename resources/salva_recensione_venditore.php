<?php
session_start();
require_once 'helpers.php';
require_once '../config/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $idVenditore = (int)$_POST['id_venditore'];
    $idUtente    = $_SESSION['user_id'];
    $voto        = (int)$_POST['voto'];
    $contenuto    = trim($_POST['commento']);

    if ($voto >= 1 && $voto <= 5 && $commento !== '') {
        aggiungiRecensioneVenditore($pdo, $idVenditore, $idUtente, $voto, $contenuto);
    }
}

header("Location: ../vendor.php?id=" . $idVenditore);
exit;