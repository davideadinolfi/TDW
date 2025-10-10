<?php
require 'config/db.php';
require_once 'resources/helpers.php';


$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
if (!csrf_check($_POST['csrf'] ?? '')) {
$errors[] = 'Token CSRF non valido.';
}


$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';


if ($name === '') $errors[] = 'Il nome è obbligatorio.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email non valida.';
if (strlen($password) < 6) $errors[] = 'La password deve essere di almeno 6 caratteri.';
if ($password !== $password_confirm) $errors[] = 'Le password non corrispondono.';


if (empty($errors)) {
// verifico email unica
$stmt = $pdo->prepare('SELECT id FROM utenti WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
$errors[] = 'Esiste già un account con questa email.';
} else {
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO utenti (nome, email, password) VALUES (?, ?, ?)');
$stmt->execute([$name, $email, $hash]);
$id=$pdo->lastInsertId();
$stmt = $pdo->prepare('INSERT INTO utenti_gruppi (id_utente, id_gruppo) VALUES (?, ?)');
$stmt->execute([$id,1]);

flash_set('success', 'Registrazione completata. Ora puoi effettuare il login.');
header('Location: login.php');
exit;
}
}
}


$pageTitle = 'Registrazione';
include 'templates/header.php';
?>


<h2>Registrati</h2>


<?php if ($msg = flash_get('success')): ?>
<p class="success"><?= htmlspecialchars($msg) ?></p>
<?php endif; ?>


<?php if (!empty($errors)): ?>
<div class="errors">
<ul>
<?php foreach ($errors as $e): ?>
<li><?= htmlspecialchars($e) ?></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>


<form method="post" action="register.php">
<input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
<label>Nome<br><input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"></label><br>
<label>Email<br><input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"></label><br>
<label>Password<br><input type="password" name="password"></label><br>
<label>Conferma password<br><input type="password" name="password_confirm"></label><br>
<button type="submit">Registrati</button>
</form>


<?php include 'templates/footer.php'; ?>