<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';
header('Content-Type: application/json');

try {
    // Ambil semua proyek dengan urutan no_urut sekarang
    $stmt = $pdo->query("SELECT id, no_urut FROM Projects ORDER BY no_urut ASC");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Loop dan update nomor urut agar benar-benar berurutan
    $counter = 1;
    $pdo->beginTransaction();
    $updateStmt = $pdo->prepare("UPDATE Projects SET no_urut = :no_urut WHERE id = :id");

    foreach ($projects as $project) {
        if ($project['no_urut'] != $counter) {
            $updateStmt->execute([':no_urut' => $counter, ':id' => $project['id']]);
        }
        $counter++;
    }
    $pdo->commit();

    // Ambil data proyek yang sudah terurut dengan benar
    $stmt = $pdo->query("SELECT * FROM Projects ORDER BY no_urut ASC");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($projects);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['error' => 'Gagal mengambil data proyek: ' . $e->getMessage()]);
}
?>