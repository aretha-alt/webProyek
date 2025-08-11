<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php'; // pastikan $pdo sudah dibuat di sini

$response = [];

try {
    $stmt = $pdo->query("SELECT * FROM Finance ORDER BY id DESC");
    $response = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($response);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Gagal mengambil data keuangan: ' . $e->getMessage()]);
}