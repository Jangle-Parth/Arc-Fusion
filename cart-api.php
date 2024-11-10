<?php
require_once 'vendor/autoload.php';
require 'cart-management.php';
session_start();

// Initialize cart manager
$cartManager = new CartManager();

// Get user ID from session
$userId = $_SESSION['user_id'] ?? $_SESSION['temp_user_id'] ?? null;

if (!$userId) {
    sendJsonResponse(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

// Helper functions
function sendJsonResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function validateProductId($productId) {
    return preg_match('/^[0-9a-fA-F]{24}$/', $productId);
}

try {
    // Handle different HTTP methods
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // Get cart contents
            $result = $cartManager->getCart($userId);
            sendJsonResponse($result);
            break;

        case 'POST':
            // Add to cart
            $input = json_decode(file_get_contents('php://input'), true);
            if (!isset($input['product_id'])) {
                throw new Exception('Product ID is required');
            }
            if (!validateProductId($input['product_id'])) {
                throw new Exception('Invalid product ID format');
            }
            $quantity = isset($input['quantity']) ? (int)$input['quantity'] : 1;
            if ($quantity <= 0) {
                throw new Exception('Quantity must be greater than 0');
            }
            $result = $cartManager->addToCart($userId, $input['product_id'], $quantity);
            sendJsonResponse($result);
            break;

        case 'PUT':
            // Update quantity
            $input = json_decode(file_get_contents('php://input'), true);
            if (!isset($input['product_id']) || !isset($input['quantity'])) {
                throw new Exception('Product ID and quantity are required');
            }
            if (!validateProductId($input['product_id'])) {
                throw new Exception('Invalid product ID format');
            }
            $quantity = (int)$input['quantity'];
            if ($quantity < 0) {
                throw new Exception('Quantity cannot be negative');
            }
            $result = $cartManager->updateQuantity($userId, $input['product_id'], $quantity);
            sendJsonResponse($result);
            break;

        case 'DELETE':
            // Remove from cart
            $input = json_decode(file_get_contents('php://input'), true);
            if (!isset($input['product_id'])) {
                throw new Exception('Product ID is required');
            }
            if (!validateProductId($input['product_id'])) {
                throw new Exception('Invalid product ID format');
            }
            $result = $cartManager->removeFromCart($userId, $input['product_id']);
            sendJsonResponse($result);
            break;

        default:
            throw new Exception('Method not allowed');
    }
} catch (Exception $e) {
    sendJsonResponse([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}