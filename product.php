<?php
require 'config/db.php';
if (!isset($_GET['id'])) {
    header('Location: products.php'); exit;
}
session_start();
$id = (int) $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM prodotti WHERE id = ?");
$stmt->execute([$id]);
$prodotto = $stmt->fetch(PDO::FETCH_ASSOC);
$idUtente = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM liste WHERE id_utente = ?");
$stmt->execute([$idUtente]);
$liste = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$prodotto) {
    die("Prodotto non trovato");
}
$pageTitle = $prodotto['nome'];
$stmt = $pdo -> prepare("SELECT nome FROM venditori WHERE id = $prodotto[id_venditore]");
$stmt -> execute();
$venditore = $stmt -> fetch(PDO::FETCH_ASSOC);
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
    Venduto da: <a href="vendor.php?id=<?= $prodotto['id_venditore'] ?>"><?= htmlspecialchars($venditore['nome']) ?></a>
    <?php if (isset($_SESSION['user_id'])): ?>
    <form action="resources/aggiungi_carrello.php" method="post">
        <input type="hidden" name="id_prodotto" value="<?= $prodotto['id'] ?>">
        <button type="submit" class="btn">Aggiungi al carrello</button>



    </form>
<?php else: ?>
    <p><a href="login.php">Accedi</a> per acquistare.</p>
<?php endif; ?>
</div>
    </form>
  
    <!-- Link per creare una nuova lista -->

            <h3>Aggiungi alla tua lista</h3>
<form action="resources/aggiungi_a_lista.php" method="post">
    <input type="hidden" name="id_prodotto" value="<?= $id ?>">
    
    <label>Lista:</label>
    <select name="id_lista" required>
        <option value="">-- Seleziona una lista --</option>
        <?php foreach ($liste as $l): ?>
            <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['nome']) ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Aggiungi</button>
</form>
<p><a href="nuovalista.php?redirect=product.php?id=<?= $id ?>">+ Crea nuova lista</a></p>
<div class ="recensioni_container">
    <h3>Recensioni</h3>
 
<?php
// Assicurati che session sia avviata, che $idProdotto sia definito
if (isset($_SESSION['user_id'])): ?>
  <h3>Lascia la tua recensione</h3>
  <form action="resources/salva_recensione_prodotto.php" method="post">
    <input type="hidden" name="id_prodotto" value="<?= $id ?>">
    <label for="voto">Voto (1–5):</label>
    <select name="voto" id="voto" required>
      <option value="">--</option>
      <?php for ($i = 1; $i <= 5; $i++): ?>
        <option value="<?= $i ?>"><?= $i ?></option>
      <?php endfor; ?>
    </select>
    <br>
    <label for="commento">Commento:</label><br>
    <textarea name="commento" id="commento" rows="4" required></textarea><br>
    <button type="submit">Invia recensione</button>
  </form>
<?php else: ?>
  <p><a href="login.php">Accedi</a> per lasciare una recensione.</p>
<?php endif; ?>
<?php
$recensioni = getRecensioniProdotto($pdo, $id);
if (count($recensioni) > 0): ?>
    <ul class="recensioni">
        <?php foreach ($recensioni as $r): ?>
            <li>
                <strong><?= htmlspecialchars($r['nome']) ?></strong> 
                (<?= $r['voto'] ?>/5 ⭐) 
                <br>
                <?= nl2br(htmlspecialchars($r['contenuto'])) ?>
                <br>
                <small><?= $r['data'] ?></small>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Nessuna recensione per questo prodotto.</p>
<?php endif; ?>
  </div>


<?php include 'templates/footer.php'; ?>