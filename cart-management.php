<?php
require_once 'vendor/autoload.php';
session_start();

class CartManager {
    private $mongoClient;
    private $db;
    private $cartsCollection;
    private $productsCollection;

    public function __construct() {
        // MongoDB connection settings
        $mongoUsername = "arcfusionindia";
        $mongoPassword = "SKu3QYP2zJuhoQps";
        $clusterUrl = "arcfusion.0j40w.mongodb.net";
        $database = "ArcFusion";

        $connectionString = "mongodb+srv://{$mongoUsername}:{$mongoPassword}@{$clusterUrl}/{$database}?retryWrites=true&w=majority";
         
        $this->mongoClient = new MongoDB\Client($connectionString);
        $this->db = $this->mongoClient->$database;
        $this->cartsCollection = $this->db->carts;
        $this->productsCollection = $this->db->products;
    }

    public function addToCart($userId, $productId, $quantity = 1) {
        try {
            // First verify if product exists
            $product = $this->productsCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($productId)]);
            if (!$product) {
                return ['success' => false, 'message' => 'Product not found'];
            }

            // Find existing cart for user
            $cart = $this->cartsCollection->findOne(['user_id' => $userId]);
            
            if (!$cart) {
                // Create new cart if doesn't exist
                $result = $this->cartsCollection->insertOne([
                    'user_id' => $userId,
                    'items' => [
                        [
                            'product_id' => new MongoDB\BSON\ObjectId($productId),
                            'quantity' => $quantity,
                            'added_at' => new MongoDB\BSON\UTCDateTime()
                        ]
                    ],
                    'created_at' => new MongoDB\BSON\UTCDateTime(),
                    'updated_at' => new MongoDB\BSON\UTCDateTime()
                ]);
                return ['success' => true, 'message' => 'Product added to cart'];
            }

            // Check if product already exists in cart
            $itemExists = false;
            foreach ($cart['items'] as &$item) {
                if ((string)$item['product_id'] === $productId) {
                    $item['quantity'] += $quantity;
                    $itemExists = true;
                    break;
                }
            }

            if (!$itemExists) {
                // Add new product to cart
                $cart['items'][] = [
                    'product_id' => new MongoDB\BSON\ObjectId($productId),
                    'quantity' => $quantity,
                    'added_at' => new MongoDB\BSON\UTCDateTime()
                ];
            }

            // Update cart
            $this->cartsCollection->updateOne(
                ['user_id' => $userId],
                [
                    '$set' => [
                        'items' => $cart['items'],
                        'updated_at' => new MongoDB\BSON\UTCDateTime()
                    ]
                ]
            );

            return ['success' => true, 'message' => 'Cart updated successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getCart($userId) {
        try {
            $cart = $this->cartsCollection->findOne(['user_id' => $userId]);
            if (!$cart) {
                return ['success' => true, 'items' => [], 'total' => 0];
            }

            // Get product details for each item in cart
            $cartItems = [];
            $cartTotal = 0;

            foreach ($cart['items'] as $item) {
                $product = $this->productsCollection->findOne(
                    ['_id' => $item['product_id']]
                );

                if ($product) {
                    $itemPrice = $product['price'] ?? 0;
                    $itemTotal = $itemPrice * $item['quantity'];
                    $cartTotal += $itemTotal;

                    $cartItems[] = [
                        'id' => (string)$item['product_id'],
                        'name' => $product['name'],
                        'description' => $product['description'],
                        'price' => $itemPrice,
                        'price2' => $product['price2'] ?? null,
                        'quantity' => $item['quantity'],
                        'category' => $product['category'] ?? '',
                        'colors' => $product['colors'] ?? [],
                        'images' => $product['images'] ?? [],
                        'ratings' => $product['ratings'] ?? 0,
                        'total' => $itemTotal,
                        'added_at' => $item['added_at']
                    ];
                }
            }

            // Sort items by added_at timestamp
            usort($cartItems, function($a, $b) {
                return $b['added_at']->toDateTime()->getTimestamp() - 
                       $a['added_at']->toDateTime()->getTimestamp();
            });

            return [
                'success' => true,
                'items' => $cartItems,
                'total' => $cartTotal,
                'item_count' => count($cartItems),
                'created_at' => $cart['created_at'],
                'updated_at' => $cart['updated_at']
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function updateQuantity($userId, $productId, $quantity) {
        try {
            if ($quantity <= 0) {
                return $this->removeFromCart($userId, $productId);
            }

            $this->cartsCollection->updateOne(
                [
                    'user_id' => $userId,
                    'items.product_id' => new MongoDB\BSON\ObjectId($productId)
                ],
                [
                    '$set' => [
                        'items.$.quantity' => $quantity,
                        'updated_at' => new MongoDB\BSON\UTCDateTime()
                    ]
                ]
            );

            return ['success' => true, 'message' => 'Quantity updated'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function removeFromCart($userId, $productId) {
        try {
            $this->cartsCollection->updateOne(
                ['user_id' => $userId],
                [
                    '$pull' => ['items' => ['product_id' => new MongoDB\BSON\ObjectId($productId)]],
                    '$set' => ['updated_at' => new MongoDB\BSON\UTCDateTime()]
                ]
            );

            return ['success' => true, 'message' => 'Product removed from cart'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}