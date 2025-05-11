<?php
require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $ndc = filter_var($_POST['ndc'], FILTER_SANITIZE_STRING);
    $medication_name = filter_var($_POST['medication'], FILTER_SANITIZE_STRING);
    $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
    $stock = filter_var($_POST['stock'], FILTER_SANITIZE_NUMBER_INT);
    $reorder_point = filter_var($_POST['reorderPoint'], FILTER_SANITIZE_NUMBER_INT);
    $unit_price = filter_var($_POST['unitPrice'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $expiry_date = filter_var($_POST['expiryDate'], FILTER_SANITIZE_STRING);

    try {
        // Check if medicine already exists
        $check_stmt = $conn->prepare("SELECT ndc FROM medicine WHERE ndc = ?");
        $check_stmt->execute([$ndc]);

        if ($check_stmt->rowCount() > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Medicine with this NDC already exists',
                'type' => 'error'
            ]);
            exit;
        }

        // Handle image upload
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png'];
            $filename = $_FILES['image']['name'];
            $filetype = pathinfo($filename, PATHINFO_EXTENSION);

            if (!in_array(strtolower($filetype), $allowed)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Only JPG, JPEG, and PNG files are allowed',
                    'type' => 'error'
                ]);
                exit;
            }

            $new_filename = uniqid() . '.' . $filetype;
            $upload_path = '../uploads/medicines/' . $new_filename;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to upload image',
                    'type' => 'error'
                ]);
                exit;
            }
            
            $image_path = 'uploads/medicines/' . $new_filename;
        }

        // Determine status based on stock level
        $status = 'normal';
        if ($stock <= 5) {
            $status = 'critical';
        } elseif ($stock <= 20) {
            $status = 'low';
        } elseif ($stock > 100) {
            $status = 'overstock';
        }

        // Insert new medicine
        $insert_stmt = $conn->prepare(
            "INSERT INTO medicine (ndc, medication_name, category, stock, reorder_point, 
             unit_price, expiry_date, image_path, status) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $insert_stmt->execute([
            $ndc,
            $medication_name,
            $category,
            $stock,
            $reorder_point,
            $unit_price,
            $expiry_date,
            $image_path,
            $status
        ]);

        // Check expiry date for warnings
        $today = new DateTime();
        $expiry = new DateTime($expiry_date);
        $days_until_expiry = $today->diff($expiry)->days;

        $warning_type = '';
        if ($days_until_expiry <= 5) {
            $warning_type = 'critical';
        } elseif ($days_until_expiry <= 10) {
            $warning_type = 'warning';
        }

        echo json_encode([
            'success' => true,
            'message' => 'Medicine added successfully',
            'type' => 'success',
            'warning_type' => $warning_type,
            'days_until_expiry' => $days_until_expiry
        ]);

    } catch(PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage(),
            'type' => 'error'
        ]);
    }
}
?>