// Get modal elements
const modal = document.getElementById('addMedicineModal');
const openModalBtn = document.querySelector('.page-title button');
const closeModalBtn = document.querySelector('.close');
const addMedicineForm = document.getElementById('addMedicineForm');

// Open modal
openModalBtn.addEventListener('click', () => {
    modal.style.display = 'block';
});

// Close modal
closeModalBtn.addEventListener('click', () => {
    modal.style.display = 'none';
});

// Close modal when clicking outside
window.addEventListener('click', (e) => {
    if (e.target === modal) {
        modal.style.display = 'none';
    }
});

// Handle form submission
addMedicineForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('handlers/inventory_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${data.type}`;
        alertDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px;
            border-radius: 4px;
            z-index: 1000;
            animation: slideIn 0.3s ease-out;
            ${data.type === 'error' ? 
                'background-color: #ffebee; border-left: 3px solid #ff5252; color: #c62828;' :
                'background-color: #e8f5e9; border-left: 3px solid #4caf50; color: #2e7d32;'
            }
        `;
        
        alertDiv.textContent = data.message;
        document.body.appendChild(alertDiv);

        if (data.success) {
            addMedicineForm.reset();
            modal.style.display = 'none';
            location.reload();
            updateInventorySummary(); // Update summary after successful addition
        }

        setTimeout(() => alertDiv.remove(), 3000);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the medicine.');
    });
});

// Add this function to update inventory summary
function updateInventorySummary() {
    fetch('handlers/get_inventory_summary.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update total medications
                document.querySelector('.summary-card:nth-child(1) .summary-value')
                    .textContent = data.data.total_medications.value;
                document.querySelector('.summary-card:nth-child(1) .summary-details')
                    .textContent = data.data.total_medications.details;

                // Update stock value
                document.querySelector('.summary-card:nth-child(2) .summary-value')
                    .textContent = '$' + data.data.stock_value.value;
                document.querySelector('.summary-card:nth-child(2) .summary-details')
                    .textContent = data.data.stock_value.details;

                // Update low stock items
                document.querySelector('.summary-card:nth-child(3) .summary-value')
                    .textContent = data.data.low_stock.value;
                document.querySelector('.summary-card:nth-child(3) .summary-details')
                    .textContent = data.data.low_stock.details;

                // Update expiring soon
                document.querySelector('.summary-card:nth-child(4) .summary-value')
                    .textContent = data.data.expiring_soon.value;
                document.querySelector('.summary-card:nth-child(4) .summary-details')
                    .textContent = data.data.expiring_soon.details;
            }
        })
        .catch(error => console.error('Error:', error));
}

// Call the function when page loads
document.addEventListener('DOMContentLoaded', updateInventorySummary);

document.querySelectorAll('.row-actions a:first-child').forEach(editBtn => {
    editBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const row = this.closest('tr');
        const isEditing = row.classList.contains('editing');

        if (!isEditing) {
            // Store original values
            row.dataset.original = row.innerHTML;
            
            // Get current values
            const name = row.querySelector('.medication-name').textContent;
            const generic = row.querySelector('.medication-generic').textContent;
            const ndc = row.cells[1].textContent;
            const category = row.cells[2].textContent;
            const stock = parseInt(row.cells[3].textContent);
            const reorder = parseInt(row.cells[4].textContent);
            const price = parseFloat(row.cells[5].textContent.replace('$', ''));

            // Replace cells with input fields
            row.cells[0].innerHTML = `
                <input type="text" class="edit-input" name="name" value="${name}">
                <input type="text" class="edit-input" name="generic" value="${generic}">
            `;
            row.cells[2].innerHTML = `
                <select name="category" class="edit-input">
                    <option ${category === 'Antibiotics' ? 'selected' : ''}>Antibiotics</option>
                    <option ${category === 'Antihypertensives' ? 'selected' : ''}>Antihypertensives</option>
                    <option ${category === 'Analgesics' ? 'selected' : ''}>Analgesics</option>
                    <option ${category === 'Antidiabetics' ? 'selected' : ''}>Antidiabetics</option>
                    <option ${category === 'Statins' ? 'selected' : ''}>Statins</option>
                </select>
            `;
            row.cells[3].innerHTML = `<input type="number" class="edit-input" name="stock" value="${stock}">`;
            row.cells[4].innerHTML = `<input type="number" class="edit-input" name="reorder" value="${reorder}">`;
            row.cells[5].innerHTML = `<input type="number" class="edit-input" name="price" step="0.01" value="${price}">`;

            // Change action buttons
            row.cells[7].innerHTML = `
                <button class="save-btn">Save</button>
                <button class="cancel-btn">Cancel</button>
            `;

            row.classList.add('editing');
        }
    });
});

// Event delegation for save and cancel buttons
document.querySelector('.inventory-table').addEventListener('click', function(e) {
    if (e.target.matches('.save-btn')) {
        const row = e.target.closest('tr');
        const ndc = row.cells[1].textContent;
        const formData = {
            ndc: ndc,
            name: row.querySelector('input[name="name"]').value,
            generic: row.querySelector('input[name="generic"]').value,
            category: row.querySelector('select[name="category"]').value,
            stock: row.querySelector('input[name="stock"]').value,
            reorder: row.querySelector('input[name="reorder"]').value,
            price: row.querySelector('input[name="price"]').value
        };

        // Send update to server
        fetch('handlers/update_medication.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update row with new values
                updateRow(row, formData);
                row.classList.remove('editing');
            } else {
                alert('Error updating medication: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the medication');
        });
    }
    
    if (e.target.matches('.cancel-btn')) {
        const row = e.target.closest('tr');
        row.innerHTML = row.dataset.original;
        row.classList.remove('editing');
    }
});

function updateRow(row, data) {
    row.innerHTML = `
        <td>
            <div class="medication-name">${data.name}</div>
            <div class="medication-generic">${data.generic}</div>
        </td>
        <td>${data.ndc}</td>
        <td>${data.category}</td>
        <td>${data.stock} units</td>
        <td>${data.reorder} units</td>
        <td>$${parseFloat(data.price).toFixed(2)}</td>
        <td><span class="stock-badge ${getStockStatus(data.stock, data.reorder)}">${getStockLabel(data.stock, data.reorder)}</span></td>
        <td class="row-actions">
            <a href="#">Edit</a>
            <a href="#">Restock</a>
        </td>
    `;
}

function getStockStatus(stock, reorder) {
    if (stock <= reorder * 0.25) return 'stock-critical';
    if (stock <= reorder * 0.5) return 'stock-low';
    if (stock >= reorder * 2) return 'stock-overstock';
    return 'stock-normal';
}

function getStockLabel(stock, reorder) {
    if (stock <= reorder * 0.25) return 'Critical';
    if (stock <= reorder * 0.5) return 'Low Stock';
    if (stock >= reorder * 2) return 'Overstock';
    return 'Normal';
}

document.addEventListener('DOMContentLoaded', function() {
    // Get modal elements
    const removeMedicationBtn = document.getElementById('removeMedicationBtn');
    const removeMedicineModal = document.getElementById('removeMedicineModal');
    const removeReasonSelect = document.getElementById('removeReason');
    const otherReasonGroup = document.getElementById('otherReasonGroup');
    
    // Show modal when clicking remove button
    removeMedicationBtn.onclick = function() {
        removeMedicineModal.style.display = "block";
        loadMedicationList();
    }

    // Close modal when clicking X
    removeMedicineModal.querySelector('.close').onclick = function() {
        removeMedicineModal.style.display = "none";
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target == removeMedicineModal) {
            removeMedicineModal.style.display = "none";
        }
    }

    // Show/hide other reason input
    removeReasonSelect.onchange = function() {
        otherReasonGroup.style.display = 
            this.value === 'other' ? 'block' : 'none';
    }

    // Load medications into select dropdown
    function loadMedicationList() {
        const select = document.getElementById('removeMedication');
        select.innerHTML = '<option value="">Choose medication to remove</option>';
        
        // Get all medications from the table
        document.querySelectorAll('.inventory-table tbody tr').forEach(row => {
            const name = row.querySelector('.medication-name').textContent;
            const ndc = row.querySelector('td:nth-child(2)').textContent;
            const stock = parseInt(row.querySelector('td:nth-child(4)').textContent);
            
            select.innerHTML += `
                <option value="${ndc}" data-stock="${stock}">
                    ${name} (Stock: ${stock} units)
                </option>
            `;
        });
    }

    // Handle form submission
    document.getElementById('removeMedicineForm').onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('handlers/remove_medication.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                removeMedicineModal.style.display = "none";
                location.reload(); // Refresh page to show updated inventory
            } else {
                const alertDiv = document.querySelector('.form-alert');
                alertDiv.textContent = data.message;
                alertDiv.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while removing the medication');
        });
    }

    document.getElementById('removeMedication').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const currentStock = selectedOption.dataset.stock;
        const removeQuantityInput = document.getElementById('removeQuantity');
        removeQuantityInput.max = currentStock;
    });

    document.getElementById('removeAllBtn').addEventListener('click', function() {
        const selectedMedication = document.getElementById('removeMedication');
        const selectedOption = selectedMedication.options[selectedMedication.selectedIndex];
        const currentStock = selectedOption.dataset.stock;
        
        if (selectedMedication.value) {
            document.getElementById('removeQuantity').value = currentStock;
        } else {
            alert('Please select a medication first');
        }
    });

    // Handle add medication form submission
    document.getElementById('addMedicineForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('handlers/add_medication.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add new row to table
                const tableBody = document.querySelector('.inventory-table tbody');
                const newRow = `
                    <tr>
                        <td>
                            <div class="medication-name">${formData.get('medication')}</div>
                            <div class="medication-generic">${formData.get('category')}</div>
                        </td>
                        <td>${formData.get('ndc')}</td>
                        <td>${formData.get('category')}</td>
                        <td>${formData.get('stock')} units</td>
                        <td>${formData.get('reorderPoint')} units</td>
                        <td>$${parseFloat(formData.get('unitPrice')).toFixed(2)}</td>
                        <td><span class="stock-badge ${getStockStatusClass(formData.get('stock'), formData.get('reorderPoint'))}">${getStockLabel(formData.get('stock'), formData.get('reorderPoint'))}</span></td>
                        <td class="row-actions">
                            <a href="#">Edit</a>
                            <a href="#">Restock</a>
                        </td>
                    </tr>
                `;
                tableBody.insertAdjacentHTML('afterbegin', newRow);
                
                // Close modal and reset form
                document.getElementById('addMedicineModal').style.display = 'none';
                this.reset();
                
                // Update summary cards
                updateInventorySummary();
            } else {
                alert('Error adding medication: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding the medication');
        });
    });

    function getStockStatusClass(stock, reorder) {
        stock = parseInt(stock);
        reorder = parseInt(reorder);
        if (stock <= reorder * 0.25) return 'stock-critical';
        if (stock <= reorder * 0.5) return 'stock-low';
        if (stock >= reorder * 2) return 'stock-overstock';
        return 'stock-normal';
    }

    function updateInventorySummary() {
        fetch('handlers/get_inventory_summary.php')
            .then(response => response.json())
            .then(data => {
                document.querySelector('.summary-card:nth-child(1) .summary-value').textContent = data.totalMedications;
                document.querySelector('.summary-card:nth-child(2) .summary-value').textContent = `$${data.stockValue}`;
                document.querySelector('.summary-card:nth-child(3) .summary-value').textContent = data.lowStockItems;
                document.querySelector('.summary-card:nth-child(4) .summary-value').textContent = data.expiringItems;
            });
    }

    loadMedications();
});

function loadMedications() {
    fetch('handlers/get_medications.php')
        .then(response => response.json())
        .then(medications => {
            const tableBody = document.querySelector('.inventory-table tbody');
            tableBody.innerHTML = ''; // Clear existing rows

            medications.forEach(med => {
                const stockStatus = getStockStatus(med.stock, med.reorder_point);
                const row = `
                    <tr>
                        <td>
                            <div class="medication-name">${med.medication_name}</div>
                            <div class="medication-generic">${med.generic_name || ''}</div>
                        </td>
                        <td>${med.ndc}</td>
                        <td>${med.category}</td>
                        <td>${med.stock} units</td>
                        <td>${med.reorder_point} units</td>
                        <td>$${parseFloat(med.unit_price).toFixed(2)}</td>
                        <td><span class="stock-badge ${stockStatus.class}">${stockStatus.label}</span></td>
                        <td class="row-actions">
                            <a href="#" onclick="editMedication('${med.ndc}')">Edit</a>
                            <a href="#" onclick="restockMedication('${med.ndc}')">Restock</a>
                        </td>
                    </tr>
                `;
                tableBody.insertAdjacentHTML('beforeend', row);
            });
        })
        .catch(error => {
            console.error('Error loading medications:', error);
            const tableBody = document.querySelector('.inventory-table tbody');
            tableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center">
                        Error loading medications. Please try again later.
                    </td>
                </tr>
            `;
        });
}

