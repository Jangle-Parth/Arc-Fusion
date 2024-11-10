<?php
require 'vendor/autoload.php';
session_start();

// Authentication check
if (!isset($_SESSION['admin'])) {
    header('Location: admin.php');
    exit();
}

// MongoDB Atlas connection settings
$mongoUsername = "arcfusionindia";
$mongoPassword = "SKu3QYP2zJuhoQps";
$clusterUrl = "arcfusion.0j40w.mongodb.net";
$database = "ArcFusion";

// Create the connection string
$connectionString = "mongodb+srv://{$mongoUsername}:{$mongoPassword}@{$clusterUrl}/{$database}?retryWrites=true&w=majority";

try {
    // Create a new MongoDB client
    $mongoClient = new MongoDB\Client($connectionString, [
        'serverSelectionTimeoutMS' => 5000,
        'socketTimeoutMS' => 10000,
        'ssl' => true
    ]);
    
    $db = $mongoClient->$database;
    $collection = $db->products;
    
    // Validate required fields
    if (empty($_POST['name']) || empty($_POST['price'])) {
        throw new Exception('Required fields are missing');
    }

    // Handle image uploads
    $imageUrls = [];
    if (isset($_FILES['images'])) {
        $uploadDir = 'uploads/products/'; // Make sure this directory exists and is writable
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Process each uploaded file
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                $filename = uniqid() . '_' . basename($_FILES['images']['name'][$key]);
                $uploadFile = $uploadDir . $filename;
                
                // Validate file type
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $fileType = mime_content_type($_FILES['images']['tmp_name'][$key]);
                
                if (!in_array($fileType, $allowedTypes)) {
                    throw new Exception('Invalid file type uploaded');
                }
                
                if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $uploadFile)) {
                    $imageUrls[] = $uploadFile;
                }
            }
        }
    }

    // Process colors
    $colors = isset($_POST['colors']) ? $_POST['colors'] : [];

    // Prepare document data
    $productDocument = [
        'name' => htmlspecialchars($_POST['name']),
        'description' => htmlspecialchars($_POST['description']),
        'price' => floatval($_POST['price']),
        'price2' => !empty($_POST['price2']) ? floatval($_POST['price2']) : null,
        'category' => htmlspecialchars($_POST['category']),
        'colors' => array_map('htmlspecialchars', $colors),
        'images' => $imageUrls,
        'ratings' => isset($_POST['ratings']) ? intval($_POST['ratings']) : null,
        'created_at' => new MongoDB\BSON\UTCDateTime(),
        'updated_at' => new MongoDB\BSON\UTCDateTime()
    ];

    // Insert the document
    $insertResult = $collection->insertOne($productDocument);
    
    if ($insertResult->getInsertedCount() > 0) {
        // Log successful insertion
        error_log("Product inserted successfully with ID: " . $insertResult->getInsertedId());
        
        // Return success response as JSON
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Product added successfully',
            'productId' => (string)$insertResult->getInsertedId()
        ]);
    } else {
        throw new Exception('Failed to insert document');
    }
    
} catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
    // Handle connection timeout
    error_log('MongoDB Atlas Connection Timeout: ' . $e->getMessage());
    sendErrorResponse('Database connection timeout');
} catch (MongoDB\Driver\Exception\AuthenticationException $e) {
    // Handle authentication errors
    error_log('MongoDB Atlas Authentication Error: ' . $e->getMessage());
    sendErrorResponse('Database authentication error');
} catch (MongoDB\Driver\Exception\Exception $e) {
    // Handle MongoDB-specific errors
    error_log('MongoDB Error: ' . $e->getMessage());
    sendErrorResponse('Database error occurred');
} catch (Exception $e) {
    // Handle general errors
    error_log('Error: ' . $e->getMessage());
    sendErrorResponse($e->getMessage());
}

// Helper function to send error response
function sendErrorResponse($message) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => $message
    ]);
    exit();
}
?>