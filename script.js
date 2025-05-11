function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    errorDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px;
        background-color: #ffebee;
        color: #c62828;
        border-left: 3px solid #ff5252;
        border-radius: 4px;
        z-index: 1000;
        animation: slideIn 0.3s ease-out;
    `;
    document.body.appendChild(errorDiv);
    setTimeout(() => errorDiv.remove(), 3000);
}

function showSuccess(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.textContent = message;
    successDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px;
        background-color: #e8f5e9;
        color: #2e7d32;
        border-left: 3px solid #4caf50;
        border-radius: 4px;
        z-index: 1000;
        animation: slideIn 0.3s ease-out;
    `;
    document.body.appendChild(successDiv);
    setTimeout(() => successDiv.remove(), 3000);
}

function checkbox() {
    const pharmacistFields = document.getElementById('pharmacist-fields');
    const clientFields = document.getElementById('client-fields');
    const regPharmacyCheckbox = document.getElementById('reg-pharmacy-checkbox');
console.log("Checkbox function called");
    if (!pharmacistFields || !clientFields || !regPharmacyCheckbox) {
        console.error("Required elements not found");
        return;
    }

    // Toggle fields based on checkbox state
    if (regPharmacyCheckbox.checked) {
        // Show pharmacist fields, hide client fields
        pharmacistFields.style.display = "block";
        clientFields.style.display = "none";
    } else {
        // Show client fields, hide pharmacist fields
        pharmacistFields.style.display = "none";
        clientFields.style.display = "block";
    }
}

// Initialize form state when the page loads
document.addEventListener('DOMContentLoaded', function() {
    const regPharmacyCheckbox = document.getElementById('reg-pharmacy-checkbox');
    if (regPharmacyCheckbox) {
        checkbox(); // Set initial state
    }
});