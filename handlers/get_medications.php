<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    $stmt = $conn->query("
        SELECT 
            ndc,
            medication_name,
            category,
            stock,
            reorder_point,
            unit_price,
            expiry_date,
            status
        FROM medicine
        ORDER BY medication_name ASC
    ");
    
    $medications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($medications)) {
        echo json_encode([
            'success' => true,
            'message' => 'No medications found',
            'data' => []
        ]);
    } else {
        echo json_encode($medications);
    }
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>