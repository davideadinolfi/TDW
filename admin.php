<?php
// Avvia la sessione se necessario
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'resources/helpers.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/twig_loader.php';
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// Inizializzo Twig
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, ['cache' => false]);
$twig->addGlobal('session', $_SESSION);

// 🔒 Controlli di sicurezza
if (!isset($_SESSION['user_id'])) {
    flash_set('error', 'Devi effettuare il login per accedere a questa pagina.');
    header('Location: login.php');
    exit;
}

$idUtente = $_SESSION['user_id'];

$stmt = $pdo->prepare('SELECT id_gruppo FROM utenti_gruppi WHERE id_utente = ? LIMIT 1');
$stmt->execute([$idUtente]);
$idGruppo = $stmt->fetchColumn();

// 🔐 Controlla se è admin (ad esempio gruppo = 2)
if ($idGruppo != 2) {
    flash_set('error', 'Non hai i permessi per accedere a questa pagina.');
    header('Location: index.php');
    exit;
}

// --- LOGICA BACKOFFICE ---

$entity = $_GET['entity'] ?? null;
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Lista delle tabelle ammesse
$allowedTables = [
    'caratteristiche',
    'corrieri',
    'gruppi',
    'gruppi_servizi',
    'item_carrello',
    'item_ordini',
    'liste',
    'liste_prodotti',
    'ordini',
    'prodotti',
    'recensioni_prodotti',
    'recensioni_venditori',
    'servizi',
    'specifiche',
    'tipi',
    'utenti',
    'utenti_gruppi',
    'venditori'
];

$data = [
    'page_title' => 'Area Amministratore',
    'username' => $_SESSION['user_name'] ?? 'Admin',
    'current_entity' => $entity,
    'current_action' => $action
];

