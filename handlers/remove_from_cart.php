<?php
// handlers/remove_from_cart.php
session_start();
require_once '../config/db.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get JSON data from request
$data = json_decode(file_get_contents('php://input'), true);

// Check if data is valid
if (!isset($data['cart_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

// Validate cart_id
$cartId = (int)$data['cart_id'];

try {
    // Check if cart item exists and belongs to this user
    $stmt = $conn->prepare("
        SELECT * FROM cart WHERE id = ? AND client_phone = ?
    ");
    $stmt->execute([$cartId, $_SESSION['phone']]);
    $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$cartItem) {
        echo json_encode(['success' => false, 'message' => 'Item not found in your cart']);
        exit;
    }
    
    // Delete the cart item
    $stmt = $conn->prepare("
        DELETE FROM cart WHERE id = ?
    ");
    $stmt->execute([$cartId]);
    
    echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>