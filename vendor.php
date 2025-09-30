<?php
require_once __DIR__ . '/config/db.php';

// Ottieni l'ID venditore da URL
$idVenditore = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Recupero dati venditore
$stmt = $pdo->prepare("SELECT * FROM venditori WHERE id = ?");
$stmt->execute([$idVenditore]);
$venditore = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venditore) {
    echo "<h2>Venditore non trovato</h2>";
    exit;
}

// Recupero i prodotti del venditore
$stmt = $pdo->prepare("
    SELECT id, nome, prezzo, immagine
    FROM prodotti
    WHERE id_venditore = ?
");
$stmt->execute([$idVenditore]);
$prodotti = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'templates/header.php'; ?>

<div class="venditore">
    <h2><?= htmlspecialchars($venditore['nome']) ?></h2>
    <?php if (!empty($venditore['email'])): ?>
        <p><strong>Email:</strong> <?= htmlspecialchars($venditore['email']) ?></p>
    <?php endif; ?>
</div>

<hr>

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
<h3>Recensioni venditore</h3>

<?php 
$recensioniVenditore = getRecensioniVenditore($pdo, $idVenditore);
if (count($recensioniVenditore) > 0): ?>
    <ul class="recensioni">
        <?php foreach ($recensioniVenditore as $r): ?>
            <li>
                <strong><?= htmlspecialchars($r['nome']) ?></strong> 
                (<?= $r['voto'] ?>/5 ⭐) <br>
                <?= nl2br(htmlspecialchars($r['contenuto'])) ?><br>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Nessuna recensione per questo venditore.</p>
<?php endif; ?>

<?php include 'templates/footer.php'; ?>