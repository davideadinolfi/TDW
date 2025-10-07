<?php
require '../config/db.php';

$idLista = $_POST['id_lista'];
$idProdotto = $_POST['id_prodotto'];

$stmt = $pdo->prepare("INSERT INTO liste_prodotti (id_lista, id_prodotto) VALUES (?, ?)");
$stmt->execute([$idLista, $idProdotto]);

header("Location: ../dettagliLista.php?id=" . $idLista);
exit;