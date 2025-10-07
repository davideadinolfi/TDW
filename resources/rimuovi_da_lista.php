<?php
require_once '../config/db.php';

// Avvia la sessione se non è già attiva
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// L'utente deve essere loggato
if (empty($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Verifica parametri passati via GET
$idLista = $_GET['id_lista'] ?? null;
$idProdotto = $_GET['id_prodotto'] ?? null;

// Se mancano i parametri, reindirizza
if (!$idLista || !$idProdotto) {
    header('Location: liste.php');
    exit;
}

// Verifica che la lista appartenga all'utente loggato
$stmt = $pdo->prepare("SELECT id FROM liste WHERE id = ? AND id_utente = ?");
$stmt->execute([$idLista, $_SESSION['user_id']]);
$lista = $stmt->fetch();

if (!$lista) {
    // L'utente sta cercando di modificare una lista non sua
    header('HTTP/1.1 403 Forbidden');
    echo "Non hai il permesso di modificare questa lista.";
    exit;
}

// Elimina l'elemento dalla lista
$stmt = $pdo->prepare("DELETE FROM liste_prodotti WHERE id_lista = ? AND id_prodotto = ?");
$stmt->execute([$idLista, $idProdotto]);

// Redirect alla lista corrente o alla pagina principale delle liste
header("Location: ../dettagliLista.php?id=$idLista");
exit;
?>