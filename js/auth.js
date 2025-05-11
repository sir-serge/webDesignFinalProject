document.addEventListener('DOMContentLoaded', function() {
    // Form toggle functionality
    const showRegisterLink = document.getElementById('show-register');
    const showLoginLink = document.getElementById('show-login');
    const loginForm = document.getElementById('loginForm');
    const loginDiv = document.getElementById('login');
    const registerDiv = document.getElementById('register');

    // Add checkbox handling for registration form
    const regPharmacyCheckbox = document.getElementById('reg-pharmacy-checkbox');
    if (regPharmacyCheckbox) {
        regPharmacyCheckbox.addEventListener('change', function() {
            toggleFormFields(this.checked);
        });

        // Set initial state
        toggleFormFields(regPharmacyCheckbox.checked);
    }

    // Form toggle event listeners
    showRegisterLink.addEventListener('click', function(e) {
        e.preventDefault();
        loginDiv.classList.add('hidden');
        registerDiv.classList.add('active');
    });

    showLoginLink.addEventListener('click', function(e) {
        e.preventDefault();
        loginDiv.classList.remove('hidden');
        registerDiv.classList.remove('active');
    });

    console.log('Login form found:', !!loginForm); // Debug log

    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted');

            removeExistingMessages();
            const formData = new FormData(this);
            const isPharmacist = document.getElementById('pharmacy-checkbox').checked;
            formData.append('isPharmacist', isPharmacist ? 'true' : 'false');

            fetch('handlers/login_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    if (isPharmacist) {
                        window.location.href = 'pharmacistHomepage.php';
                    } else {
                        window.location.href = 'clientHomepage.php';
                    }
                } else {
                    showMessage('error-message', 'Login Failed', true);
                    let detailMessage = data.message || 'Invalid credentials';
                    showMessage('error-detail', detailMessage, false);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showMessage('error-message', 'Login Failed', true);
                showMessage('error-detail', 'Server connection failed. Please try again.', false);
            });
        });
    }

    const registrationForm = document.getElementById('registrationForm');
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Registration form submitted');

            const formData = new FormData(this);
            const isPharmacist = document.getElementById('reg-pharmacy-checkbox').checked;
            formData.append('isPharmacist', isPharmacist ? 'true' : 'false');

            // Log form data before sending
            console.log('Form Data:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            fetch('handlers/register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Server response:', data);
                if (data.success) {
                    showMessage('success-message', 'Registration successful!', true);
                    showMessage('success-detail',
                        `You have been registered as a ${data.userType}. You can now login.`,
                        false
                    );

                    // Redirect to login after 2 seconds
                    setTimeout(() => {
                        window.location.href = 'index.html#login';
                        location.reload();
                    }, 2000);
                } else {
                    let errorMessage = 'Registration failed';
                    let detailMessage = data.message;

                    switch (data.errorType) {
                        case 'connection':
                            errorMessage = 'Database Connection Failed';
                            break;
                        case 'database':
                            errorMessage = 'Database Error';
                            break;
                        case 'validation':
                            errorMessage = 'Validation Error';
                            break;
                        case 'method':
                            errorMessage = 'Invalid Request';
                            break;
                        default:
                            errorMessage = 'Registration failed';
                            detailMessage = data.message || 'An unknown error occurred.';
                            break;
                    }

                    showMessage('error-message', errorMessage, true);
                    showMessage('error-detail', detailMessage, false);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showMessage('error-message', 'Registration Failed', true);
                showMessage('error-detail', 'Server connection error. Please try again.', false);
            });
        });
    }

    function showMessage(className, message, isMain) {
        const messageDiv = document.createElement('div');
        messageDiv.className = className;
        messageDiv.style.cssText = `
            position: fixed;
            right: 20px;
            padding: 15px;
            background-color: ${isMain ? '#ffebee' : '#fff3e0'};
            color: ${isMain ? '#c62828' : '#e65100'};
            border-left: 3px solid ${isMain ? '#ff5252' : '#ff9100'};
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
            animation: slideIn 0.3s ease-out;
            width: 300px;
        `;
        messageDiv.style.top = isMain ? '20px' : '90px';
        messageDiv.textContent = message;
        document.body.appendChild(messageDiv);

        setTimeout(() => {
            messageDiv.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => messageDiv.remove(), 300);
        }, 3000);
    }

    function removeExistingMessages() {
        document.querySelectorAll('.error-message, .error-detail').forEach(el => el.remove());
    }
});

// Add the checkbox function
function toggleFormFields(isPharmacist) {
    const pharmacistFields = document.getElementById('pharmacist-fields');
    const clientFields = document.getElementById('client-fields');
    
    if (isPharmacist) {
        pharmacistFields.style.display = "block";
        clientFields.style.display = "none";
        // Disable client fields when hidden
        clientFields.querySelectorAll('input').forEach(input => {
            input.disabled = true;
        });
        // Enable pharmacist fields when shown
        pharmacistFields.querySelectorAll('input').forEach(input => {
            input.disabled = false;
        });
    } else {
        pharmacistFields.style.display = "none";
        clientFields.style.display = "block";
        // Enable client fields when shown
        clientFields.querySelectorAll('input').forEach(input => {
            input.disabled = false;
        });
        // Disable pharmacist fields when hidden
        pharmacistFields.querySelectorAll('input').forEach(input => {
            input.disabled = true;
        });
    }
}