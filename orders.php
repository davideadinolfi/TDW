<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$idUtente = $_SESSION['user_id'];

// Recupera ordini con prodotti
$sql = "
    SELECT 
        o.id AS id_ordine, 
        o.created_at, 
        c.nome AS corriere,
        p.nome AS prodotto, 
        p.prezzo, 
        p.immagine
    FROM ordini o
    JOIN item_ordini io ON o.id = io.id_ordine
    JOIN prodotti p ON io.id_prodotto = p.id
    LEFT JOIN corrieri c ON o.id_corriere = c.id
    WHERE o.id_utente = ?
    ORDER BY o.created_at DESC, o.id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$idUtente]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Raggruppa per ordine
$ordini = [];
foreach ($rows as $row) {
    $id = $row['id_ordine'];
    if (!isset($ordini[$id])) {
        $ordini[$id] = [
            'data' => $row['created_at'],
            'corriere' => $row['corriere'],
            'prodotti' => []
        ];
    }
    $ordini[$id]['prodotti'][] = [
        'nome' => $row['prodotto'],
        'prezzo' => $row['prezzo'],
        'immagine' => $row['immagine']
    ];
}
?>

<?php include 'templates/header.php'; ?>

<h2>I miei ordini</h2>

<?php if (!empty($ordini)): ?>
    <?php foreach ($ordini as $idOrdine => $ordine): ?>
        <div class="ordine">
            <h3>Ordine #<?= $idOrdine ?> - <?= $ordine['data'] ?></h3>
            <p>Corriere: <?= $ordine['corriere'] ?: 'N/D' ?></p>
            
            <table>
                <thead>
                    <tr>
                        <th>Prodotto</th>
                        <th>Prezzo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totale = 0;
                    foreach ($ordine['prodotti'] as $prodotto): 
                        $totale += $prodotto['prezzo'];
                    ?>
                        <tr>
                            <td>
                                <img src="images/<?= htmlspecialchars($prodotto['immagine']) ?>" width="50">
                                <?= htmlspecialchars($prodotto['nome']) ?>
                            </td>
                            <td><?= number_format($prodotto['prezzo'], 2, ',', '.') ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><strong>Totale ordine:</strong> <?= number_format($totale, 2, ',', '.') ?> €</p>
        </div>
        <hr>
    <?php endforeach; ?>
<?php else: ?>
    <p>Non hai ancora effettuato ordini.</p>
<?php endif; ?>

<?php include 'templates/footer.php'; ?>