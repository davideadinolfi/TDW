<?php require 'config/db.php';
// recupero prodotti
$stmt = $pdo->query("SELECT id, nome, prezzo, immagine FROM prodotti");
$prodotti = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Prodotti</h2>
<div class="grid">
<?php foreach ($prodotti as $prodotto): ?>
  <div class="card">
    <img src="images/<?= htmlspecialchars($prodotto['immagine']) ?>" alt="<?= htmlspecialchars($prodotto['nome']) ?>">
    <h3><?= htmlspecialchars($prodotto['nome']) ?></h3>
    <p>€ <?= number_format($prodotto['prezzo'], 2, ',', '.') ?></p>
    <a href="product.php?id=<?= $prodotto['id'] ?>" class="btn">Dettagli</a>
  </div>
<?php endforeach; ?>
</div>