if ($entity && in_array($entity, $allowedTables)) {
    // Gestione CRUD
    $data['page_title'] = 'Gestione ' . ucfirst($entity);

    try {
        if ($action === 'list') {
            $stmt = $pdo->query("SELECT * FROM $entity");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $columns = !empty($rows) ? array_keys($rows[0]) : getTableColumns($pdo, $entity);

            // Fetch full column info for type detection in view
            $columnsInfo = getTableColumnsInfo($pdo, $entity);


            // --- GENERIC FK LOADING FOR LIST ---
            $fk_map = []; // [col_name => [id => label, id => label]]
            $fks = getForeignKeys($pdo, $entity);
            foreach ($fks as $fk) {
                $refTable = $fk['REFERENCED_TABLE_NAME'];
                $refCol = $fk['REFERENCED_COLUMN_NAME']; // usually id
                $myCol = $fk['COLUMN_NAME'];

                $labelField = getTableLabelField($pdo, $refTable);

                $stmt = $pdo->query("SELECT $refCol, $labelField FROM $refTable");
                $options = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // [id => label]
                $fk_map[$myCol] = $options;
            }

            $data['columns'] = $columns;
            // Map types for easier lookup in Twig: [field => type]
            $data['col_types'] = array_column($columnsInfo, 'Type', 'Field');
            $data['rows'] = $rows;
            $data['fk_map'] = $fk_map;

            echo $twig->render('backoffice/list.twig', $data);

        } elseif ($action === 'create' || $action === 'edit') {
            $row = null;
            $relations = [];

            if ($action === 'edit' && $id) {
                // Assumendo che la chiave primaria sia 'id' per la maggior parte delle tabelle
                // Per tabelle di relazione come utenti_gruppi serve logica specifica se non hanno ID incrementale
                $pk = getPrimaryKey($pdo, $entity);
                if ($pk) {
                    $stmt = $pdo->prepare("SELECT * FROM $entity WHERE $pk = ?");
                    $stmt->execute([$id]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                }
            }

            // --- LOGICA RELAZIONI (N:N) ---
            if ($entity === 'utenti') {
                // Carica tutti i gruppi
                $stmt = $pdo->query("SELECT id, nome FROM gruppi");
                $allGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $userGroups = [];
                if ($id) {
                    $stmt = $pdo->prepare("SELECT id_gruppo FROM utenti_gruppi WHERE id_utente = ?");
                    $stmt->execute([$id]);
                    $userGroups = $stmt->fetchAll(PDO::FETCH_COLUMN);
                }

                $relations['gruppi'] = [
                    'label' => 'Gruppi',
                    'options' => $allGroups,
                    'selected' => $userGroups,
                    'value_key' => 'id',
                    'label_key' => 'nome'
                ];

            } elseif ($entity === 'gruppi') {
                // Carica tutti i servizi
                $stmt = $pdo->query("SELECT id, nome FROM servizi");
                $allServices = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $groupServices = [];
                if ($id) {
                    $stmt = $pdo->prepare("SELECT id_servizio FROM gruppi_servizi WHERE id_gruppo = ?");
                    $stmt->execute([$id]);
                    $groupServices = $stmt->fetchAll(PDO::FETCH_COLUMN);
                }

                $relations['servizi'] = [
                    'label' => 'Servizi Abilitati',
                    'options' => $allServices,
                    'selected' => $groupServices,
                    'value_key' => 'id',
                    'label_key' => 'nome'
                ];

            }

            // --- GENERIC FK LOADING FOR FORM ---
            // Detect and load data for all FKs automatically
            $fks = getForeignKeys($pdo, $entity);
            foreach ($fks as $fk) {
                $refTable = $fk['REFERENCED_TABLE_NAME'];
                $myCol = $fk['COLUMN_NAME']; // e.g., id_tipo

                $labelField = getTableLabelField($pdo, $refTable); // e.g., nome

                // Fetch options [id, label]
                // Note: we fetch as Assoc to match the expected format in form.twig: opt.id, opt.nome (aliased as)
                $stmt = $pdo->query("SELECT id, $labelField as nome FROM $refTable");
                // Store in $data['foreign_keys'] directly, not in $relations['foreign_keys']
                $data['foreign_keys'][$myCol] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            $data['row'] = $row;
            $data['columns'] = getTableColumnsInfo($pdo, $entity);
            $data['relations'] = $relations;
            echo $twig->render('backoffice/form.twig', $data);

        } elseif ($action === 'save') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Separa i campi della tabella principale dai dati extra (es. relazioni)
                $fields = array_filter($_POST, function ($key) {
                    return $key !== 'action' && $key !== 'entity' && $key !== 'id' && $key !== 'relations';
                }, ARRAY_FILTER_USE_KEY);

                // Raccogli eventuali dati di relazione inviati come array (es. relations[gruppi][])
                $relationsData = $_POST['relations'] ?? [];

                // --- GESTIONE UPLOAD IMMAGINI ---
                if (isset($_FILES['immagine']) && $_FILES['immagine']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/public/images/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $fileName = basename($_FILES['immagine']['name']);
                    // Opzionale: rendere il nome univoco per evitare sovrascritture
                    // $fileName = uniqid() . '_' . $fileName; 

                    $uploadFile = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['immagine']['tmp_name'], $uploadFile)) {
                        $fields['immagine'] = $fileName;
                    } else {
                        flash_set('error', 'Errore durante il caricamento dell\'immagine.');
                        header("Location: admin.php?entity=$entity&action=edit&id=$id");
                        exit;
                    }
                }

                $pdo->beginTransaction();

                try {
                    if ($id) {
                        // Update
                        $pk = getPrimaryKey($pdo, $entity);
                        $setClause = implode(', ', array_map(fn($k) => "$k = ?", array_keys($fields)));
                        $values = array_values($fields);
                        $values[] = $id; // Add ID for WHERE clause

                        $sql = "UPDATE $entity SET $setClause WHERE $pk = ?";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($values);
                        $recordId = $id;
                        flash_set('success', 'Elemento aggiornato con successo.');
                    } else {
                        // Insert
                        $columns = implode(', ', array_keys($fields));
                        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
                        $sql = "INSERT INTO $entity ($columns) VALUES ($placeholders)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(array_values($fields));
                        $recordId = $pdo->lastInsertId();
                        flash_set('success', 'Elemento creato con successo.');
                    }

                    // --- SALVATAGGIO RELAZIONI ---
                    if ($entity === 'utenti' && isset($relationsData['gruppi'])) {
                        // Pulisci vecchie associazioni
                        $stmt = $pdo->prepare("DELETE FROM utenti_gruppi WHERE id_utente = ?");
                        $stmt->execute([$recordId]);

                        // Inserisci nuove
                        $stmt = $pdo->prepare("INSERT INTO utenti_gruppi (id_utente, id_gruppo) VALUES (?, ?)");
                        foreach ($relationsData['gruppi'] as $groupId) {
                            $stmt->execute([$recordId, $groupId]);
                        }
                    }

                    if ($entity === 'gruppi' && isset($relationsData['servizi'])) {
                        // Pulisci vecchie associazioni
                        $stmt = $pdo->prepare("DELETE FROM gruppi_servizi WHERE id_gruppo = ?");
                        $stmt->execute([$recordId]);

                        // Inserisci nuove
                        $stmt = $pdo->prepare("INSERT INTO gruppi_servizi (id_gruppo, id_servizio) VALUES (?, ?)");
                        foreach ($relationsData['servizi'] as $serviceId) {
                            $stmt->execute([$recordId, $serviceId]);
                        }
                    }

                    $pdo->commit();

                } catch (Exception $e) {
                    $pdo->rollBack();
                    flash_set('error', 'Errore durante il salvataggio: ' . $e->getMessage());
                }

                header("Location: admin.php?entity=$entity");
                exit;
            }
        } elseif ($action === 'delete') {
            if ($id) {
                $pk = getPrimaryKey($pdo, $entity);
                $stmt = $pdo->prepare("DELETE FROM $entity WHERE $pk = ?");

                try {
                    $stmt->execute([$id]);
                    flash_set('success', 'Elemento eliminato.');
                } catch (Exception $e) {
                    flash_set('error', 'Errore eliminazione: ' . $e->getMessage());
                }
            }
            header("Location: admin.php?entity=$entity");
            exit;
        }

    } catch (Exception $e) {
        $data['error'] = $e->getMessage();
        echo $twig->render('backoffice/dashboard.twig', $data);
    }

} else {
    // Dashboard
    echo $twig->render('backoffice/dashboard.twig', $data);
}


