<?php
header('Content-Type: application/json');
require_once 'config.php';

$conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    echo json_encode([
        'success' => false,
        'error' => 'Cannot connect to database: ' . mysqli_connect_error()
    ]);
    exit;
}

$result = $conn->query("SHOW TABLES");
$tables = [];
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

if (count($tables) >= 4) {
    echo json_encode([
        'success' => true,
        'message' => 'Found ' . count($tables) . ' tables: ' . implode(', ', $tables)
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Expected 4+ tables, found ' . count($tables) . '. Tables: ' . implode(', ', $tables)
    ]);
}

$conn->close();
?>
