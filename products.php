<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/twig_loader.php';
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, ['cache' => false]);
// 3. Recupero Dati
try {
    $stmt = $pdo->query("SELECT id, nome, prezzo, immagine FROM prodotti");
    $prodotti = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (\PDOException $e) {
    die("Errore nel recupero dei prodotti: " . $e->getMessage());
}

// 4. Renderizzazione del Template
// Diciamo a Twig di usare "products.twig" e gli passiamo 
// i dati (la variabile 'prodotti')
echo $twig->render('sfoglia.twig', [
    'page_title' => 'Prodotti',
    'prodotti' => $prodotti
]);