// --- Helper Functions (da spostare eventualmente) ---

function getTableColumns($pdo, $table)
{
    $stmt = $pdo->query("DESCRIBE $table");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getTableColumnsInfo($pdo, $table)
{
    $stmt = $pdo->query("DESCRIBE $table");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPrimaryKey($pdo, $table)
{
    $stmt = $pdo->query("SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'");
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    // Se è una chiave composta, questo metodo semplice potrebbe fallire o ritornare solo una parte.
    // Per ora gestiamo chiavi singole.
    return $res['Column_name'] ?? 'id';
}

function getForeignKeys($pdo, $table)
{
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

function getTableLabelField($pdo, $table)
{
    // Try to guess a descriptive field name
    $columns = getTableColumns($pdo, $table);
    $candidates = ['nome', 'titolo', 'label', 'name', 'title', 'email', 'username', 'codice'];

    foreach ($candidates as $c) {
        if (in_array($c, $columns))
            return $c;
    }

    // Fallback: first non-id string column or just primary key
    $info = getTableColumnsInfo($pdo, $table);
    foreach ($info as $col) {
        if ($col['Field'] !== 'id' && (strpos($col['Type'], 'char') !== false || strpos($col['Type'], 'text') !== false)) {
            return $col['Field'];
        }
    }

    return 'id';
}
