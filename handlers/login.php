<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

$phone = $_POST['phone'] ?? '';
$password = $_POST['password'] ?? '';

try {
    // Validate input
    if (empty($phone) || empty($password)) {
        throw new Exception('Please enter both phone and password');
    }

    // Fetch user from database
    $stmt = $conn->prepare("SELECT * FROM clientUser WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('Invalid phone or password');
    }

    // Verify password
    if (!password_verify($password, $user['password'])) {
        throw new Exception('Invalid phone or password');
    }

    // Add this line before setting the session variable
    error_log("User's phone from database: " . $user['phone']);

    // Set session variables
    $_SESSION['phone'] = $user['phone'];
    session_regenerate_id(true); // Prevent session fixation

    echo json_encode(['success' => true, 'message' => 'Login successful']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}