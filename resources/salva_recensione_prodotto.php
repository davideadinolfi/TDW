 <?php
session_start();

require_once 'helpers.php';
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $idProdotto = (int)($_POST['id_prodotto'] ?? 0);
    $idUtente = (int)$_SESSION['user_id'];
    $voto = (int)($_POST['voto'] ?? 0);
    $commento = trim($_POST['commento'] ?? '');

    if ($idProdotto > 0 && $commento !== '' && $voto >= 1 && $voto <= 5) {
        aggiungiRecensioneProdotto($pdo, $idProdotto, $idUtente, $voto, $commento);
    }
}

// Reindirizza alla pagina del prodotto (presumendo che tu abbia product.php?id=…)
header("Location: ../product.php?id=" . $idProdotto);
exit;