<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';
header('Content-Type: application/json');

// Ambil nama klien dari input
$name = trim($_POST['name'] ?? '');

// Validasi input
if ($name === '') {
    echo json_encode(['success' => false, 'message' => 'Nama klien tidak boleh kosong.']);
    exit;
}

try {
    // Cek apakah klien sudah ada
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM clients WHERE name = :name");
    $stmt->execute(['name' => $name]);
    $exists = $stmt->fetchColumn();

    if ($exists) {
        echo json_encode(['success' => false, 'message' => 'Klien sudah terdaftar.']);
        exit;
    }

    // Simpan klien baru
    $stmt = $pdo->prepare("INSERT INTO clients (name) VALUES (:name)");
    $success = $stmt->execute(['name' => $name]);

    // Jika klien berhasil disimpan, kirimkan respons sukses
    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Klien berhasil disimpan.' : 'Gagal menyimpan klien.'
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Kesalahan database: ' . $e->getMessage()
    ]);
}
?>