<?php
require_once 'resources/helpers.php';
require_once 'config/db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
if (!csrf_check($_POST['csrf'] ?? '')) {
$errors[] = 'Token CSRF non valido.';
}


$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email non valida.';


if (empty($errors)) {
$stmt = $pdo->prepare('SELECT id, nome, email, password FROM utenti WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);


if ($user && password_verify($password, $user['password'])) {
// login OK
session_regenerate_id(true);
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['nome'];
$_SESSION['user_email'] = $user['email'];
$stmt=$pdo->prepare('Select id_gruppo from utenti_gruppi where id_utente=?');
$stmt->execute([$user['id']]);
$gruppo=$stmt->fetchAll(PDO::FETCH_COLUMN);
flash_set('success', 'Login effettuato con successo.');
if($gruppo[0]==1)
    header('Location: index.php');
if($gruppo[0]==2)
    header('Location: admin.php');
exit;
} else {
$errors[] = 'Email o password errata.';
}
}
}


$pageTitle = 'Login';
include 'templates/header.php';
?>


<h2>Accedi</h2>


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


<form method="post" action="login.php">
<input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
<label>Email<br><input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"></label><br>
<label>Password<br><input type="password" name="password"></label><br>
<button type="submit">Accedi</button>
</form>


<p>Non hai un account? <a href="register.php">Registrati</a></p>


<?php include 'templates/footer.php'; ?>