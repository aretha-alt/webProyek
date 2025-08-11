<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php'; // gunakan $pdo dari file ini

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $id = $data['id'] ?? null;
    $newStatus = $data['status'] ?? null;

    if ($id === null || $newStatus === null) {
        $response['message'] = 'ID proyek atau status baru tidak ditemukan.';
        echo json_encode($response);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE Projects SET status = :status WHERE id = :id");
        $stmt->execute([
            ':status' => $newStatus,
            ':id' => $id
        ]);

        if ($stmt->rowCount() > 0) {
            $response['success'] = true;
            $response['message'] = 'Status proyek berhasil diubah.';
        } else {
            $response['message'] = 'Proyek tidak ditemukan atau status tidak berubah.';
        }
    } catch (PDOException $e) {
        $response['message'] = 'Gagal mengubah status proyek: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Metode request tidak valid.';
}

echo json_encode($response);