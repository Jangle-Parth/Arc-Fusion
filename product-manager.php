<?php
// includes/ProductManager.php
require_once 'php/db.php';  // Make sure the correct file path is referenced

class ProductManager {
    private $db;

    public function __construct() {
        // Corrected the class name from DatabaseConnection to Database
        $this->db = Database::getInstance();
    }

    public function getDailyDrops($limit = 3) {
        try {
            $drops = $this->db->find('products', 
                ['isDropOfDay' => true], 
                [
                    'limit' => $limit,
                    'sort' => ['createdAt' => -1]
                ]
            );
            
            return ['success' => true, 'data' => $drops];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getBestSellers($limit = 3) {
        try {
            $bestsellers = $this->db->find('products', 
                [], 
                [
                    'limit' => $limit,
                    'sort' => ['salesCount' => -1]
                ]
            );
            
            return ['success' => true, 'data' => $bestsellers];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getProductDetails($productId) {
        try {
            $product = $this->db->findOne('products', 
                ['_id' => new MongoDB\BSON\ObjectId($productId)]
            );
            
            return $product ? 
                ['success' => true, 'data' => $product] : 
                ['success' => false, 'message' => 'Product not found'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
?>