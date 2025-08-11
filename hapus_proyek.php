<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Baca JSON body
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;

    if (!$id) {
        $response['message'] = 'ID proyek tidak ditemukan.';
        echo json_encode($response);
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM Projects WHERE id = :id");
        $success = $stmt->execute([':id' => $id]);

        if ($success) {
            $pdo->exec("SET @counter := 0");
            $stmt = $pdo->prepare("UPDATE Projects SET no_urut = (@counter := @counter + 1) ORDER BY no_urut ASC");
            $stmt->execute();
        }

        $response['success'] = $success;
        $response['message'] = $success ? 'Proyek berhasil dihapus dan nomor urut diperbarui.' : 'Gagal menghapus proyek.';
    } catch (PDOException $e) {
        $response['message'] = 'Kesalahan database: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Metode request tidak valid.';
}

echo json_encode($response);