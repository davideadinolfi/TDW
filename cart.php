<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$idUtente = $_SESSION['user_id'];

// Recupera i prodotti nel carrello
$sql = "
    SELECT c.id AS id_carrello, p.id AS id_prodotto, p.nome, p.prezzo, p.immagine
    FROM item_carrello c
    JOIN prodotti p ON c.id_prodotto = p.id
    WHERE c.id_utente = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$idUtente]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcola totale
$totale = 0;
foreach ($items as $item) {
    $totale += $item['prezzo'];
}
?>

<?php include 'templates/header.php'; ?>

<h2>Il tuo carrello</h2>

<?php if (count($items) > 0): ?>
    <table class="carrello">
        <thead>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
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
                    <a href="resources/rimuovi_carrello.php?id=<?= $item['id_carrello'] ?>" 
                       class="btn-danger">Rimuovi</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Totale: <?= number_format($totale, 2, ',', '.') ?> €</h3>
    <a href="checkout.php" class="btn">Procedi al checkout</a>

<?php else: ?>
    <p>Il tuo carrello è vuoto.</p>
<?php endif; ?>

<?php include 'templates/footer.php'; ?>