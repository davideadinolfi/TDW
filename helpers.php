<?php
if (session_status() === PHP_SESSION_NONE) session_start();


function flash_set($key, $msg) {
if (!isset($_SESSION['flash'])) $_SESSION['flash'] = [];
$_SESSION['flash'][$key] = $msg;
}

/**
 * Restituisce un array [nome_caratteristica => specifica] per un prodotto.
 *
 * @param PDO $pdo connessione PDO al DB
 * @param int $idProdotto ID del prodotto
 * @return array
 */
function getSpecificheProdotto(PDO $pdo, int $idProdotto): array {
    $sql = "
        SELECT c.nome_caratteristica, s.specifica
        FROM specifiche s
        JOIN caratteristiche c ON s.id_caratteristica = c.id
        WHERE s.id_prodotto = ?
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idProdotto]);

    $specifiche = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $specifiche[$row['nome_caratteristica']] = $row['specifica'];
    }
    return $specifiche;
}


function flash_get($key) {
if (isset($_SESSION['flash'][$key])) {
$msg = $_SESSION['flash'][$key];
unset($_SESSION['flash'][$key]);
return $msg;
}
return null;
}


function is_logged_in() {
return !empty($_SESSION['user_id']);
}


function current_user() {
if (!is_logged_in()) return null;
return [
'id' => $_SESSION['user_id'],
'name' => $_SESSION['user_name'],
'email' => $_SESSION['user_email'] ?? null
];
}


// CSRF token
function csrf_token() {
if (empty($_SESSION['csrf_token'])) {
$_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
return $_SESSION['csrf_token'];
}


function csrf_check($token) {
return hash_equals($_SESSION['csrf_token'] ?? '', $token ?? '');
}?>