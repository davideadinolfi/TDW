<?php
require_once 'config/db.php';

function getForeignKeys($pdo, $table)
{
    echo "Checking table: $table\n";
    $stmt = $pdo->prepare("
        SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = ?
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    $stmt->execute([$table]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Test with 'prodotti'
$fks = getForeignKeys($pdo, 'prodotti');
print_r($fks);
