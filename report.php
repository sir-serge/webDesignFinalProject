<?php
session_start();

// Check if user is logged in and is a pharmacist
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'pharmacist') {
    header("Location: index.php");
    exit();
}

$userName = $_SESSION['name'] ?? 'Unknown';
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaSys - Report Generator</title>
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
        
        /* Report Generator Styles */
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
        
        .dashboard-section {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        /* Report Builder Styles */
        .report-builder {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
        }
        
        .report-options {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
        }
        
        .option-group {
            margin-bottom: 20px;
        }
        
        .option-group h3 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #0a4275;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            font-size: 14px;
        }
        
        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .checkbox-group {
            margin-top: 10px;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }
        
        .checkbox-item input {
            margin-right: 8px;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            border: none;
        }
        
        .btn-primary {
            background-color: #0a4275;
            color: white;
        }
        
        .btn-secondary {
            background-color: #f0f7ff;
            color: #0a4275;
            border: 1px solid #0a4275;
        }
        
        /* Report Preview */
        .report-preview {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .preview-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        
        .preview-title {
            font-size: 18px;
            font-weight: 600;
            color: #0a4275;
            margin-bottom: 5px;
        }
        
        .preview-subtitle {
            font-size: 14px;
            color: #666;
        }
        
        .report-chart {
            background-color: #f9f9f9;
            border-radius: 8px;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            border: 1px dashed #ddd;
        }
        
        .chart-placeholder {
            color: #999;
            text-align: center;
        }
        
        .chart-placeholder i {
            font-size: 48px;
            margin-bottom: 10px;
            display: block;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .data-table th {
            background-color: #f8f9fa;
            padding: 10px;
            text-align: left;
            font-weight: 500;
            color: #666;
            border-bottom: 2px solid #eee;
        }
        
        .data-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .data-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #0a4275;
        }
        
        /* Saved Reports */
        .saved-reports {
            margin-top: 30px;
        }
        
        .report-list {
            list-style: none;
        }
        
        .report-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .report-item:last-child {
            border-bottom: none;
        }
        
        .report-info h4 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .report-info p {
            font-size: 14px;
            color: #666;
        }
        
        .report-actions {
            display: flex;
            gap: 10px;
        }
        
        .report-actions a {
            color: #0a4275;
            text-decoration: none;
        }
        
        /* Report Templates */
        .templates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .template-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: transform 0.2s;
            cursor: pointer;
            border: 1px solid #eee;
        }
        
        .template-card:hover {
            transform: translateY(-5px);
            border-color: #0a4275;
        }
        
        .template-icon {
            width: 60px;
            height: 60px;
            background-color: #f0f7ff;
            color: #0a4275;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .template-title {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 10px;
        }
        
        .template-description {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }
        
        .template-footer {
            font-size: 12px;
            color: #999;
        }
        
        /* Report Scheduling */
        .schedule-form {
            max-width: 600px;
        }
        
        .schedule-options {
            margin-top: 15px;
            display: none;
        }
        
        #enableSchedule:checked ~ .schedule-options {
            display: block;
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .report-builder {
                grid-template-columns: 1fr;
            }
            
            .templates-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
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
            
            .summary-stats {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        @media (max-width: 768px) {
            nav ul {
                display: none;
            }
            
            .summary-stats {
                grid-template-columns: 1fr;
            }
        }
        
        /* Tabs */
        .tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            font-weight: 500;
        }
        
        .tab.active {
            border-bottom-color: #0a4275;
            color: #0a4275;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
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
                    <li><a href="report.php" class="active">Reports</a></li>
                </ul>
            </nav>
            <div class="user-menu">
                <div>Dr. Jane Smith</div>
                <div class="user-avatar">JS</div>
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
                <li><a href="report.php" class="active"><i>📊</i> <span>Reports</span></a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="page-title">
                <h1>Report Generator</h1>
                <button><i>💾</i> Save Report</button>
            </div>
            
            <!-- Tabs Navigation -->
            <div class="tabs">
                <div class="tab active" data-tab="builder">Build Report</div>
                <div class="tab" data-tab="templates">Templates</div>
                <div class="tab" data-tab="saved">Saved Reports</div>
                <div class="tab" data-tab="scheduled">Scheduled Reports</div>
            </div>
            
            <!-- Report Builder Tab -->
            <div class="tab-content active" id="builder">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2 class="section-title">Create Custom Report</h2>
                    </div>
                    
                    <div class="report-builder">
                        <div class="report-options">
                            <div class="option-group">
                                <h3>Report Details</h3>
                                <div class="form-group">
                                    <label for="reportTitle">Report Title</label>
                                    <input type="text" id="reportTitle" class="form-control" placeholder="Enter report title">
                                </div>
                                <div class="form-group">
                                    <label for="reportDescription">Description (Optional)</label>
                                    <textarea id="reportDescription" class="form-control" rows="2" placeholder="Enter description"></textarea>
                                </div>
                            </div>
                            
                            <div class="option-group">
                                <h3>Data Source</h3>
                                <div class="form-group">
                                    <label for="dataSource">Select Data Source</label>
                                    <select id="dataSource" class="form-control">
                                        <option value="">Select a data source</option>
                                        <option value="prescriptions">Prescription Data</option>
                                        <option value="inventory">Inventory Data</option>
                                        <option value="patients">Patient Data</option>
                                        <option value="sales">Sales & Revenue</option>
                                        <option value="insurance">Insurance Claims</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="option-group">
                                <h3>Time Range</h3>
                                <div class="form-group">
                                    <label for="timeRange">Select Time Range</label>
                                    <select id="timeRange" class="form-control">
                                        <option value="today">Today</option>
                                        <option value="yesterday">Yesterday</option>
                                        <option value="last7days">Last 7 Days</option>
                                        <option value="last30days" selected>Last 30 Days</option>
                                        <option value="thisMonth">This Month</option>
                                        <option value="lastMonth">Last Month</option>
                                        <option value="custom">Custom Range</option>
                                    </select>
                                </div>
                                <div class="form-group" id="customDateRange" style="display: none;">
                                    <div style="display: flex; gap: 10px;">
                                        <div style="flex: 1;">
                                            <label for="startDate">Start Date</label>
                                            <input type="date" id="startDate" class="form-control">
                                        </div>
                                        <div style="flex: 1;">
                                            <label for="endDate">End Date</label>
                                            <input type="date" id="endDate" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="option-group">
                                <h3>Filters</h3>
                                <div class="form-group">
                                    <label for="filterType">Filter By</label>
                                    <select id="filterType" class="form-control">
                                        <option value="">No Filter</option>
                                        <option value="medication">Medication</option>
                                        <option value="status">Prescription Status</option>
                                        <option value="physician">Prescribing Physician</option>
                                        <option value="insurance">Insurance Provider</option>
                                    </select>
                                </div>
                                <div class="form-group" id="filterValues" style="display: none;">
                                    <label for="filterValue">Select Values</label>
                                    <select id="filterValue" class="form-control" multiple>
                                        <!-- Values will be populated based on filter type -->
                                    </select>
                                </div>
                            </div>
                            
                            <div class="option-group">
                                <h3>Display Options</h3>
                                <div class="checkbox-group">
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="includeChart" checked>
                                        <label for="includeChart">Include Chart</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="includeTable" checked>
                                        <label for="includeTable">Include Data Table</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="includeSummary" checked>
                                        <label for="includeSummary">Include Summary Statistics</label>
                                    </div>
                                </div>
                                
                                <div class="form-group" id="chartOptions">
                                    <label for="chartType">Chart Type</label>
                                    <select id="chartType" class="form-control">
                                        <option value="bar">Bar Chart</option>
                                        <option value="line">Line Chart</option>
                                        <option value="pie">Pie Chart</option>
                                        <option value="area">Area Chart</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="action-buttons">
                                <button class="btn btn-primary">Generate Report</button>
                                <button class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                        
                        <div class="report-preview">
                            <div class="preview-header">
                                <h3 class="preview-title">Prescription Activity Report</h3>
                                <div class="preview-subtitle">Last 30 Days (Apr 3, 2025 - May 3, 2025)</div>
                            </div>
                            
                            <div class="report-chart">
                                <div class="chart-placeholder">
                                    <i>📊</i>
                                    <div>Prescription Activity Chart Will Appear Here</div>
                                </div>
                            </div>
                            
                            <div class="summary-stats">
                                <div class="stat-card">
                                    <div class="stat-label">Total Prescriptions</div>
                                    <div class="stat-value">1,247</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-label">Avg. Daily Volume</div>
                                    <div class="stat-value">41.6</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-label">Most Common Med</div>
                                    <div class="stat-value">Atorvastatin</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-label">Refill Rate</div>
                                    <div class="stat-value">37%</div>
                                </div>
                            </div>
                            
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>New Rx</th>
                                        <th>Refills</th>
                                        <th>Total Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>May 3, 2025</td>
                                        <td>28</td>
                                        <td>14</td>
                                        <td>42</td>
                                    </tr>
                                    <tr>
                                        <td>May 2, 2025</td>
                                        <td>31</td>
                                        <td>17</td>
                                        <td>48</td>
                                    </tr>
                                    <tr>
                                        <td>May 1, 2025</td>
                                        <td>25</td>
                                        <td>13</td>
                                        <td>38</td>
                                    </tr>
                                    <tr>
                                        <td>Apr 30, 2025</td>
                                        <td>30</td>
                                        <td>15</td>
                                        <td>45</td>
                                    </tr>
                                    <tr>
                                        <td>Apr 29, 2025</td>
                                        <td>27</td>
                                        <td>12</td>
                                        <td>39</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Templates Tab -->
            <div class="tab-content" id="templates">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2 class="section-title">Report Templates</h2>
                    </div>
                    
                    <div class="templates-grid">
                        <div class="template-card">
                            <div class="template-icon">📊</div>
                            <h3 class="template-title">Daily Dispensing Report</h3>
                            <p class="template-description">Summary of all prescriptions dispensed in a day with details on medication types and patient demographics.</p>
                            <div class="template-footer">Last generated: 2 days ago</div>
                        </div>
                        
                        <div class="template-card">
                            <div class="template-icon">💊</div>
                            <h3 class="template-title">Inventory Status Report</h3>
                            <p class="template-description">Current inventory levels with highlighted low stock or expired medications requiring attention.</p>
                            <div class="template-footer">Last generated: Yesterday</div>
                        </div>
                        
                        <div class="template-card">
                            <div class="template-icon">💰</div>
                            <h3 class="template-title">Financial Summary</h3>
                            <p class="template-description">Revenue tracking, insurance claims, and payment processing statistics over a selected period.</p>
                            <div class="template-footer">Last generated: 1 week ago</div>
                        </div>
                        
                        <div class="template-card">
                            <div class="template-icon">👥</div>
                            <h3 class="template-title">Patient Compliance Report</h3>
                            <p class="template-description">Analysis of medication adherence patterns with identified patients requiring follow-up.</p>
                            <div class="template-footer">Last generated: 3 days ago</div>
                        </div>
                        
                        <div class="template-card">
                            <div class="template-icon">🔍</div>
                            <h3 class="template-title">Controlled Substance Audit</h3>
                            <p class="template-description">Detailed tracking of controlled substance prescriptions with regulatory compliance verification.</p>
                            <div class="template-footer">Last generated: 5 days ago</div>
                        </div>
                        
                        <div class="template-card">
                            <div class="template-icon">📅</div>
                            <h3 class="template-title">Monthly Performance Report</h3>
                            <p class="template-description">Comprehensive overview of pharmacy operations, KPIs, and performance metrics for the month.</p>
                            <div class="template-footer">Last generated: Apr 30, 2025</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Saved Reports Tab -->
            <div class="tab-content" id="saved">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2 class="section-title">Saved Reports</h2>
                    </div>
                    
                    <div class="report-list">
                        <div class="report-item">
                            <div class="report-info">
                                <h4>April 2025 Dispensing Activity</h4>
                                <p>Created: Apr 30, 2025 | Type: Prescription Activity</p>
                            </div>
                            <div class="report-actions">
                                <a href="#">View</a>
                                <a href="#">Download</a>
                                <a href="#">Edit</a>
                                <a href="#">Delete</a>
                            </div>
                        </div>
                        
                        <div class="report-item">
                            <div class="report-info">
                                <h4>Q1 2025 Financial Summary</h4>
                                <p>Created: Apr 12, 2025 | Type: Financial Report</p>
                            </div>
                            <div class="report-actions">
                                <a href="#">View</a>
                                <a href="#">Download</a>
                                <a href="#">Edit</a>
                                <a href="#">Delete</a>
                            </div>
                        </div>
                        
                        <div class="report-item">
                            <div class="report-info">
                                <h4>Inventory Audit - April 2025</h4>
                                <p>Created: Apr 28, 2025 | Type: Inventory Report</p>
                            </div>
                            <div class="report-actions">
                                <a href="#">View</a>
                                <a href="#">Download</a>
                                <a href="#">Edit</a>
                                <a href="#">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Scheduled Reports Tab -->
            <div class="tab-content" id="scheduled">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2 class="section-title">Scheduled Reports</h2>
                    </div>
                    
                    <form class="schedule-form">
                        <div class="form-group">
                            <label for="reportName">Report Name</label>
                            <input type="text" id="reportName" class="form-control" placeholder="Enter report name">
                        </div>
                        <div class="form-group">
                            <label for="scheduleFrequency">Frequency</label>
                            <select id="scheduleFrequency" class="form-control">
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="startDateTime">Start Date & Time</label>
                            <input type="datetime-local" id="startDateTime" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="endDateTime">End Date & Time</label>
                            <input type="datetime-local" id="endDateTime" class="form-control">
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="enableSchedule">
                            <label for="enableSchedule">Enable Scheduling</label>
                        </div>
                        
                        <div class="schedule-options">
                            <div class="form-group">
                                <label for="notificationMethod">Notification Method</label>
                                <select id="notificationMethod" class="form-control">
                                    <option value="email">Email</option>
                                    <option value="sms">SMS</option>
                                    <option value="none">None</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="recipients">Recipients</label>
                                <input type="text" id="recipients" class="form-control" placeholder="Enter email or phone number">
                            </div }
                        </div>
                        
                        <div class="action-buttons">
                            <button type="submit" class="btn btn-primary">Schedule Report</button>
                            <button type="button" class="btn btn-secondary">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>