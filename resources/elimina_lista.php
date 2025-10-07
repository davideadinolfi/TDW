<?php
require '../config/db.php';
session_start();
$idLista = $_GET['id'] ?? 0;

$pdo->prepare("DELETE FROM liste_prodotti WHERE id_lista = ?")->execute([$idLista]);
$pdo->prepare("DELETE FROM liste WHERE id = ? AND id_utente = ?")->execute([$idLista, $_SESSION['user_id']]);

header("Location: ../liste.php");
exit;