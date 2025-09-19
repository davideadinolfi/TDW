<?php
require 'config/db.php';
if (!isset($_GET['id'])) {
    header('Location: products.php'); exit;
}
$id = (int) $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM prodotti WHERE id = ?");
$stmt->execute([$id]);
$prodotto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prodotto) {
    die("Prodotto non trovato");
}
$pageTitle = $prodotto['nome'];
include 'templates/header.php';
?>

<div class="product-detail">
  <img src="images/<?= htmlspecialchars($prodotto['immagine']) ?>" alt="<?= htmlspecialchars($prodotto['nome']) ?>">
  <div>
    <h2><?= htmlspecialchars($prodotto['nome']) ?></h2>
    <p><?= nl2br(htmlspecialchars($prodotto['descrizione'])) ?></p>
    <?php
    
      $specifiche = getSpecificheProdotto($pdo, $id);

      echo "<h3>Specifiche prodotto</h3>";
      echo "<dl class='specifiche'>";
      foreach ($specifiche as $caratteristica => $valore) {
          echo "<dt>" . htmlspecialchars($caratteristica) . ":</dt><dd> " . htmlspecialchars($valore) . "</dd>";
      }
      echo "</dl>";
    ?>
    <p class="price">€ <?= number_format($prodotto['prezzo'], 2, ',', '.') ?></p>
    <form action="cart.php" method="post">
      <input type="hidden" name="id" value="<?= $prodotto['id'] ?>">
      <button type="submit">Aggiungi al carrello</button>
    </form>
  </div>
</div>

<?php include 'templates/footer.php'; ?>