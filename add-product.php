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

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-dark);
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-sizing: border-box;
    }

    .color-options {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-top: 0.5rem;
    }

    .color-option {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .image-preview {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .preview-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 4px;
    }

    .submit-btn {
        width: 100%;
        padding: 0.75rem;
        background: var(--primary);
        color: var(--white);
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 500;
    }

    .submit-btn:hover {
        background: var(--secondary);
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
                <label>Available Colors</label>
                <div class="color-options">
                    <div class="color-option">
                        <input type="checkbox" id="color-white" name="colors[]" value="white">
                        <label for="color-white">White</label>
                    </div>
                    <div class="color-option">
                        <input type="checkbox" id="color-red" name="colors[]" value="red">
                        <label for="color-red">Red</label>
                    </div>
                    <div class="color-option">
                        <input type="checkbox" id="color-blue" name="colors[]" value="blue">
                        <label for="color-blue">Blue</label>
                    </div>
                    <div class="color-option">
                        <input type="checkbox" id="color-glow" name="colors[]" value="glow">
                        <label for="color-glow">Glow</label>
                    </div>
                    <div class="color-option">
                        <input type="checkbox" id="color-black" name="colors[]" value="black">
                        <label for="color-black">Black</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="images">Product Images</label>
                <input type="file" id="images" name="images[]" multiple accept="image/*" onchange="previewImages(event)"
                    required>
                <div id="imagePreview" class="image-preview"></div>
            </div>

            <div class="form-group">
                <label for="price">Product Price</label>
                <input type="number" id="price" name="price" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="price2">Alternative Price (Optional)</label>
                <input type="number" id="price2" name="price2" step="0.01">
            </div>

            <div class="form-group">
                <label for="ratings">Product Rating</label>
                <select id="ratings" name="ratings" required>
                    <option value="">Select rating</option>
                    <option value="5">5 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="2">2 Stars</option>
                    <option value="1">1 Star</option>
                </select>
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

    function previewImages(event) {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';
        const files = event.target.files;

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            if (!file.type.startsWith('image/')) continue;

            const img = document.createElement('img');
            img.classList.add('preview-image');
            img.file = file;

            preview.appendChild(img);

            const reader = new FileReader();
            reader.onload = (function(aImg) {
                return function(e) {
                    aImg.src = e.target.result;
                };
            })(img);

            reader.readAsDataURL(file);
        }
    }
    </script>
</body>

</html>