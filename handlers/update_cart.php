<?php
// handlers/update_cart.php
session_start();
require_once '../config/db.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get JSON data from request
$data = json_decode(file_get_contents('php://input'), true);

// Check if data is valid
if (!isset($data['cart_id']) || !isset($data['action'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

// Validate cart_id and action
$cartId = (int)$data['cart_id'];
$action = $data['action'];

// Validate action
if (!in_array($action, ['increase', 'decrease'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

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
    
    // Update quantity based on action
    $newQuantity = $cartItem['quantity'];
    
    if ($action === 'increase') {
        $newQuantity += 1;
    } else { // decrease
        $newQuantity -= 1;
    }
    
    // Enforce minimum quantity of 1
    if ($newQuantity < 1) {
        $newQuantity = 1;
    }
    
    // Update the cart item
    $stmt = $conn->prepare("
        UPDATE cart SET quantity = ?, updated_at = NOW() WHERE id = ?
    ");
    $stmt->execute([$newQuantity, $cartId]);
    
    echo json_encode(['success' => true, 'message' => 'Cart updated successfully']);
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>