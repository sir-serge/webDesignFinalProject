<?php
require_once '../config/db.php';

try {
    // Get total medications count
    $totalQuery = $conn->query("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN stock > 0 THEN 1 ELSE 0 END) as active,
        SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as inactive
        FROM medicine");
    $totalData = $totalQuery->fetch(PDO::FETCH_ASSOC);

    // Calculate total stock value
    $valueQuery = $conn->query("SELECT 
        SUM(stock * unit_price) as current_value 
        FROM medicine");
    $valueData = $valueQuery->fetch(PDO::FETCH_ASSOC);

    // Get low stock and critical items count
    $stockQuery = $conn->query("SELECT 
        SUM(CASE WHEN stock <= 20 AND stock > 5 THEN 1 ELSE 0 END) as low_stock,
        SUM(CASE WHEN stock <= 5 THEN 1 ELSE 0 END) as critical
        FROM medicine");
    $stockData = $stockQuery->fetch(PDO::FETCH_ASSOC);

    // Get items expiring in next 30 days
    $today = date('Y-m-d');
    $thirtyDays = date('Y-m-d', strtotime('+30 days'));
    $expiryQuery = $conn->prepare("SELECT COUNT(*) as expiring_soon 
        FROM medicine 
        WHERE expiry_date BETWEEN ? AND ?");
    $expiryQuery->execute([$today, $thirtyDays]);
    $expiryData = $expiryQuery->fetch(PDO::FETCH_ASSOC);

    // Prepare response
    $response = [
        'success' => true,
        'data' => [
            'total_medications' => [
                'value' => $totalData['total'],
                'details' => $totalData['active'] . ' active, ' . $totalData['inactive'] . ' inactive'
            ],
            'stock_value' => [
                'value' => number_format($valueData['current_value'], 2),
                'details' => 'Current inventory value'
            ],
            'low_stock' => [
                'value' => $stockData['low_stock'] + $stockData['critical'],
                'details' => $stockData['critical'] . ' critical, ' . $stockData['low_stock'] . ' warning'
            ],
            'expiring_soon' => [
                'value' => $expiryData['expiring_soon'],
                'details' => 'Within next 30 days'
            ]
        ]
    ];

    header('Content-Type: application/json');
    echo json_encode($response);

} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>