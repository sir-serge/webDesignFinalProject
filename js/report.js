document.addEventListener('DOMContentLoaded', function() {
    // Set default date range (last 30 days)
    const endDate = new Date();
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - 30);
    
    document.getElementById('startDate').value = startDate.toISOString().split('T')[0];
    document.getElementById('endDate').value = endDate.toISOString().split('T')[0];
});

function updateReports() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (!startDate || !endDate) {
        alert('Please select both start and end dates');
        return;
    }
    
    // Refresh all report data
    generateReport('inventory');
}

function generateReport(type) {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    fetch(`handlers/generate_report.php?type=${type}&start=${startDate}&end=${endDate}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayReport(type, data);
            } else {
                alert('Error generating report: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
}

function displayReport(type, data) {
    const preview = document.getElementById('reportPreview');
    const title = document.getElementById('reportTitle');
    const chartContainer = document.getElementById('chartContainer');
    const reportData = document.getElementById('reportData');
    
    preview.style.display = 'block';
    
    switch(type) {
        case 'inventory':
            title.textContent = 'Inventory Report';
            createInventoryChart(data.chartData);
            displayInventoryTable(data.tableData);
            break;
        case 'patients':
            title.textContent = 'Patient Analytics';
            createPatientChart(data.chartData);
            displayPatientStats(data.stats);
            break;
        case 'financial':
            title.textContent = 'Financial Summary';
            createFinancialChart(data.chartData);
            displayFinancialSummary(data.summary);
            break;
    }
}

function createInventoryChart(data) {
    const ctx = document.getElementById('chartContainer').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Stock Levels',
                data: data.values,
                backgroundColor: '#0a4275'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function downloadReport() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    window.location.href = `handlers/download_report.php?start=${startDate}&end=${endDate}`;
}