<?php
session_start();

// Debugging the issue with add_to_cart.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the request content
$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);

// If the request is not in JSON format, try to get from POST parameters
if (!$data) {
    $data = [
        'ndc' => $_POST['ndc'] ?? null,
        'phone' => $_POST['phone'] ?? null,
        'quantity' => $_POST['quantity'] ?? 1
    ];
}

// Validate request data
if (empty($data['ndc']) || empty($data['phone'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields: NDC code or phone number'
    ]);
    exit;
}

require_once '../config/db.php';

try {
    // Check if the item already exists in the cart
    $stmt = $conn->prepare("
        SELECT id, quantity FROM cart 
        WHERE ndc = ? AND client_phone = ?
    ");
    $stmt->execute([$data['ndc'], $data['phone']]);
    $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingItem) {
        // Update quantity if the item is already in the cart
        $newQuantity = $existingItem['quantity'] + $data['quantity'];
        $stmt = $conn->prepare("
            UPDATE cart SET quantity = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$newQuantity, $existingItem['id']]);
    } else {
        // Insert new item into the cart
        $stmt = $conn->prepare("
            INSERT INTO cart (ndc, client_phone, quantity, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$data['ndc'], $data['phone'], $data['quantity']]);
    }
    
    // Return success response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Item added to cart successfully'
    ]);
    
} catch (PDOException $e) {
    // Log the error and return error response
    error_log("Error adding to cart: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Failed to add item to cart: ' . $e->getMessage()
    ]);
}