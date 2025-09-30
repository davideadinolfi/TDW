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

/**
 * Restituisce le recensioni di un prodotto
 * 
 * @param PDO $pdo connessione
 * @param int $idProdotto id del prodotto
 * @return array elenco recensioni
 */
function getRecensioniProdotto(PDO $pdo, int $idProdotto): array {
    $sql = "
        SELECT r.voto, r.contenuto, r.data, u.nome
        FROM recensioni_prodotti r
        JOIN utenti u ON r.id_utente = u.id
        WHERE r.id_prodotto = ?
        ORDER BY r.data DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idProdotto]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Restituisce tutte le recensioni di un venditore
 */
function getRecensioniVenditore(PDO $pdo, int $idVenditore): array {
    $sql = "
        SELECT r.voto, r.contenuto, u.nome
        FROM recensioni_venditori r
        JOIN utenti u ON r.id_utente = u.id
        WHERE r.id_venditore = ?
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idVenditore]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Inserisce una nuova recensione
 */
function aggiungiRecensioneVenditore(PDO $pdo, int $idVenditore, int $idUtente, int $voto, string $commento): bool {
    $sql = "INSERT INTO recensioni_venditori (id_venditore, id_utente, voto, commento)
            VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$idVenditore, $idUtente, $voto, $commento]);
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