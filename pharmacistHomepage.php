<?php
session_start();

// Check if the user is logged in and is a pharmacist
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'pharmacist') {
    // Redirect to login page if not logged in or not a pharmacist
    header("Location: index.php");
    exit();
}

// Get user information from session
$userName = $_SESSION['name'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaSys - Pharmacist Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            line-height: 1.6;
            color: #333;
            background-color: #f5f7fa;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles */
        header {
            background-color: #0a4275;
            color: white;
            padding: 10px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 20px;
            font-weight: bold;
            display: flex;
            align-items: center;
        }

        .logo span {
            color: #56d799;
            margin-left: 5px;
        }

        nav ul {
            display: flex;
            list-style: none;
        }

        nav ul li {
            margin-left: 20px;
            position: relative;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 10px 0;
            display: block;
        }

        nav ul li a:hover {
            color: #56d799;
        }

        .user-menu {
            display: flex;
            align-items: center;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #56d799;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-left: 10px;
        }

        /* Sidebar */
        .dashboard-container {
            display: flex;
            min-height: calc(100vh - 60px);
        }

        .sidebar {
            width: 250px;
            background-color: #fff;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            padding: 20px 0;
            position: sticky;
            top: 60px;
            height: calc(100vh - 60px);
            overflow-y: auto;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu li a {
            padding: 12px 20px;
            display: block;
            color: #555;
            text-decoration: none;
            border-left: 3px solid transparent;
        }

        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background-color: #f0f7ff;
            color: #0a4275;
            border-left-color: #0a4275;
        }

        .sidebar-menu li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 30px;
        }

        .page-title {
            font-size: 24px;
            margin-bottom: 20px;
            color: #0a4275;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title button {
            background-color: #0a4275;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .page-title button i {
            margin-right: 5px;
        }

        /* Dashboard Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .stat-card h3 {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }

        .stat-card .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #0a4275;
        }

        .stat-card .stat-change {
            font-size: 14px;
            margin-top: 5px;
        }

        .stat-up {
            color: #56d799;
        }

        .stat-down {
            color: #ff5d5d;
        }

        /* Inventory Section */
        .dashboard-section {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 18px;
            color: #0a4275;
        }

        .view-all {
            color: #0a4275;
            text-decoration: none;
            font-size: 14px;
        }

        /* Inventory Section */
        .inventory-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .inventory-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .inventory-item {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }

        .inventory-item:last-child {
            border-bottom: none;
        }

        .inventory-info h4 {
            margin-bottom: 5px;
            font-size: 16px;
        }

        .inventory-info p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }

        .inventory-stock {
            text-align: right;
        }

        .stock-qty {
            font-size: 18px;
            font-weight: bold;
            color: #0a4275;
        }

        .stock-status {
            font-size: 12px;
            padding: 3px 8px;
            border-radius: 10px;
            display: inline-block;
            margin-top: 5px;
        }

        .stock-normal {
            background-color: #e8f5e9;
            color: #4caf50;
        }

        .stock-low {
            background-color: #fff8e1;
            color: #ffc107;
        }

        .stock-critical {
            background-color: #ffebee;
            color: #ff5252;
        }

        /* Patient Records */
        .patient-search {
            margin-bottom: 20px;
        }

        .search-input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .patient-list {
            list-style: none;
        }

        .patient-item {
            background-color: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
        }

        .patient-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #e3f2fd;
            color: #0a4275;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 20px;
            margin-right: 15px;
        }

        .patient-info {
            flex: 1;
        }

        .patient-name {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .patient-details {
            display: flex;
            font-size: 14px;
            color: #666;
        }

        .patient-details div {
            margin-right: 20px;
        }

        .patient-actions {
            display: flex;
        }

        .patient-actions a {
            margin-left: 10px;
            color: #0a4275;
            text-decoration: none;
        }

        /* Reports Section */
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .report-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .report-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .report-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background-color: #e3f2fd;
            color: #0a4275;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-right: 10px;
        }

        .report-title {
            font-size: 16px;
            font-weight: 500;
        }

        .report-description {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .report-actions a {
            display: block;
            text-align: center;
            background-color: #f0f7ff;
            color: #0a4275;
            padding: 8px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .inventory-grid, .reports-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
                overflow: visible;
            }

            .sidebar-menu li a span {
                display: none;
            }

            .sidebar-menu li a i {
                margin-right: 0;
                font-size: 20px;
            }

            .main-content {
                padding: 20px;
            }
        }

        @media (max-width: 768px) {
            nav ul {
                display: none;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container header-content">
            <div class="logo">
                💊 Pharma<span>care</span>
            </div>
            <nav>
                <ul>
                    <li><a href="pharmacistHomepage.php">Dashboard</a></li>
                    <li><a href="patient.php">Patients</a></li>
                    <li><a href="inventory.php">Inventory</a></li>
                    <li><a href="report.php">Reports</a></li>
                </ul>
            </nav>
            <div class="user-menu">
                <div class="user-avatar">
                  <?php
                    // Get the initials from the user's name
                    $nameParts = explode(" ", $userName);
                    $initials = strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1));
                    echo htmlspecialchars($initials);
                  ?>
                </div>
                <a href="handlers/logout.php" style="color: white; text-decoration: none; margin-left: 10px;">Logout</a>
            </div>
        </div>
    </header>

    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <ul class="sidebar-menu">
                <li><a href="pharmacistHomepage.php"><i>📊</i> <span>Dashboard</span></a></li>
                <li><a href="patient.php"><i>👥</i> <span>Patient Records</span></a></li>
                <li><a href="inventory.php"><i>💊</i> <span>Medication Inventory</span></a></li>
                <li><a href="report.php"><i>📊</i> <span>Reports</span></a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="page-title">
                <h1>Pharmacy Dashboard</h1>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Active Patients</h3>
                    <div class="stat-value">237</div>
                    <div class="stat-change stat-up">↑ 5 new this week</div>
                </div>
                <div class="stat-card">
                    <h3>Inventory Status</h3>
                    <div class="stat-value">92%</div>
                    <div class="stat-change">3 items low stock</div>
                </div>
            </div>

            <!-- Inventory Section -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2 class="section-title">Medication Inventory</h2>
                    <a href="#" class="view-all">View All</a>
                </div>
                <div class="inventory-grid">
                    <div class="inventory-card">
                        <h3>Common Medications</h3>
                        <div class="inventory-item">
                            <div class="inventory-info">
                                <h4>Atorvastatin 40mg</h4>
                                <p>Tablets, Lipitor</p>
                            </div>
                            <div class="inventory-stock">
                                <div class="stock-qty">127 units</div>
                                <div class="stock-status stock-normal">Normal</div>
                            </div>
                        </div>
                        <div class="inventory-item">
                            <div class="inventory-info">
                                <h4>Lisinopril 20mg</h4>
                                <p>Tablets, Generic</p>
                            </div>
                            <div class="inventory-stock">
                                <div class="stock-qty">43 units</div>
                                <div class="stock-status stock-low">Low Stock</div>
                            </div>
                        </div>
                        <div class="inventory-item">
                            <div class="inventory-info">
                                <h4>Metformin 500mg</h4>
                                <p>Tablets, Generic</p>
                            </div>
                            <div class="inventory-stock">
                                <div class="stock-qty">215 units</div>
                                <div class="stock-status stock-normal">Normal</div>
                            </div>
                        </div>
                    </div>
                    <div class="inventory-card">
                        <h3>Critical Inventory</h3>
                        <div class="inventory-item">
                            <div class="inventory-info">
                                <h4>Amoxicillin 250mg</h4>
                                <p>Capsules, Generic</p>
                            </div>
                            <div class="inventory-stock">
                                <div class="stock-qty">12 units</div>
                                <div class="stock-status stock-critical">Critical</div>
                            </div>
                        </div>
                        <div class="inventory-item">
                            <div class="inventory-info">
                                <h4>Insulin Glargine</h4>
                                <p>100units/ml, Lantus</p>
                            </div>
                            <div class="inventory-stock">
                                <div class="stock-qty">5 pens</div>
                                <div class="stock-status stock-critical">Critical</div>
                            </div>
                        </div>
                        <div class="inventory-item">
                            <div class="inventory-info">
                                <h4>Albuterol Inhaler</h4>
                                <p>90mcg, ProAir HFA</p>
                            </div>
                            <div class="inventory-stock">
                                <div class="stock-qty">8 units</div>
                                <div class="stock-status stock-low">Low Stock</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patient Records -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2 class="section-title">Patient Records</h2>
                    <a href="#" class="view-all">View All</a>
                </div>
                <div class="patient-search">
                    <input type="text" class="search-input" placeholder="Search patients by name, ID, or phone...">
                </div>
                <ul class="patient-list">
                    <li class="patient-item">
                        <div class="patient-avatar">RW</div>
                        <div class="patient-info">
                            <div class="patient-name">Robert Williams</div>
                            <div class="patient-details">
                                <div>ID: PT-3482</div>
                                <div>DOB: 05/12/1975</div>
                                <div>Phone: (555) 123-4567</div>
                            </div>
                        </div>
                        <div class="patient-actions">
                            <a href="#">View Profile</a>
                            <a href="#">Medication History</a>
                        </div>
                    </li>
                    <li class="patient-item">
                        <div class="patient-avatar">MJ</div>
                        <div class="patient-info">
                            <div class="patient-name">Maria Johnson</div>
                            <div class="patient-details">
                                <div>ID: PT-3476</div>
                                <div>DOB: 11/23/1982</div>
                                <div>Phone: (555) 234-5678</div>
                            </div>
                        </div>
                        <div class="patient-actions">
                            <a href="#">View Profile</a>
                            <a href="#">Medication History</a>
                        </div>
                    </li>
                    <li class="patient-item">
                        <div class="patient-avatar">DS</div>
                        <div class="patient-info">
                            <div class="patient-name">David Smith</div>
                            <div class="patient-details">
                                <div>ID: PT-3471</div>
                                <div>DOB: 02/15/1968</div>
                                <div>Phone: (555) 345-6789</div>
                            </div>
                        </div>
                        <div class="patient-actions">
                            <a href="#">View Profile</a>
                            <a href="#">Medication History</a>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Reports Section -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2 class="section-title">Reports</h2>
                    <a href="#" class="view-all">Create New Report</a>
                </div>
                <div class="reports-grid">
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon">📊</div>
                            <div class="report-title">Dispensing Activity</div>
                        </div>
                        <div class="report-description">
                            View daily, weekly, and monthly dispensing statistics and trends.
                        </div>
                        <div class="report-actions">
                            <a href="#">Generate Report</a>
                        </div>
                    </div>
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon">💊</div>
                            <div class="report-title">Inventory Levels</div>
                        </div>
                        <div class="report-description">
                            Track medication inventory levels, usage rates, and reorder recommendations.
                        </div>
                        <div class="report-actions">
                            <a href="#">Generate Report</a>
                        </div>
                    </div>
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon">💰</div>
                            <div class="report-title">Financial Summary</div>
                        </div>
                        <div class="report-description">
                            Review revenue, insurance claims, and payment processing statistics.
                        </div>
                        <div class="report-actions">
                            <a href="#">Generate Report</a>
                        </div>
                    </div>
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon">👥</div>
                            <div class="report-title">Patient Analytics</div>
                        </div>
                        <div class="report-description">
                            Analyze patient demographics, loyalty patterns, and medication compliance.
                        </div>
                        <div class="report-actions">
                            <a href="#">Generate Report</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>