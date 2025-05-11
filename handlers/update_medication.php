<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'pharmacist') {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    $stmt = $conn->prepare("
        UPDATE medicine 
        SET medication_name = ?,
            generic_name = ?,
            category = ?,
            stock = ?,
            reorder_point = ?,
            unit_price = ?
        WHERE ndc = ?
    ");

    $stmt->execute([
        $data['name'],
        $data['generic'],
        $data['category'],
        $data['stock'],
        $data['reorder'],
        $data['price'],
        $data['ndc']
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Medication updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No changes made'
        ]);
    }
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>