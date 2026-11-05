<?php
require_once 'includes/product_manager.php';
require_once 'includes/user_manager.php';
require_once 'includes/cart_manager.php';

SessionManager::init();

$orderManager = new OrderManager();

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if (!$orderId) {
    header('Location: index.php');
    exit;
}

$userId = SessionManager::get('user_id');
$order = $orderManager->getOrderById($orderId, $userId);

if (!$order) {
    header('Location: index.php');
    exit;
}

// Get order items
try {
    $pdo = getConnection();
    $orderItemsSql = "SELECT oi.*, p.name, p.image_url FROM order_items oi 
                      JOIN products p ON oi.product_id = p.id 
                      WHERE oi.order_id = :order_id";
    $orderItemsStmt = $pdo->prepare($orderItemsSql);
    $orderItemsStmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
    $orderItemsStmt->execute();
    $orderItems = $orderItemsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $orderItems = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.png" type="image/png">
    <title>Order Confirmation - Nature's Glow</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.php">
                    <i class="fas fa-leaf text-success"></i> Nature's Glow
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="products.php">Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="about.php">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact.php">Contact</a>
                        </li>
                    </ul>
                    
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">Hello, <?php echo SessionManager::get('username'); ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                        <?php if (SessionManager::isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin/index.php">Admin Panel</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center bg-success text-white">
                        <h3><i class="fas fa-check-circle me-2"></i>Order Confirmed!</h3>
                    </div>
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                        <h4 class="mt-3">Thank You for Your Order!</h4>
                        <p class="text-muted">Your order has been placed successfully.</p>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h5>Order Details</h5>
                                <p><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>
                                <p><strong>Date:</strong> <?php echo date('M j, Y', strtotime($order['order_date'])); ?></p>
                                <p><strong>Status:</strong> 
                                    <span class="badge bg-<?php 
                                        echo $order['status'] === 'pending' ? 'warning' : 
                                             ($order['status'] === 'processing' ? 'info' : 
                                             ($order['status'] === 'shipped' ? 'primary' : 
                                             ($order['status'] === 'delivered' ? 'success' : 'danger'))); ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h5>Payment Details</h5>
                                <p><strong>Method:</strong> <?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></p>
                                <p><strong>Status:</strong> 
                                    <span class="badge bg-<?php 
                                        echo $order['payment_status'] === 'completed' ? 'success' : 
                                             ($order['payment_status'] === 'pending' ? 'warning' : 'danger'); ?>">
                                        <?php echo ucfirst($order['payment_status']); ?>
                                    </span>
                                </p>
                                <p><strong>Total:</strong> RS <?php echo number_format($order['total_amount'], 2); ?></p>
                            </div>
                        </div>
                        
                        <h5 class="mt-4">Order Items</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orderItems as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                                            <td>RS <?php echo number_format($item['price'], 2); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>RS <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total:</th>
                                        <th>RS <?php echo number_format($order['total_amount'], 2); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <h5 class="mt-4">Shipping Information</h5>
                        <p class="text-start"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                        
                        <div class="mt-4">
                            <a href="index.php" class="btn btn-success me-2">
                                <i class="fas fa-home me-2"></i>Back to Home
                            </a>
                            <a href="profile.php#orders" class="btn btn-outline-success">
                                <i class="fas fa-box me-2"></i>View Orders
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>Nature's Glow</h5>
                    <p>Pure natural cosmetics for healthy, glowing skin. Made with organic ingredients and sustainable practices.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-light text-decoration-none">Home</a></li>
                        <li><a href="products.php" class="text-light text-decoration-none">Products</a></li>
                        <li><a href="about.php" class="text-light text-decoration-none">About Us</a></li>
                        <li><a href="contact.php" class="text-light text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Contact Info</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-map-marker-alt me-2"></i> 52/A, Deens Road, T/D-5, Akkaraipattu 32400</li>
                        <li><i class="fas fa-phone me-2"></i> 0779191742</li>
                        <li><i class="fas fa-envelope me-2"></i> info@naturesglow.com</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p>&copy; 2026 Nature's Glow. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="js/main.js"></script>
</body>
</html>