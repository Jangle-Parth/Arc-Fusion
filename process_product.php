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
$clusterUrl = "arcfusion.0j40w.mongodb.net"; // e.g., cluster0.xxxxx.mongodb.net
$database = "ArcFusion";

// Create the connection string
$connectionString = "mongodb+srv://{$mongoUsername}:{$mongoPassword}@{$clusterUrl}/{$database}?retryWrites=true&w=majority";

try {
    // Create a new MongoDB client with connection options
    $mongoClient = new MongoDB\Client($connectionString, [
        'serverSelectionTimeoutMS' => 5000, // 5 second timeout
        'socketTimeoutMS' => 10000, // 10 second timeout
        'ssl' => true
    ]);
    
    // Select database and collection
    $db = $mongoClient->$database;
    $collection = $db->products;
    
    // Validate input data
    if (empty($_POST['name']) || empty($_POST['price'])) {
        throw new Exception('Required fields are missing');
    }
    
    // Prepare document data
    $productDocument = [
        'name' => htmlspecialchars($_POST['name']),
        'description' => htmlspecialchars($_POST['description']),
        'price' => floatval($_POST['price']),
        'category' => htmlspecialchars($_POST['category']),
        'created_at' => new MongoDB\BSON\UTCDateTime(),
        'updated_at' => new MongoDB\BSON\UTCDateTime(),
    ];
    
    // Insert the document
    $insertResult = $collection->insertOne($productDocument);
    
    if ($insertResult->getInsertedCount() > 0) {
        // Log successful insertion
        error_log("Product inserted successfully with ID: " . $insertResult->getInsertedId());
        
        header('Location: add-product.php?success=1');
        exit();
    } else {
        throw new Exception('Failed to insert document');
    }
    
} catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
    // Handle connection timeout
    error_log('MongoDB Atlas Connection Timeout: ' . $e->getMessage());
    header('Location: add-product.php?error=timeout');
    exit();
} catch (MongoDB\Driver\Exception\AuthenticationException $e) {
    // Handle authentication errors
    error_log('MongoDB Atlas Authentication Error: ' . $e->getMessage());
    header('Location: add-product.php?error=auth');
    exit();
} catch (MongoDB\Driver\Exception\Exception $e) {
    // Handle MongoDB-specific errors
    error_log('MongoDB Error: ' . $e->getMessage());
    header('Location: add-product.php?error=db');
    exit();
} catch (Exception $e) {
    // Handle general errors
    error_log('Error: ' . $e->getMessage());
    header('Location: add-product.php?error=general');
    exit();
}
?>