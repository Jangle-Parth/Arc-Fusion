<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: index.html');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Add Product</title>
    <style>
    :root {
        --primary: #7B6CF6;
        --secondary: #4A3F9F;
        --background: #F3F1FF;
        --text-dark: #2D2A45;
        --white: #FFFFFF;
    }

    body {
        background-color: var(--background);
        padding: 2rem;
        font-family: 'Inter', sans-serif;
    }

    .admin-container {
        max-width: 800px;
        margin: 0 auto;
        background: var(--white);
        padding: 2rem;
        border-radius: 8px;
    }

    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .logout-btn {
        padding: 0.5rem 1rem;
        background: var(--secondary);
        color: var(--white);
        border: none;
        border-radius: 8px;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 0.75rem;
        border-radius: 8px;
    }

    .submit-btn {
        width: 100%;
        padding: 0.75rem;
        background: var(--primary);
        color: var(--white);
        border: none;
        border-radius: 8px;
        cursor: pointer;
    }

    .success-message {
        color: #059669;
        text-align: center;
        margin-top: 1rem;
        display: none;
    }
    </style>
</head>

<body>
    <div class="admin-container">
        <div class="admin-header">
            <h2>Add New Product</h2>
            <button class="logout-btn" onclick="logout()">Logout</button>
        </div>

        <form id="productForm" action="process_product.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="description">Product Description</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="price">Product Price</label>
                <input type="number" id="price" name="price" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <option value="">Select a category</option>
                    <option value="home_decor">Home Decor</option>
                    <option value="toys">Toys</option>
                    <option value="gifts">Gifts</option>
                    <option value="electronics">Electronics</option>
                </select>
            </div>
            <button type="submit" class="submit-btn">Add Product</button>
        </form>
        <div id="successMessage" class="success-message">Product added successfully!</div>
    </div>

    <script>
    function logout() {
        window.location.href = 'logout.php';
    }
    </script>
</body>

</html>