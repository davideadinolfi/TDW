<?php
require 'config/db.php';
include 'templates/header.php';
$idLista = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM liste WHERE id = ? AND id_utente = ?");
$stmt->execute([$idLista, $_SESSION['user_id']]);
$lista = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lista) {
    die("Lista non trovata");
}

$stmt = $pdo->prepare("
    SELECT p.*
    FROM liste_prodotti lp
    JOIN prodotti p ON lp.id_prodotto = p.id
    WHERE lp.id_lista = ?");
$stmt->execute([$idLista]);
$prodotti = $stmt->fetchAll(PDO::FETCH_ASSOC);

$allProducts = $pdo->query("SELECT * FROM prodotti")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2><?= htmlspecialchars($lista['nome']) ?></h2>
<p><?= htmlspecialchars($lista['descrizione']) ?></p>

<h3>Prodotti nella lista</h3>
<table class="carrello">
        <thead>
        </thead>
        <tbody>
            <?php foreach ($prodotti as $item): ?>
            <tr>
                <td>
                    <img src="images/<?= htmlspecialchars($item['immagine']) ?>" 
                         alt="<?= htmlspecialchars($item['nome']) ?>" width="60">
                    <td>
                    <?= htmlspecialchars($item['nome']) ?>
            </td>
                </td>
                <td><?= number_format($item['prezzo'], 2, ',', '.') ?> €</td>
                <td>
                  <a href="resources/rimuovi_da_lista.php?id_lista=<?= $lista['id'] ?>&id_prodotto=<?= $item['id'] ?>" 
   onclick="return confirm('Sei sicuro di voler rimuovere questo prodotto dalla lista?');">
   Rimuovi
</a>  
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php include 'templates/footer.php';?>