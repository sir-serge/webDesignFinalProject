<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/db.php';
session_start();

// Add CORS headers
header('Access-Control-Allow-Origin: http://127.0.0.1:5501');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_POST['email']) || !isset($_POST['password'])) {
        throw new Exception('Email and password are required');
    }

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $isPharmacist = isset($_POST['isPharmacist']) && $_POST['isPharmacist'] === 'true';

    // Check database connection
    if (!$conn) {
        echo json_encode([
            'success' => false,
            'errorType' => 'database',
            'message' => 'Database connection failed'
        ]);
        exit;
    }

    // Select appropriate table
    $table = $isPharmacist ? 'pharmacistUser' : 'clientUser';
    $stmt = $conn->prepare("SELECT * FROM $table WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify user existence
    if (!$user) {
        echo json_encode([
            'success' => false,
            'errorType' => 'no_user',
            'message' => 'User not found'
        ]);
        exit;
    }

    // Verify password
    if (!password_verify($password, $user['password'])) {
        echo json_encode([
            'success' => false,
            'errorType' => 'invalid_password',
            'message' => 'Invalid password'
        ]);
        exit;
    }

    // Set session variables
    $_SESSION['user_id'] = $isPharmacist ? $user['license_number'] : $user['phone'];
    $_SESSION['user_type'] = $isPharmacist ? 'pharmacist' : 'client';
    $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];

    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'userType' => $isPharmacist ? 'pharmacist' : 'client'
    ]);

} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'errorType' => 'database',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'errorType' => 'system',
        'message' => $e->getMessage()
    ]);
}
?>