function getStockStatus(stock, reorderPoint) {
    stock = parseInt(stock);
    reorderPoint = parseInt(reorderPoint);
    
    if (stock <= reorderPoint * 0.25) {
        return { class: 'stock-critical', label: 'Critical' };
    }
    if (stock <= reorderPoint * 0.5) {
        return { class: 'stock-low', label: 'Low Stock' };
    }
    if (stock >= reorderPoint * 2) {
        return { class: 'stock-overstock', label: 'Overstock' };
    }
    return { class: 'stock-normal', label: 'Normal' };
}

document.addEventListener('DOMContentLoaded', function() {
    // Load medications when page loads
    loadMedications();

    function loadMedications() {
        fetch('handlers/get_medications.php')
            .then(response => response.json())
            .then(data => {
                if (data.success === false) {
                    console.error(data.message);
                    return;
                }

                const tableBody = document.querySelector('.inventory-table tbody');
                tableBody.innerHTML = ''; // Clear existing rows

                data.forEach(med => {
                    const stockStatus = getStockStatus(med.stock);
                    const row = `
                        <tr>
                            <td>
                                <div class="medication-name">${med.medication_name}</div>
                                <div class="medication-generic">${med.category}</div>
                            </td>
                            <td>${med.ndc}</td>
                            <td>${med.category}</td>
                            <td>${med.stock} units</td>
                            <td>${med.reorder_point} units</td>
                            <td>$${parseFloat(med.unit_price).toFixed(2)}</td>
                            <td><span class="stock-badge ${stockStatus.class}">${stockStatus.label}</span></td>
                            <td class="row-actions">
                                <a href="#" onclick="editMedication('${med.ndc}')">Edit</a>
                                <a href="#" onclick="restockMedication('${med.ndc}')">Restock</a>
                            </td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });

                // Update summary cards
                updateInventorySummary(data);
            })
            .catch(error => {
                console.error('Error loading medications:', error);
            });
    }

    function getStockStatus(stock) {
        stock = parseInt(stock);
        if (stock <= 10) {
            return { class: 'stock-critical', label: 'Critical' };
        } else if (stock <= 20) {
            return { class: 'stock-low', label: 'Low Stock' };
        } else if (stock >= 100) {
            return { class: 'stock-overstock', label: 'Overstock' };
        } else {
            return { class: 'stock-normal', label: 'Normal' };
        }
    }

    function updateInventorySummary(medications) {
        const totalMeds = medications.length;
        const stockValue = medications.reduce((total, med) => 
            total + (med.stock * med.unit_price), 0);
        const lowStock = medications.filter(med => med.stock <= 20).length;
        
        // Update summary cards
        document.querySelector('.summary-card:nth-child(1) .summary-value').textContent = totalMeds;
        document.querySelector('.summary-card:nth-child(2) .summary-value').textContent = 
            `$${stockValue.toFixed(2)}`;
        document.querySelector('.summary-card:nth-child(3) .summary-value').textContent = lowStock;
    }

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', filterInventory);
    document.getElementById('categoryFilter').addEventListener('change', filterInventory);
    document.getElementById('stockFilter').addEventListener('change', filterInventory);
});

function filterInventory() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const categoryFilter = document.getElementById('categoryFilter').value;
    const stockFilter = document.getElementById('stockFilter').value;
    
    const items = document.querySelectorAll('.inventory-card');
    
    items.forEach(item => {
        const name = item.querySelector('h3').textContent.toLowerCase();
        const category = item.querySelector('p').textContent.toLowerCase();
        const stockElement = item.querySelector('span');
        const stockClass = stockElement.className;
        
        let showItem = true;
        
        // Apply search filter
        if (searchTerm && !name.includes(searchTerm)) {
            showItem = false;
        }
        
        // Apply category filter
        if (categoryFilter && !category.includes(categoryFilter)) {
            showItem = false;
        }
        
        // Apply stock filter
        if (stockFilter && !stockClass.includes(stockFilter)) {
            showItem = false;
        }
        
        item.style.display = showItem ? 'block' : 'none';
    });
}

function openAddItemModal() {
    document.getElementById('itemForm').reset();
    document.getElementById('itemId').value = '';
    document.getElementById('itemModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('itemModal').style.display = 'none';
}

function editItem(itemId) {
    // Fetch item details and populate form
    fetch(`handlers/get_item.php?id=${itemId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('itemId').value = data.id;
            document.getElementById('itemName').value = data.name;
            document.getElementById('itemCategory').value = data.category;
            document.getElementById('itemQuantity').value = data.quantity;
            document.getElementById('itemPrice').value = data.price;
            document.getElementById('itemModal').style.display = 'block';
        })
        .catch(error => console.error('Error:', error));
}

function updateStock(itemId) {
    const quantity = prompt('Enter new stock quantity:');
    if (quantity === null || quantity === '') return;
    
    fetch('handlers/update_stock.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${itemId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating stock: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

// Form submission handler
document.getElementById('itemForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('handlers/save_item.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal();
            location.reload();
        } else {
            alert('Error saving item: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
});