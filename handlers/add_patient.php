<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    // Validate required fields
    $requiredFields = ['patientID', 'firstName', 'lastName', 'phone', 'gender', 'dateOfBirth', 'address'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Field {$field} is required");
        }
    }

    // Calculate age from date of birth
    $dob = new DateTime($_POST['dateOfBirth']);
    $today = new DateTime();
    $age = $today->diff($dob)->y;

    // Insert new patient
    $sql = "INSERT INTO PATIENT (
        PatientID, FirstName, LastName, Phone, Email, 
        Gender, DateOfBirth, Age, Address, 
        InsuranceProvider, PolicyNumber
    ) VALUES (
        :patientID, :firstName, :lastName, :phone, :email,
        :gender, :dateOfBirth, :age, :address,
        :insuranceProvider, :policyNumber
    )";

    $stmt = $conn->prepare($sql);
    
    $stmt->execute([
        'patientID' => $_POST['patientID'],
        'firstName' => $_POST['firstName'],
        'lastName' => $_POST['lastName'],
        'phone' => $_POST['phone'],
        'email' => $_POST['email'] ?? null,
        'gender' => $_POST['gender'],
        'dateOfBirth' => $_POST['dateOfBirth'],
        'age' => $age,
        'address' => $_POST['address'],
        'insuranceProvider' => $_POST['insuranceProvider'] ?? null,
        'policyNumber' => $_POST['policyNumber'] ?? null
    ]);

    // Fetch the newly inserted patient to return for card creation
    $stmt = $conn->prepare("SELECT * FROM PATIENT WHERE PatientID = ?");
    $stmt->execute([$_POST['patientID']]);
    $newPatient = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'message' => 'Patient added successfully',
        'patient' => $newPatient
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>