<?php
include_once 'helpers.php';
// puoi passare il titolo alla pagina
if (!isset($pageTitle)) $pageTitle = "Il mio shop";
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <h1><a href="index.php">myShop</a></h1>
    <nav>
      <a href="cart.php">Carrello</a>
      <?php if (is_logged_in()): ?>
        <div class="user-welcome">Ciao, <?= htmlspecialchars(current_user()['name']) ?> | <a href="logout.php">Logout</a></div>
        <?php else: ?>
        <div class="auth-links"><a href="login.php">Accedi</a> | <a href="register.php">Registrati</a></div>
        <?php endif; ?>
        <?php
        if (session_status() === PHP_SESSION_NONE) session_start();
        ?>
    </nav>
  </header>
  <main>