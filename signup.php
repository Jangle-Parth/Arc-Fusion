<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Fashion Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
    :root {
        --primary-purple: #7B6CF6;
        --light-purple: #F3F1FF;
        --dark-purple: #4A3F9F;
        --accent-purple: #9D8DF7;
        --text-dark: #2D2A45;
        --text-light: #6E6B85;
        --white: #FFFFFF;
        --gradient-purple: linear-gradient(135deg, #7B6CF6 0%, #9D8DF7 100%);
        --shadow-sm: 0 2px 4px rgba(123, 108, 246, 0.1);
        --shadow-md: 0 4px 6px rgba(123, 108, 246, 0.15);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f5f5f5;
        padding: 20px;
    }

    .signup-container {
        display: flex;
        max-width: 1000px;
        width: 100%;
        background: var(--white);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .image-section {
        flex: 1;
        background: var(--gradient-purple);
        padding: 40px;
        position: relative;
        min-height: 600px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .image-section img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.9;
    }

    .form-section {
        flex: 1;
        padding: 40px;
        background: var(--white);
    }

    .form-header {
        margin-bottom: 30px;
    }

    .form-header h1 {
        font-size: 28px;
        color: var(--text-dark);
        margin-bottom: 10px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--text-dark);
        font-size: 14px;
        font-weight: 500;
    }

    .form-group input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #e1e1e1;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-group input:focus {
        outline: none;
        border-color: var(--primary-purple);
        box-shadow: 0 0 0 3px rgba(123, 108, 246, 0.1);
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 20px 0;
    }

    .checkbox-group input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: var(--primary-purple);
    }

    .checkbox-group label {
        font-size: 14px;
        color: var(--text-light);
    }

    .checkbox-group a {
        color: var(--primary-purple);
        text-decoration: none;
    }

    .signup-button {
        width: 100%;
        padding: 12px;
        background: var(--gradient-purple);
        color: var(--white);
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }

    .signup-button:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .login-link {
        text-align: center;
        font-size: 14px;
        color: var(--text-light);
    }

    .login-link a {
        color: var(--primary-purple);
        text-decoration: none;
        font-weight: 500;
    }

    .error-message {
        color: #dc3545;
        font-size: 12px;
        margin-top: 5px;
        display: none;
    }

    input.error {
        border-color: #dc3545;
    }

    input.error:focus {
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    #form-error {
        color: #dc3545;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 4px;
        background-color: rgba(220, 53, 69, 0.1);
    }

    #form-success {
        color: #28a745;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 4px;
        background-color: rgba(40, 167, 69, 0.1);
    }

    @media (max-width: 768px) {
        .image-section {
            display: none;
        }

        .signup-container {
            max-width: 400px;
        }

        .form-section {
            padding: 30px;
        }

        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }

        .success-message {
            color: #28a745;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }

        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .profile-upload {
            margin-bottom: 20px;
        }

        .profile-upload label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-dark);
            font-size: 14px;
            font-weight: 500;
        }
    }
    </style>
</head>

<body>
    <div class="signup-container">
        <div class="image-section">
            <img src="assets/images/extras/signup_robot.png" alt="Fashion model with tablet">
        </div>
        <div class="form-section">
            <div class="form-header">
                <h1>Sign Up</h1>
            </div>
            <form id="signupForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" placeholder="Enter your full name">
                    <div class="error-message" id="fullname-error"></div>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email">
                    <div class="error-message" id="email-error"></div>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Choose a username">
                    <div class="error-message" id="username-error"></div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••">
                    <div class="error-message" id="password-error"></div>
                </div>

                <div class="form-group">
                    <label for="confirm-password">Repeat Password</label>
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="••••••••">
                    <div class="error-message" id="confirm-password-error"></div>
                </div>

                <div class="form-group profile-upload">
                    <label for="profile_picture">Profile Picture (Optional)</label>
                    <input type="file" id="profile_picture" name="profile_picture"
                        accept="image/jpeg,image/png,image/gif">
                    <div class="error-message" id="profile-picture-error"></div>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="terms" name="terms">
                    <label for="terms">I agree to the <a href="#">Terms of Use</a></label>
                    <div class="error-message" id="terms-error"></div>
                </div>

                <button type="submit" class="signup-button">Sign Up</button>
                <div class="success-message" id="form-success"></div>
                <div class="error-message" id="form-error"></div>

                <p class="login-link">
                    Already have an account? <a href="login.html">Sign In →</a>
                </p>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('signupForm');
        const formError = document.getElementById('form-error');
        const formSuccess = document.getElementById('form-success');

        // Password validation function
        function validatePassword(password) {
            const minLength = 8;
            const specialCharRegex = /[!@#$%^&*(),.?":{}|<>]/;

            const errors = [];
            if (password.length < minLength) {
                errors.push(`Password must be at least ${minLength} characters long`);
            }
            if (!specialCharRegex.test(password)) {
                errors.push('Password must contain at least one special character');
            }

            return {
                isValid: errors.length === 0,
                errors: errors
            };
        }

        // Enhanced error display function
        function showError(elementId, message) {
            const errorElement = document.getElementById(`${elementId}-error`);
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.style.display = 'block';
                const input = document.getElementById(elementId);
                if (input) {
                    input.classList.add('error');
                }
            }
        }

        function clearErrors() {
            document.querySelectorAll('.error-message').forEach(div => {
                div.style.display = 'none';
                div.textContent = '';
            });
            document.querySelectorAll('input').forEach(input => {
                input.classList.remove('error');
            });
            formError.style.display = 'none';
            formSuccess.style.display = 'none';
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors();

            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;

            // Validate password
            const passwordValidation = validatePassword(password);
            if (!passwordValidation.isValid) {
                passwordValidation.errors.forEach(error => {
                    showError('password', error);
                });
                return;
            }

            // Check if passwords match
            if (password !== confirmPassword) {
                showError('confirm-password', 'Passwords do not match');
                return;
            }

            const formData = new FormData(this);
            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.textContent;
            submitButton.textContent = 'Processing...';
            submitButton.disabled = true;

            try {
                const response = await fetch('add-user.php', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const textResponse = await response.text();
                let result;

                try {
                    result = JSON.parse(textResponse);
                } catch (e) {
                    throw new Error('Invalid JSON response from server: ' + textResponse);
                }

                if (result.success) {
                    formSuccess.textContent = result.message;
                    formSuccess.style.display = 'block';

                    if (result.redirect) {
                        setTimeout(() => {
                            window.location.href = result.redirect;
                        }, 1500);
                    }
                } else {
                    if (result.errors) {
                        Object.entries(result.errors).forEach(([field, message]) => {
                            showError(field, message);
                        });
                    }

                    if (result.message) {
                        formError.textContent = result.message;
                        formError.style.display = 'block';
                    }
                }
            } catch (error) {
                formError.textContent = 'Error: ' + error.message;
                formError.style.display = 'block';
            } finally {
                submitButton.textContent = originalButtonText;
                submitButton.disabled = false;
            }
        });

        // Real-time password validation
        document.getElementById('password').addEventListener('input', function() {
            const passwordValidation = validatePassword(this.value);
            if (!passwordValidation.isValid) {
                showError('password', passwordValidation.errors.join('. '));
            } else {
                clearErrors();
            }
        });

        // Clear field errors when user starts typing
        form.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                const errorElement = document.getElementById(`${this.id}-error`);
                if (errorElement && this.id !== 'password') {
                    errorElement.style.display = 'none';
                    errorElement.textContent = '';
                }
                this.classList.remove('error');
            });
        });
    });
    </script>
</body>

</html>