<?php
// First check if MongoDB extension is installed
if (!extension_loaded('mongodb')) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'MongoDB PHP extension is not installed. Please install it first.',
        'error' => 'Missing MongoDB extension'
    ]);
    exit;
}

// Check if Composer autoloader exists
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Dependencies not installed. Please run "composer require mongodb/mongodb" in the project directory.',
        'error' => 'Missing Composer dependencies'
    ]);
    exit;
}

require __DIR__ . '/vendor/autoload.php';
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

try {
    // Basic validation
    $errors = [];
    
    if (empty($_POST['email'])) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }
    
    if (empty($_POST['password'])) {
        $errors['password'] = 'Password is required';
    }

    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'errors' => $errors,
            'message' => 'Please correct the errors in the form'
        ]);
        exit;
    }

    // MongoDB connection
    $mongoUsername = "arcfusionindia";
    $mongoPassword = "SKu3QYP2zJuhoQps";
    $clusterUrl = "arcfusion.0j40w.mongodb.net";
    $database = "ArcFusion";

    $connectionString = "mongodb+srv://{$mongoUsername}:{$mongoPassword}@{$clusterUrl}/{$database}?retryWrites=true&w=majority";
    
    $mongoClient = new MongoDB\Client($connectionString);
    $db = $mongoClient->$database;
    $collection = $db->users;

    // Find user by email
    $user = $collection->findOne(['email' => strtolower($_POST['email'])]);
    
    if (!$user) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email or password'
        ]);
        exit;
    }

    // Verify password
    if (!password_verify($_POST['password'], $user->password)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email or password'
        ]);
        exit;
    }

    // Check if account is active
    if (!$user->active) {
        echo json_encode([
            'success' => false,
            'message' => 'Your account is currently inactive. Please contact support.'
        ]);
        exit;
    }

    // Set session variables
    $_SESSION['user_id'] = (string)$user->_id;
    $_SESSION['username'] = $user->username;
    $_SESSION['fullname'] = $user->fullname;

    // Update last login timestamp
    $collection->updateOne(
        ['_id' => $user->_id],
        ['$set' => ['last_login' => new MongoDB\BSON\UTCDateTime()]]
    );

    echo json_encode([
        'success' => true,
        'message' => 'Login successful! Redirecting...',
        'redirect' => 'profile.php'
    ]);

} catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection timeout. Please try again.',
        'error' => $e->getMessage()
    ]);
} catch (MongoDB\Driver\Exception\AuthenticationException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database authentication failed. Please contact support.',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage(),
        'error' => $e->getMessage()
    ]);
}
?>