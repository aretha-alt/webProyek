<?php
$host = 'localhost';
$dbname = 'projectmanagement'; // ← sudah dibenarkan
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Koneksi gagal: ' . $e->getMessage()]);
    exit;
}
?>
