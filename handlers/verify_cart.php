<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['phone'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Check if product exists in cart
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM cart WHERE client_phone = ? AND ndc = ?");
    $stmt->execute([$data['client_phone'], $data['ndc']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'verified' => $result['count'] > 0
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error checking cart status'
    ]);
}
?>