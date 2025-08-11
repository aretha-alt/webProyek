<?php
require 'db.php';

try {
    $stmt = $pdo->query("SELECT * FROM Clients");
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($clients);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Gagal mengambil data klien: ' . $e->getMessage()]);
}