<!-- db password for 1st user SKu3QYP2zJuhoQps -->

<?php
require 'vendor/autoload.php'; // Include Composer's autoload file

// Connection URI
$uri = "mongodb+srv://arcfusionindia:SKu3QYP2zJuhoQps@arcfusion.0j40w.mongodb.net/?retryWrites=true&w=majority&appName=ArcFusion";

// Create a MongoDB client
try {
    $client = new MongoDB\Client($uri);

    // Select a database and collection
    $database = $client->mydatabase; // Replace with your database name
    $collection = $database->mycollection; // Replace with your collection name

    // Example query: Insert a document
    $result = $collection->insertOne([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    echo "Inserted with Object ID '{$result->getInsertedId()}'";
} catch (Exception $e) {
    echo "Failed to connect to MongoDB Atlas: ", $e->getMessage();
}
?>