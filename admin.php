<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
    :root {
        --primary: #7B6CF6;
        --secondary: #4A3F9F;
        --text-dark: #2D2A45;
        --text-light: #6E6B85;
        --white: #FFFFFF;
        --background: #F3F1FF;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
    }

    body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: var(--background);
    }

    .login-container {
        background: var(--white);
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 8px 16px rgba(123, 108, 246, 0.2);
        width: 300px;
        text-align: center;
    }

    h2 {
        margin-bottom: 1rem;
        color: var(--text-dark);
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 8px;
    }

    .login-btn {
        width: 100%;
        padding: 0.75rem;
        background-color: var(--primary);
        color: var(--white);
        border: none;
        border-radius: 8px;
        cursor: pointer;
        margin-top: 1rem;
    }

    .error-message {
        color: red;
        display: none;
        margin-top: 1rem;
    }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <form id="loginForm">
            <div class="form-group">
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>
            <div class="form-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="login-btn">Login</button>
            <div id="errorMessage" class="error-message">Invalid credentials</div>
        </form>
    </div>

    <script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'add-product.php';
                } else {
                    document.getElementById('errorMessage').style.display = 'block';
                }
            })
            .catch(error => console.error('Error:', error));
    });
    </script>
</body>

</html>