<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT * FROM minutes"); // pastikan nama tabelnya benar
    $minutes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($minutes);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal ambil data notulen: ' . $e->getMessage()
    ]);
}
