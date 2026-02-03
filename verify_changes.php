<?php
// Mock session/auth
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Test Admin';
// Mock admin check? Accessing admin.php directly might redirect if ID group logic is not met.
// Instead of including admin.php which has side effects (redirects), 
// let's manually setup Twig and test the template rendering with mock data.

require_once 'twig_loader.php'; // Existing custom loader

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, ['cache' => false]);

echo "--- Testing List View (Content Display) ---\n";

$dataList = [
    'current_entity' => 'prodotti',
    'columns' => ['id', 'nome', 'descrizione', 'prezzo'],
    'col_types' => [
        'id' => 'int(11)',
        'nome' => 'varchar(100)',
        'descrizione' => 'text', // Should generate raw HTML
        'prezzo' => 'decimal(10,2)'
    ],
    'rows' => [
        [
            'id' => 1,
            'nome' => 'Prodotto Test',
            'descrizione' => '<p><strong>Bold</strong> description</p>',
            'prezzo' => '10.00'
        ]
    ],
    'fk_map' => []
];

$output = $twig->render('backoffice/list.twig', $dataList);

if (strpos($output, '<strong>Bold</strong>') !== false && strpos($output, '&lt;p&gt;') === false) {
    echo "[PASS] List view renders raw HTML for text columns.\n";
} else {
    echo "[FAIL] List view did not render raw HTML.\n";
    // echo $output;
}

echo "\n--- Testing Form View (Dropdowns) ---\n";

$dataForm = [
    'current_entity' => 'prodotti',
    'current_action' => 'edit',
    'row' => ['id' => 1, 'nome' => 'P1', 'id_tipo' => 5],
    'columns' => [
        ['Field' => 'id_tipo', 'Type' => 'int(11)', 'Key' => 'MUL', 'Extra' => '']
    ],
    'foreign_keys' => [
        'id_tipo' => [
            ['id' => 5, 'nome' => 'Elettronica'],
            ['id' => 6, 'nome' => 'Abbigliamento']
        ]
    ],
    'relations' => [] // Old way empty
];

$outputForm = $twig->render('backoffice/form.twig', $dataForm);

if (strpos($outputForm, '<select class="form-select" id="id_tipo"') !== false) {
    echo "[PASS] Form contains dropdown for 'id_tipo'.\n";
} else {
    echo "[FAIL] Form missing dropdown.\n";
}

if (strpos($outputForm, '<option value="5" selected>Elettronica</option>') !== false) {
    echo "[PASS] Dropdown has correct options and selected value.\n";
} else {
    echo "[FAIL] Dropdown options incorrect.\n";
    // echo $outputForm;
}
