<?php
require 'config/db.php';
include 'templates/header.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descrizione = $_POST['descrizione'] ?? '';
    $stmt = $pdo->prepare("INSERT INTO liste (id_utente, nome, descrizione) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $nome, $descrizione]);
    header("Location: liste.php");
    exit;
}
?>

<h2>Crea nuova lista</h2>
<form method="post">
    <label>Nome: <input type="text" name="nome" required></label><br>
    <label>Descrizione: <textarea name="descrizione"></textarea></label><br>
    <button type="submit">Crea</button>
</form>
<?php include 'templates/footer.php'; ?>