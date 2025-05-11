<?php
session_start();

// Check if user is logged in and is a pharmacist
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'pharmacist') {
    header("Location: index.php");
    exit();
}

$userName = $_SESSION['name'] ?? 'Unknown';

// Database connection
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaSys - Patient Management</title>
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
        
        nav ul li a.active {
            color: #56d799;
            border-bottom: 2px solid #56d799;
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
        
        /* Patient Search & Filter */
        .patient-tools {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .search-filters {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .search-box {
            flex: 1;
            position: relative;
        }
        
        .search-box input {
            width: 100%;
            padding: 12px 15px 12px 40px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .search-box::before {
            content: '🔍';
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }
        
        .filter-group {
            display: flex;
            gap: 10px;
        }
        
        .filter-select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            font-size: 14px;
            min-width: 150px;
        }
        
        .patient-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .view-options {
            display: flex;
            gap: 10px;
        }
        
        .view-btn {
            background-color: #f0f7ff;
            border: none;
            color: #0a4275;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }
        
        .view-btn.active {
            background-color: #0a4275;
            color: white;
        }
        
        .export-btn {
            background-color: #f0f7ff;
            border: none;
            color: #0a4275;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        
        .export-btn i {
            margin-right: 5px;
        }
        
        /* Patient List */
        .patient-list {
            margin-bottom: 30px;
        }
        
        .patient-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .patient-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        
        .patient-header {
            padding: 15px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #eee;
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
        
        .patient-title {
            flex: 1;
        }
        
        .patient-name {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 3px;
        }
        
        .patient-id {
            font-size: 14px;
            color: #666;
        }
        
        .patient-body {
            padding: 15px;
        }
        
        .patient-info-group {
            margin-bottom: 15px;
        }
        
        .patient-info-group:last-child {
            margin-bottom: 0;
        }
        
        .info-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 14px;
            color: #333;
        }
        
        .patient-footer {
            padding: 15px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }
        
        .card-btn {
            padding: 8px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }
        
        .btn-primary {
            background-color: #0a4275;
            color: white;
        }
        
        .btn-secondary {
            background-color: #f0f7ff;
            color: #0a4275;
        }
        
        /* Patient Detail View */
        .patient-detail {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .patient-detail-header {
            padding: 20px;
            display: flex;
            border-bottom: 1px solid #eee;
        }
        
        .patient-detail-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #e3f2fd;
            color: #0a4275;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 30px;
            margin-right: 20px;
        }
        
        .patient-detail-title {
            flex: 1;
        }
        
        .patient-detail-name {
            font-size: 24px;
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .patient-detail-subtitle {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .patient-tags {
            display: flex;
            gap: 10px;
        }
        
        .patient-tag {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .tag-insurance {
            background-color: #e3f2fd;
            color: #2196f3;
        }
        
        .tag-allergy {
            background-color: #ffebee;
            color: #f44336;
        }
        
        .tag-chronic {
            background-color: #e8f5e9;
            color: #4caf50;
        }
        
        .patient-detail-actions {
            display: flex;
            gap: 10px;
        }
        
        .detail-btn {
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
        }
        
        .detail-btn-primary {
            background-color: #0a4275;
            color: white;
        }
        
        .detail-btn-secondary {
            background-color: #f0f7ff;
            color: #0a4275;
        }
        
        .detail-btn-danger {
            background-color: #ffebee;
            color: #f44336;
        }
        
        /* Patient Info Tabs */
        .patient-tabs {
            display: flex;
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
        }
        
        .patient-tab {
            padding: 15px 20px;
            font-size: 14px;
            font-weight: 500;
            color: #666;
            cursor: pointer;
            border-bottom: 2px solid transparent;
        }
        
        .patient-tab.active {
            color: #0a4275;
            border-bottom-color: #0a4275;
        }
        
        .patient-tab-content {
            padding: 20px;
        }
        
        /* Patient Profile */
        .profile-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .profile-section {
            margin-bottom: 20px;
        }
        
        .profile-section h3 {
            font-size: 16px;
            color: #0a4275;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        
        .profile-field {
            margin-bottom: 15px;
        }
        
        .profile-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 5px;
        }
        
        .profile-value {
            font-size: 14px;
        }
        
        /* Medication History */
        .med-history-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .med-history-table th {
            text-align: left;
            padding: 12px;
            background-color: #f8f9fa;
            color: #666;
            font-weight: 500;
        }
        
        .med-history-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        
        .med-history-table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .medication-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
        }
        
        .status-active {
            background-color: #e8f5e9;
            color: #4caf50;
        }
        
        .status-completed {
            background-color: #eeeeee;
            color: #757575;
        }
        
        .status-discontinued {
            background-color: #ffebee;
            color: #f44336;
        }
        
        /* Notes & Interactions */
        .notes-list {
            list-style: none;
        }
        
        .note-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .note-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .note-author {
            font-weight: 500;
        }
        
        .note-date {
            color: #666;
            font-size: 14px;
        }
        
        .note-content {
            font-size: 14px;
            line-height: 1.6;
        }
        
        .add-note-form {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        
        .note-textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            margin-bottom: 15px;
            resize: vertical;
            min-height: 100px;
        }
        
        .note-submit {
            background-color: #0a4275;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        
        .pagination-item {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 5px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 14px;
            cursor: pointer;
        }
        
        .pagination-item.active {
            background-color: #0a4275;
            color: white;
            border-color: #0a4275;
        }
        
        /* Responsive Styles */
        @media (max-width: 1200px) {
            .profile-grid {
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
            
            .patient-grid {
                grid-template-columns: 1fr;
            }
            
            .search-filters {
                flex-direction: column;
            }
            
            .filter-group {
                width: 100%;
                justify-content: space-between;
            }
            
            .patient-detail-header {
                flex-direction: column;
            }
            
            .patient-detail-avatar {
                margin-bottom: 15px;
            }
        }
        
        @media (max-width: 768px) {
            nav ul {
                display: none;
            }
            
            .patient-tabs {
                overflow-x: auto;
                white-space: nowrap;
            }
            
            .patient-detail-actions {
                flex-wrap: wrap;
                gap: 5px;
            }
            
            .detail-btn {
                width: 100%;
                margin-bottom: 5px;
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
                    <li><a href="patient.php" class="active">Patients</a></li>
                    <li><a href="inventory.php">Inventory</a></li>
                    <li><a href="report.php">Reports</a></li>
                </ul>
            </nav>
            <div class="user-menu">
                <div><?php echo htmlspecialchars($userName); ?></div>
                <div class="user-avatar">
                    <?php
                        $nameParts = explode(" ", $userName);
                        $initials = strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1));
                        echo htmlspecialchars($initials);
                    ?>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <ul class="sidebar-menu">
                <li><a href="pharmacistHomepage.php"><i>📊</i> <span>Dashboard</span></a></li>
                <li><a href="patient.php" class="active"><i>👥</i> <span>Patient Records</span></a></li>
                <li><a href="inventory.php"><i>💊</i> <span>Medication Inventory</span></a></li>
                <li><a href="report.php"><i>📊</i> <span>Reports</span></a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="page-title">
                <h1>Patient Management</h1>
                <button><i>➕</i> Add New Patient</button>
            </div>
            
            <!-- Patient Search & Filter -->
            <div class="patient-tools">
                <div class="search-filters">
                    <div class="search-box">
                        <input type="text" placeholder="Search patients by name, ID, or phone...">
                    </div>
                    <div class="filter-group">
                        <select class="filter-select">
                            <option>All Insurance</option>
                            <option>Medicare</option>
                            <option>Blue Cross</option>
                            <option>UnitedHealth</option>
                            <option>Aetna</option>
                            <option>Self-Pay</option>
                        </select>
                        <select class="filter-select">
                            <option>All Statuses</option>
                            <option>Active</option>
                            <option>Inactive</option>
                            <option>New Patient</option>
                        </select>
                    </div>
                </div>
                <div class="patient-actions">
                    <div class="view-options">
                        <button class="view-btn active">Card View</button>
                        <button class="view-btn">List View</button>
                    </div>
                    <button class="export-btn"><i>📥</i> Export</button>
                </div>
            </div>
            
            <!-- Patient List (Card View) -->
            <div class="patient-list">
                <div class="patient-grid">
                    <!-- Patient Card 1 -->
                    <div class="patient-card">
                        <div class="patient-header">
                            <div class="patient-avatar">RW</div>
                            <div class="patient-title">
                                <div class="patient-name">Robert Williams</div>
                                <div class="patient-id">ID: PT-3482</div>
                            </div>
                        </div>
                        <div class="patient-body">
                            <div class="patient-info-group">
                                <div class="info-label">Contact</div>
                                <div class="info-value">(555) 123-4567</div>
                                <div class="info-value">rwilliams@email.com</div>
                            </div>
                            <div class="patient-info-group">
                                <div class="info-label">Demographics</div>
                                <div class="info-value">Male, 48 years (05/12/1975)</div>
                                <div class="info-value">123 Main St, Anytown, USA</div>
                            </div>
                            <div class="patient-info-group">
                                <div class="info-label">Insurance</div>
                                <div class="info-value">Blue Cross Blue Shield</div>
                                <div class="info-value">Policy #: BCBS-1234567</div>
                            </div>
                        </div>
                        <div class="patient-footer">
                            <a href="#" class="card-btn btn-primary">View Profile</a>
                            <a href="#" class="card-btn btn-secondary">Prescriptions</a>
                        </div>
                    </div>
                    <!-- Patient Card 2 -->
                    <div class="patient-card">
                        <div class="patient-header">
                            <div class="patient-avatar">MJ</div>
                            <div class="patient-title">
                                <div class="patient-name">Maria Johnson</div>
                                <div class="patient-id">ID: PT-3476</div>
                            </div>
                        </div>
                        <div class="patient-body">
                            <div class="patient-info-group">
                                <div class="info-label">Contact</div>
                                <div class="info-value">(555) 234-5678</div>
                                <div class="info-value">mjohnson@email.com</div>
                            </div>
                            <div class="patient-info-group">
                                <div class="info-label">Demographics</div>
                                <div class="info-value">Female, 42 years (11/23/1982)</div>
                                <div class="info-value">456 Oak Ave, Anytown, USA</div>
                            </div>
                            <div class="patient-info-group">
                                <div class="info-label">Insurance</div>
                                <div class="info-value">Medicare</div>
                                <div class="info-value">Policy #: MED-7654321</div>
                            </div>
                        </div>
                        <div class="patient-footer">
                            <a href="#" class="card-btn btn-primary">View Profile</a>
                            <a href="#" class="card-btn btn-secondary">Prescriptions</a>
                        </div>
                    </div>
                    <!-- Patient Card 3 -->
                    <div class="patient-card">
                        <div class="patient-header">
                            <div class="patient-avatar">DS</div>
                            <div class="patient-title">
                                <div class="patient-name">David Smith</div>
                                <div class="patient-id">ID: PT-3471</div>
                            </div>
                        </div>
                        <div class="patient-body">
                            <div class="patient-info-group">
                                <div class="info-label">Contact</div>
                                <div class="info-value">(555) 345-6789</div>
                                <div class="info-value">dsmith@email.com</div>
                            </div>
                            <div class="patient-info-group">
                                <div class="info-label">Demographics</div>
                                <div class="info-value">Male, 57 years (02/15/1968)</div>
                                <div class="info-value">789 Pine St, Anytown, USA</div>
                            </div>
                            <div class="patient-info-group">
                                <div class="info-label">Insurance</div>
                                <div class="info-value">UnitedHealth</div>
                                <div class="info-value">Policy #: UH-9876543</div>
                            </div>
                        </div>
                        <div class="patient-footer">
                            <a href="#" class="card-btn btn-primary">View Profile</a>
                            <a href="#" class="card-btn btn-secondary">Prescriptions</a>
                        </div>
                    </div>
                    <!-- Patient Card 4 -->
                    <div class="patient-card">
                        <div class="patient-header">
                            <div class="patient-avatar">JL</div>
                            <div class="patient-title">
                                <div class="patient-name">Jennifer Lee</div>
                                <div class="patient-id">ID: PT-3469</div>
                            </div>
                        </div>
                        <div class="patient-body">
                            <div class="patient-info-group">
                                <div class="info-label">Contact</div>
                                <div class="info-value">(555) 456-7890</div>
                                <div class="info-value">jlee@email.com</div>
                            </div>
                            <div class="patient-info-group">
                                <div class="info-label">Demographics</div>
                                <div class="info-value">Female, 35 years (07/08/1990)</div>
                                <div class="info-value">321 Cedar Dr, Anytown, USA</div>
                            </div>
                            <div class="patient-info-group">
                                <div class="info-label">Insurance</div>
                                <div class="info-value">Aetna</div>
                                <div class="info-value">Policy #: AET-2468135</div>
                            </div>
                        </div>
                        <div class="patient-footer">
                            <a href="#" class="card-btn btn-primary">View Profile</a>
                            <a href="#" class="card-btn btn-secondary">Prescriptions</a>
                        </div>
                    </div>
                </div>
            </div>
            


                

    <script src="js/patient.js"></script>
</body>
</html>