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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Handle image upload
        $targetDir = "../uploads/";
        $fileName = basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Insert medication data
        $stmt = $conn->prepare("
            INSERT INTO medicine 
            (ndc, medication_name, category, stock, reorder_point, unit_price, expiry_date, image_path)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $_POST['ndc'],
            $_POST['medication'],
            $_POST['category'],
            $_POST['stock'],
            $_POST['reorderPoint'],
            $_POST['unitPrice'],
            $_POST['expiryDate'],
            $fileName
        ]);

        // Move uploaded file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            echo json_encode([
                'success' => true,
                'message' => 'Medication added successfully'
            ]);
        } else {
            throw new Exception('Error uploading image');
        }
    } catch(PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    } catch(Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
?>