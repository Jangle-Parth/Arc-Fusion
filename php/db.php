<?php
require 'vendor/autoload.php';

class Database {
    private static $instance = null;
    private $client;
    private $db;
    
    // Database configuration
    private $config = [
        'username' => 'arcfusionindia',
        'password' => 'SKu3QYP2zJuhoQps',
        'cluster' => 'arcfusion.0j40w.mongodb.net',
        'database' => 'ArcFusion'
    ];

    // Private constructor to prevent direct instantiation
    private function __construct() {
        try {
            // Create connection string
            $connectionString = "mongodb+srv://{$this->config['username']}:{$this->config['password']}@{$this->config['cluster']}/{$this->config['database']}?retryWrites=true&w=majority";
            
            // Initialize MongoDB client with options
            $this->client = new MongoDB\Client($connectionString, [
                'serverSelectionTimeoutMS' => 5000,
                'socketTimeoutMS' => 10000,
                'ssl' => true
            ]);
            
            $this->db = $this->client->{$this->config['database']};
            
        } catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            $this->handleError('Connection timeout', $e);
        } catch (MongoDB\Driver\Exception\AuthenticationException $e) {
            $this->handleError('Authentication failed', $e);
        } catch (Exception $e) {
            $this->handleError('Database connection error', $e);
        }
    }

    // Get database instance (Singleton pattern)
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Find documents in a collection
    public function find($collection, $filter = [], $options = []) {
        try {
            return $this->db->$collection->find($filter, $options)->toArray();
        } catch (Exception $e) {
            $this->handleError('Error retrieving documents', $e);
            return [];
        }
    }

    // Find one document
    public function findOne($collection, $filter = [], $options = []) {
        try {
            return $this->db->$collection->findOne($filter, $options);
        } catch (Exception $e) {
            $this->handleError('Error retrieving document', $e);
            return null;
        }
    }

    // Insert a single document
    public function insertOne($collection, $document) {
        try {
            $document['created_at'] = new MongoDB\BSON\UTCDateTime();
            $document['updated_at'] = new MongoDB\BSON\UTCDateTime();
            return $this->db->$collection->insertOne($document);
        } catch (Exception $e) {
            $this->handleError('Error inserting document', $e);
            return null;
        }
    }

    // Update a single document
    public function updateOne($collection, $filter, $update) {
        try {
            $update['$set']['updated_at'] = new MongoDB\BSON\UTCDateTime();
            return $this->db->$collection->updateOne($filter, $update);
        } catch (Exception $e) {
            $this->handleError('Error updating document', $e);
            return null;
        }
    }

    // Delete a single document
    public function deleteOne($collection, $filter) {
        try {
            return $this->db->$collection->deleteOne($filter);
        } catch (Exception $e) {
            $this->handleError('Error deleting document', $e);
            return null;
        }
    }

    // Handle database errors
    private function handleError($message, $exception) {
        $errorMessage = $message . ': ' . $exception->getMessage();
        error_log($errorMessage);
        
        if (php_sapi_name() !== 'cli') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $message
            ]);
            exit();
        } else {
            throw new Exception($errorMessage);
        }
    }

    // Prevent cloning of the instance (Singleton pattern)
    private function __clone() {}

    // Prevent unserializing of the instance (Singleton pattern)
    public function __wakeup() {}
}