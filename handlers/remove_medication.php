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
    $ndc = $_POST['medication'];
    $quantity = intval($_POST['quantity']);
    $reason = $_POST['reason'];
    $otherReason = $_POST['otherReason'];

    try {
        $conn->beginTransaction();

        // Check current stock
        $stmt = $conn->prepare("SELECT stock FROM medicine WHERE ndc = ?");
        $stmt->execute([$ndc]);
        $currentStock = $stmt->fetchColumn();

        if ($quantity > $currentStock) {
            throw new Exception('Cannot remove more than current stock');
        }

        // Update stock
        $stmt = $conn->prepare("UPDATE medicine SET stock = stock - ? WHERE ndc = ?");
        $stmt->execute([$quantity, $ndc]);

        // Log removal
        $stmt = $conn->prepare("
            INSERT INTO inventory_log 
            (ndc, action_type, quantity, reason, notes, user_id) 
            VALUES (?, 'remove', ?, ?, ?, ?)
        ");
        $stmt->execute([
            $ndc,
            $quantity,
            $reason,
            $reason === 'other' ? $otherReason : null,
            $_SESSION['user_id']
        ]);

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Medication removed successfully'
        ]);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
?>