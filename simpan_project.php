<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php'; // Gunakan $pdo, bukan $conn
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['project-id'] ?? null;
    $name = trim($_POST['project-name'] ?? '');
    $code = trim($_POST['project-code'] ?? '');
    $client = trim($_POST['client'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $startDate = $_POST['start-date'] ?? '';
    $endDate = $_POST['end-date'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $status = 'Aktif';

    // Validasi dasar
    if (
        empty($name) || empty($code) || empty($client) || empty($category) ||
        empty($startDate) || empty($endDate)
    ) {
        $response['message'] = 'Semua bidang wajib diisi.';
        echo json_encode($response);
        exit;
    }

    try {
        if ($id) {
            // Update proyek yang sudah ada, termasuk status jika ingin diupdate
            $stmt = $pdo->prepare("UPDATE Projects 
                SET name = :name, code = :code, client = :client, category = :category, 
                    start_date = :start_date, end_date = :end_date, description = :description, status = :status
                WHERE id = :id");

            $success = $stmt->execute([
                ':name' => $name,
                ':code' => $code,
                ':client' => $client,
                ':category' => $category,
                ':start_date' => $startDate,
                ':end_date' => $endDate,
                ':description' => $description,
                ':status' => $status,
                ':id' => $id
            ]);
        } else {
            // Cek duplikat kode proyek
            $check = $pdo->prepare("SELECT COUNT(*) FROM Projects WHERE code = :code");
            $check->execute([':code' => $code]);
            if ($check->fetchColumn() > 0) {
                $response['message'] = 'Kode proyek sudah digunakan.';
                echo json_encode($response);
                exit;
            }

            // Tentukan nomor urut yang benar
            $stmt = $pdo->query("SELECT COUNT(*) FROM Projects");
            $totalProjects = $stmt->fetchColumn();
            $no_urut = $totalProjects + 1; // Menetapkan no_urut berdasarkan total proyek yang ada

            // Memasukkan proyek baru
            $stmt = $pdo->prepare("INSERT INTO Projects 
                (name, code, client, category, start_date, end_date, description, status, no_urut)
                VALUES (:name, :code, :client, :category, :start_date, :end_date, :description, :status, :no_urut)");

            $success = $stmt->execute([
                ':name' => $name,
                ':code' => $code,
                ':client' => $client,
                ':category' => $category,
                ':start_date' => $startDate,
                ':end_date' => $endDate,
                ':description' => $description,
                ':status' => $status,
                ':no_urut' => $no_urut
            ]);

            // Setelah menambahkan proyek baru, menyusun ulang nomor urut proyek lainnya
            $pdo->exec("SET @counter := 0");
            $pdo->exec("UPDATE Projects SET no_urut = (@counter := @counter + 1) ORDER BY no_urut ASC");
        }

        $response['success'] = $success;
        $response['message'] = $success ? 'Data berhasil disimpan.' : 'Gagal menyimpan data.';
    } catch (PDOException $e) {
        $response['message'] = 'Kesalahan database: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Metode request tidak valid.';
}

echo json_encode($response);
?>