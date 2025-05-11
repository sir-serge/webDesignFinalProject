<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/db.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate database connection first
    if (!$conn) {
        error_log("Database connection failed");
        echo json_encode([
            'success' => false,
            'errorType' => 'connection',
            'message' => 'Database connection failed'
        ]);
        exit;
    }

    $isPharmacist = isset($_POST['isPharmacist']) && $_POST['isPharmacist'] === 'true';

    try {
        if ($isPharmacist) {
            // Log pharmacist registration attempt
            error_log("Attempting pharmacist registration: " . print_r($_POST, true));

            // Validate required pharmacist fields
            $requiredFields = ['licenseNumber', 'firstName', 'lastName', 'email', 'password'];
            foreach ($requiredFields as $field) {
                if (!isset($_POST[$field]) || empty($_POST[$field])) {
                    throw new Exception("Missing required field: $field");
                }
            }

            // Check if license number already exists
            $checkLicense = $conn->prepare("SELECT license_number FROM pharmacistUser WHERE license_number = ?");
            $checkLicense->execute([$_POST['licenseNumber']]);
            if ($checkLicense->rowCount() > 0) {
                throw new Exception("License number already registered");
            }

            $stmt = $conn->prepare("INSERT INTO pharmacistUser (
                license_number, first_name, last_name, email, password,
                pharmacy_name, address, city, state, phone,
                date_of_birth, tin_number, pharmacy_address, pharmacy_city
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $stmt->execute([
                $_POST['licenseNumber'],
                $_POST['firstName'],
                $_POST['lastName'],
                $_POST['email'],
                $hashedPassword,
                $_POST['pharmacyName'],
                $_POST['address'],
                $_POST['city'],
                $_POST['state'],
                $_POST['pharmacyPhone'],
                $_POST['dateOfBirth'],
                $_POST['tinNumber'],
                $_POST['pharmacyAddress'],
                $_POST['pharmacyCity']
            ]);
        } else {
            // Log client registration attempt
            error_log("Attempting client registration: " . print_r($_POST, true));

            // Validate required client fields
            $requiredFields = ['phone', 'firstName', 'lastName', 'email', 'password'];
            foreach ($requiredFields as $field) {
                if (!isset($_POST[$field]) || empty($_POST[$field])) {
                    throw new Exception("Missing required field: $field");
                }
            }

            // Check if phone number already exists
            $checkPhone = $conn->prepare("SELECT phone FROM clientUser WHERE phone = ?");
            $checkPhone->execute([$_POST['phone']]);
            if ($checkPhone->rowCount() > 0) {
                throw new Exception("Phone number already registered");
            }

            $stmt = $conn->prepare("INSERT INTO clientUser (
                phone, first_name, last_name, email, password,
                address, city, state, date_of_birth,
                insurance_number, insurance_provider
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $stmt->execute([
                $_POST['phone'],
                $_POST['firstName'],
                $_POST['lastName'],
                $_POST['email'],
                $hashedPassword,
                $_POST['address'],
                $_POST['city'],
                $_POST['state'],
                $_POST['dateOfBirth'],
                $_POST['insuranceNumber'],
                $_POST['insuranceProvider']
            ]);
        }

        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'userType' => $isPharmacist ? 'pharmacist' : 'client',
                'message' => 'Registration successful!'
            ]);
        } else {
            throw new Exception('Registration failed - no data inserted');
        }
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'errorType' => 'database',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    } catch (Exception $e) {
        error_log("Registration Error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'errorType' => 'validation',
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'errorType' => 'method',
        'message' => 'Invalid request method'
    ]);
}
?>

