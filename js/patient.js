document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchPatients');
    searchInput.addEventListener('input', filterPatients);
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