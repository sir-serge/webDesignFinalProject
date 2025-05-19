document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchPatients');
    searchInput.addEventListener('input', filterPatients);
});

// Modal functionality
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('addPatientModal');
    const addButton = document.querySelector('.page-title button');
    const closeButtons = document.querySelectorAll('.close, .close-modal');

    // Show modal
    addButton.addEventListener('click', function() {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    });

    // Close modal
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        });
    });

    // Close when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });

    // Form submission
    document.getElementById('addPatientForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Adding...';

        try {
            const formData = new FormData(this);
            const response = await fetch('handlers/add_patient.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            if (result.success) {
                alert('Patient added successfully!');
                location.reload();
            } else {
                alert(result.message || 'Failed to add patient');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while adding the patient');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Add Patient';
        }
    });
});

function filterPatients() {
    const searchTerm = document.getElementById('searchPatients').value.toLowerCase();
    const patientCards = document.querySelectorAll('.patient-card');

    patientCards.forEach(card => {
        const patientInfo = card.querySelector('.patient-info').textContent.toLowerCase();
        card.style.display = patientInfo.includes(searchTerm) ? 'block' : 'none';
    });
}

function openAddPatientModal() {
    document.getElementById('patientForm').reset();
    document.getElementById('patientId').value = '';
    document.getElementById('patientModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('patientModal').style.display = 'none';
}

function editPatient(patientId) {
    fetch(`handlers/get_patient.php?id=${patientId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('patientId').value = data.id;
            document.getElementById('firstName').value = data.first_name;
            document.getElementById('lastName').value = data.last_name;
            document.getElementById('phone').value = data.phone;
            document.getElementById('email').value = data.email;
            document.getElementById('address').value = data.address;
            document.getElementById('insuranceProvider').value = data.insurance_provider;
            document.getElementById('insuranceNumber').value = data.insurance_number;
            document.getElementById('patientModal').style.display = 'block';
        })
        .catch(error => console.error('Error:', error));
}

document.getElementById('patientForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('handlers/save_patient.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal();
            location.reload();
        } else {
            alert('Error saving patient: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
});