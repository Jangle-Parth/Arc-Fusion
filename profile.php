<?php
require_once 'vendor/autoload.php';
session_start();
function checkAuth() {
    // Check if user is not logged in
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        // Store the intended destination URL in session
        $_SESSION['redirect_url'] = $_SERVER['PHP_SELF'];
        
        // Set a message to show after login
        $_SESSION['message'] = "Please login to access your profile.";
        $_SESSION['message_type'] = "info";
        
        // Redirect to login page
        header("Location: login.html");
        exit();
    }
    
    // Check if session has expired (optional - if you want to implement session timeout)
    $session_lifetime = 3600; // 1 hour in seconds
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_lifetime)) {
        // Destroy session and redirect to login
        session_unset();
        session_destroy();
        
        // Start new session for messages
        session_start();
        $_SESSION['message'] = "Your session has expired. Please login again.";
        $_SESSION['message_type'] = "warning";
        
        header("Location: login.html");
        exit();
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
}

class ProfileManager {
    private $mongoClient;
    private $db;
    private $usersCollection;
    private $ordersCollection;
    private $cartsCollection;

    public function __construct() {
        // MongoDB Atlas connection settings
        $mongoUsername = "arcfusionindia";
        $mongoPassword = "SKu3QYP2zJuhoQps";
        $clusterUrl = "arcfusion.0j40w.mongodb.net";
        $database = "ArcFusion";

        $connectionString = "mongodb+srv://{$mongoUsername}:{$mongoPassword}@{$clusterUrl}/{$database}?retryWrites=true&w=majority";
         
        $this->mongoClient = new MongoDB\Client($connectionString);
        $this->db = $this->mongoClient->$database;
        $this->usersCollection = $this->db->users;
        $this->ordersCollection = $this->db->orders;
        $this->cartsCollection = $this->db->carts;
    }

    public function getUserData($userId) {
        try {
            return $this->usersCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($userId)]);
        } catch (Exception $e) {
            return null;
        }
    }

    public function getUserOrders($userId) {
        try {
            return $this->ordersCollection->find(['user_id' => $userId])->toArray();
        } catch (Exception $e) {
            return [];
        }
    }

    public function calculateUserStats($userId) {
        $orders = $this->getUserOrders($userId);
        $totalOrders = count($orders);
        
        // Calculate points (example: 300 points per order)
        $points = $totalOrders * 300;
        
        // Calculate average rating (you can implement your own logic)
        $rating = 4.9;
        
        return [
            'total_orders' => $totalOrders,
            'points' => $points,
            'rating' => $rating
        ];
    }

    public function formatDate($mongoDate) {
        return $mongoDate->toDateTime()->format('M d, Y');
    }
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}
checkAuth();


try {
    $userId = $_SESSION['user_id'];
    $profileManager = new ProfileManager();
    $userData = $profileManager->getUserData($userId);
    
    if (!$userData) {
        // Handle case where user data cannot be found
        $_SESSION['message'] = "Unable to load profile data. Please try again.";
        $_SESSION['message_type'] = "error";
        header("Location: error.php");
        exit();
    }

    $userStats = $profileManager->calculateUserStats($userId);
    $memberSince = (new MongoDB\BSON\UTCDateTime($userData->created_at))->toDateTime()->format('Y');

} catch (Exception $e) {
    // Log error and redirect to error page
    error_log("Profile Data Error: " . $e->getMessage());
    $_SESSION['message'] = "Error loading profile. Please try again later.";
    $_SESSION['message_type'] = "error";
    header("Location: error.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($userData->fullname); ?>'s Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    :root {
        --primary: #7B6CF6;
        --secondary: #4A3F9F;
        --accent: #9D8DF7;
        --background: #F3F1FF;
        --text-dark: #2D2A45;
        --text-light: #6E6B85;
        --white: #FFFFFF;
        --shadow-sm: 0 2px 4px rgba(123, 108, 246, 0.1);
        --shadow-md: 0 4px 6px rgba(123, 108, 246, 0.15);
        --shadow-lg: 0 8px 16px rgba(123, 108, 246, 0.2);
        --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
    }

    body {
        background-color: var(--background);
        color: var(--text-dark);
        min-height: 100vh;
    }

    .profile-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 2rem;
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 2rem;
        position: relative;
        animation: fadeIn 0.8s ease-out;
    }

    .profile-sidebar {
        position: sticky;
        top: 2rem;
        height: fit-content;
    }

    .profile-card {
        background: var(--white);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--shadow-lg);
        text-align: center;
        transform-style: preserve-3d;
        transition: var(--transition-smooth);
    }

    .profile-image-container {
        width: 150px;
        height: 150px;
        margin: 0 auto 1.5rem;
        position: relative;
    }

    .profile-image {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--primary);
    }

    .profile-status {
        position: absolute;
        bottom: 10px;
        right: 10px;
        width: 20px;
        height: 20px;
        background: #2ecc71;
        border-radius: 50%;
        border: 3px solid var(--white);
        animation: pulse 2s infinite;
    }

    .profile-name {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        background: linear-gradient(45deg, var(--primary), var(--accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .profile-title {
        color: var(--text-light);
        margin-bottom: 1.5rem;
    }

    .profile-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin: 1.5rem 0;
        padding: 1rem 0;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    .stat-item {
        text-align: center;
    }

    .stat-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary);
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--text-light);
    }

    .profile-actions {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .action-btn {
        flex: 1;
        padding: 0.75rem;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition-smooth);
        position: relative;
        overflow: hidden;
    }

    .primary-btn {
        background: var(--primary);
        color: var(--white);
    }

    .secondary-btn {
        background: var(--background);
        color: var(--primary);
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .action-btn::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: -100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: 0.5s;
    }

    .action-btn:hover::after {
        left: 100%;
    }

    .profile-main {
        display: grid;
        gap: 2rem;
    }

    .section-card {
        background: var(--white);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--shadow-md);
        transition: var(--transition-smooth);
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title i {
        color: var(--primary);
    }

    .orders-grid {
        display: grid;
        gap: 1rem;
    }

    .order-item {
        display: grid;
        grid-template-columns: auto 1fr auto;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--background);
        border-radius: 10px;
        transition: var(--transition-smooth);
    }

    .order-item:hover {
        transform: translateX(10px);
        background: #e8e5ff;
        box-shadow: var(--shadow-md);
    }

    .order-image {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        object-fit: cover;
    }

    .order-details {
        display: grid;
        gap: 0.25rem;
    }

    .order-details h4 {
        margin-bottom: 0.25rem;
        color: var(--text-dark);
    }

    .order-details p {
        color: var(--text-light);
        font-size: 0.875rem;
    }

    .order-details .order-date {
        font-size: 0.75rem;
        color: var(--text-light);
    }

    .order-status {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-delivered {
        background: #d4ffed;
        color: #00a15c;
    }

    .status-processing {
        background: #fff3d4;
        color: #a17100;
    }

    .status-shipped {
        background: #e8e5ff;
        color: var(--primary);
    }

    .status-cancelled {
        background: #ffe5e5;
        color: #a10000;
    }

    .orders-filter {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition-smooth);
        background: var(--background);
        color: var(--text-light);
    }

    .filter-btn.active {
        background: var(--primary);
        color: var(--white);
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(46, 204, 113, 0.4);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(46, 204, 113, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(46, 204, 113, 0);
        }
    }

    @media (max-width: 1024px) {
        .profile-container {
            grid-template-columns: 1fr;
        }

        .profile-sidebar {
            position: static;
        }
    }

    @media (max-width: 480px) {
        .profile-container {
            padding: 1rem;
        }

        .orders-filter {
            justify-content: center;
        }

        .order-item {
            grid-template-columns: 1fr;
            text-align: center;
        }

        .order-image {
            margin: 0 auto;
        }
    }
    </style>
