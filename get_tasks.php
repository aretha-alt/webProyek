<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php'; // Memuat $pdo

header('Content-Type: application/json');

$response = [];

try {
    $stmt = $pdo->query("SELECT * FROM Tasks ORDER BY id DESC");
    $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($response);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Gagal mengambil data tugas: ' . $e->getMessage()]);
}