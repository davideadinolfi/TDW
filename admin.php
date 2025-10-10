<?php
session_start();
require_once 'config/db.php';   // connessione PDO
require_once 'resources/helpers.php';  // funzioni tipo flash_set()

// 🔒 Controlla se l'utente è loggato
if (!isset($_SESSION['user_id'])) {
    flash_set('error', 'Devi effettuare il login per accedere a questa pagina.');
    header('Location: login.php');
    exit;
}

$idUtente = $_SESSION['user_id'];

// 🔎 Recupera il gruppo dell'utente
$stmt = $pdo->prepare('SELECT id_gruppo FROM utenti_gruppi WHERE id_utente = ? LIMIT 1');
$stmt->execute([$idUtente]);
$idGruppo = $stmt->fetchColumn();

// 🔐 Controlla se è admin (ad esempio gruppo = 2)
if ($idGruppo != 2) {
    flash_set('error', 'Non hai i permessi per accedere a questa pagina.');
    header('Location: index.php');
    exit;
}

// ✅ Se è admin, mostra la dashboard
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Area Amministratore</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Benvenuto nell'area amministratore</h1>
</body>
</html>