<?php
require 'vendor/autoload.php';
session_start();

// Enable error reporting during development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Authentication check
if (!isset($_SESSION['admin'])) {
    header('Location: admin.php');
    exit();
}

try {
    // Use the same connection string that worked in your test.php
    $mongoClient = new MongoDB\Client("mongodb+srv://arcfusionindia:SKu3QYP2zJuhoQps@arcfusion.0j40w.mongodb.net/ArcFusion?retryWrites=true&w=majority");
    
    // Select the ArcFusion database and products collection
    $collection = $mongoClient->ArcFusion->products;
    
    // Debug: Print received data
    error_log('Received POST data: ' . print_r($_POST, true));
    
    // Validate input data
    if (empty($_POST['name']) || empty($_POST['price'])) {
        throw new Exception('Required fields are missing. Name and price are required.');
    }
    
    // Prepare document data
    $productDocument = [
        'name' => htmlspecialchars($_POST['name']),
        'description' => htmlspecialchars($_POST['description'] ?? ''),
        'price' => floatval($_POST['price']),
        'category' => htmlspecialchars($_POST['category'] ?? ''),
        'created_at' => new MongoDB\BSON\UTCDateTime(),
        'updated_at' => new MongoDB\BSON\UTCDateTime()
    ];
    
    // Insert the document
    $insertResult = $collection->insertOne($productDocument);
    
    if ($insertResult->getInsertedCount() > 0) {
        // Success! Redirect with success message
        header('Location: add-product.php?success=1');
        exit();
    } else {
        throw new Exception('Failed to insert document - no document was inserted');
    }
    
} catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
    error_log('MongoDB Atlas Connection Timeout: ' . $e->getMessage());
    // During development, show the error instead of redirecting
    die('Connection Timeout Error: ' . $e->getMessage());
    
} catch (MongoDB\Driver\Exception\AuthenticationException $e) {
    error_log('MongoDB Atlas Authentication Error: ' . $e->getMessage());
    die('Authentication Error: ' . $e->getMessage());
    
} catch (MongoDB\Driver\Exception\Exception $e) {
    error_log('MongoDB Error: ' . $e->getMessage());
    die('Database Error: ' . $e->getMessage());
    
} catch (Exception $e) {
    error_log('Error: ' . $e->getMessage());
    die('General Error: ' . $e->getMessage());
}
?>