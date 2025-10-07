<?php
require 'config/db.php';
include 'templates/header.php';
$idUtente = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM liste WHERE id_utente = ?");
$stmt->execute([$idUtente]);
$liste = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Le tue liste</h2>
<a href="nuovaLista.php">+ Crea nuova lista</a>

<ul>
<?php foreach ($liste as $l): ?>
    <li>
        <a href="dettagliLista.php?id=<?= $l['id'] ?>">
            <?= htmlspecialchars($l['nome']) ?>
        </a> 
        — <?= htmlspecialchars($l['descrizione']) ?>
        (<a href="resources/elimina_lista.php?id=<?= $l['id'] ?>" onclick="return confirm('Eliminare la lista?')">Elimina</a>)
    </li>
<?php endforeach; ?>
</ul>
<?php include 'templates/footer.php'; ?>