<?php
// handlers/remove_from_cart.php
session_start();
require_once '../config/db.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['phone']) && !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Get user phone from either session structure
$userPhone = isset($_SESSION['phone']) ? $_SESSION['phone'] : $_SESSION['user_id'];

// Get JSON data from request
$data = json_decode(file_get_contents('php://input'), true);

// Check if data is valid
if (!isset($data['cart_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing cart ID']);
    exit;
}

// Validate cart_id
$cartId = filter_var($data['cart_id'], FILTER_VALIDATE_INT);
if ($cartId === false) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart ID']);
    exit;
}

try {
    // Check if cart item exists and belongs to this user
    $stmt = $conn->prepare("SELECT id FROM cart WHERE id = ? AND client_phone = ?");
    $stmt->execute([$cartId, $userPhone]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Item not found in your cart']);
        exit;
    }
    
    // Delete the cart item
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND client_phone = ?");
    $success = $stmt->execute([$cartId, $userPhone]);
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Item removed successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove item']);
    }
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>