</head>

<body>
    <div class="profile-container">
        <aside class="profile-sidebar">
            <div class="profile-card">
                <div class="profile-image-container">
                    <img src="assets/images/founders/parth.png" alt="Profile" class="profile-image">
                    <div class="profile-status"></div>
                </div>
                <h2 class="profile-name"><?php echo htmlspecialchars($userData->fullname); ?></h2>
                <p class="profile-title">Member Since <?php echo $memberSince; ?></p>
                <div class="profile-stats">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $userStats['total_orders']; ?></div>
                        <div class="stat-label">Orders</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo number_format($userStats['points']); ?></div>
                        <div class="stat-label">Points</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo number_format($userStats['rating'], 1); ?></div>
                        <div class="stat-label">Rating</div>
                    </div>
                </div>
                <div class="profile-actions">
                    <button class="action-btn primary-btn" onclick="location.href='edit-profile.php'">Edit
                        Profile</button>
                    <button class="action-btn secondary-btn" onclick="location.href='settings.php'">Settings</button>
                </div>
            </div>
        </aside>

        <main class="profile-main">
            <section class="section-card">
                <h3 class="section-title">
                    <i class="fas fa-shopping-bag"></i>
                    Order History
                </h3>
                <div class="orders-filter">
                    <button class="filter-btn active" data-filter="all">All Orders</button>
                    <button class="filter-btn" data-filter="processing">Processing</button>
                    <button class="filter-btn" data-filter="shipped">Shipped</button>
                    <button class="filter-btn" data-filter="delivered">Delivered</button>
                </div>
                <div class="orders-grid">
                    <?php
                    $orders = $profileManager->getUserOrders($userId);
                    if (empty($orders)) {
                        echo '<div class="no-orders">No orders found</div>';
                    } else {
                        foreach ($orders as $order) {
                            $orderDate = $profileManager->formatDate($order->created_at);
                            $status = isset($order->status) ? $order->status : 'processing';
                            $orderTotal = isset($order->total) ? number_format($order->total, 2) : '0.00';
                            ?>
                    <div class="order-item" data-order-id="<?php echo $order->_id; ?>">
                        <img src="<?php echo $order->items[0]->image ?? '/api/placeholder/60/60'; ?>" alt="Product"
                            class="order-image">
                        <div class="order-details">
                            <h4><?php echo htmlspecialchars($order->items[0]->product_name ?? 'Product Name'); ?></h4>
                            <p>Order #<?php echo substr((string)$order->_id, -5); ?> • $<?php echo $orderTotal; ?></p>
                            <span class="order-date">Ordered on <?php echo $orderDate; ?></span>
                        </div>
                        <span class="order-status status-<?php echo $status; ?>"><?php echo ucfirst($status); ?></span>
                    </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </section>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter functionality
        const filterButtons = document.querySelectorAll('.filter-btn');
        const orderItems = document.querySelectorAll('.order-item');

        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');

                const filter = this.dataset.filter;
                orderItems.forEach(item => {
                    if (filter === 'all') {
                        item.style.display = 'grid';
                    } else {
                        const status = item.querySelector('.order-status').classList;
                        item.style.display = status.contains('status-' + filter) ?
                            'grid' : 'none';
                    }
                });
            });
        });

        // Add click event for order items to view details
        orderItems.forEach(item => {
            item.addEventListener('click', function() {
                const orderId = this.dataset.orderId;
                window.location.href = `order-details.php?id=${orderId}`;
            });
        });
    });
    </script>
</body>

</html>