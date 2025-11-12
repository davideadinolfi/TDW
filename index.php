<?php

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/twig_loader.php';
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
// Inizializzo Twig
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, ['cache' => false]);

// Recupero prodotti in evidenza 
$stmt = $pdo->query("SELECT id, nome, prezzo, immagine, descrizione FROM prodotti LIMIT 3");
$prodotti = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Passo i dati al template
echo $twig->render('home.twig', [
    'page_title' => 'Home - Computer Store',
    'prodotti' => $prodotti
]);
?>