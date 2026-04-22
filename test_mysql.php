<?php
header('Content-Type: application/json');
require_once 'config.php';

$conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn) {
    echo json_encode([
        'success' => true,
        'database' => DB_NAME,
        'host' => DB_HOST
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => mysqli_connect_error()
    ]);
}
?>
