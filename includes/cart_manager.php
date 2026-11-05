<?php
// Shopping cart functions
require_once 'db_config.php';
require_once 'user_manager.php';

class CartManager {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getConnection();
        SessionManager::init();
    }
    
    // Add item to cart
    public function addToCart($productId, $quantity = 1, $userId = null) {
        try {
            // If user is not logged in, use session ID
            if (!$userId) {
                $userId = null;
                $sessionId = session_id();
            } else {
                $sessionId = null;
            }
            
            // Check if product exists and is in stock
            $productSql = "SELECT id, name, price, stock_quantity FROM products WHERE id = :product_id AND is_active = 1";
            $productStmt = $this->pdo->prepare($productSql);
            $productStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $productStmt->execute();
            $product = $productStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$product) {
                return ['success' => false, 'message' => 'Product not found'];
            }
            
            if ($product['stock_quantity'] < $quantity) {
                return ['success' => false, 'message' => 'Insufficient stock'];
            }
            
            // Check if item already exists in cart
            $checkSql = "SELECT id, quantity FROM cart WHERE product_id = :product_id AND 
                        ((user_id = :user_id AND :user_id IS NOT NULL) OR 
                         (session_id = :session_id AND :session_id IS NOT NULL))";
            $checkStmt = $this->pdo->prepare($checkSql);
            $checkStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $checkStmt->bindParam(':user_id', $userId);
            $checkStmt->bindParam(':session_id', $sessionId);
            $checkStmt->execute();
            $existingItem = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existingItem) {
                // Update quantity if item already exists
                $newQuantity = $existingItem['quantity'] + $quantity;
                if ($newQuantity > $product['stock_quantity']) {
                    return ['success' => false, 'message' => 'Insufficient stock for requested quantity'];
                }
                
                $updateSql = "UPDATE cart SET quantity = :quantity WHERE id = :id";
                $updateStmt = $this->pdo->prepare($updateSql);
                $updateStmt->bindParam(':quantity', $newQuantity, PDO::PARAM_INT);
                $updateStmt->bindParam(':id', $existingItem['id'], PDO::PARAM_INT);
                $result = $updateStmt->execute();
            } else {
                // Insert new item
                $insertSql = "INSERT INTO cart (user_id, session_id, product_id, quantity) 
                              VALUES (:user_id, :session_id, :product_id, :quantity)";
                $insertStmt = $this->pdo->prepare($insertSql);
                $insertStmt->bindParam(':user_id', $userId);
                $insertStmt->bindParam(':session_id', $sessionId);
                $insertStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
                $insertStmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
                $result = $insertStmt->execute();
            }
            
            if ($result) {
                return ['success' => true, 'message' => 'Item added to cart successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to add item to cart'];
            }
        } catch(PDOException $e) {
            error_log("Error adding to cart: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }
    
    // Get cart items for user or session
    public function getCartItems($userId = null) {
        try {
            if (!$userId) {
                $sessionId = session_id();
            } else {
                $sessionId = null;
            }
            
            $sql = "SELECT c.id as cart_id, c.quantity, c.added_at, 
                           p.id as product_id, p.name, p.price, p.image_url, p.stock_quantity,
                           (c.quantity * p.price) as subtotal
                    FROM cart c
                    JOIN products p ON c.product_id = p.id
                    WHERE ((c.user_id = :user_id AND :user_id IS NOT NULL) OR 
                           (c.session_id = :session_id AND :session_id IS NOT NULL))
                    AND p.is_active = 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':session_id', $sessionId);
            $stmt->execute();
            
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculate total
            $total = 0;
            foreach ($items as $item) {
                $total += $item['subtotal'];
            }
            
            return [
                'items' => $items,
                'total' => $total,
                'count' => count($items)
            ];
        } catch(PDOException $e) {
            error_log("Error getting cart items: " . $e->getMessage());
            return ['items' => [], 'total' => 0, 'count' => 0];
        }
    }
    
    // Update cart item quantity
    public function updateCartItem($cartId, $quantity, $userId = null) {
        try {
            if ($quantity <= 0) {
                return $this->removeFromCart($cartId, $userId);
            }
            
            if (!$userId) {
                $sessionId = session_id();
            } else {
                $sessionId = null;
            }
            
            // Get product info to check stock
            $productSql = "SELECT p.stock_quantity, c.product_id 
                           FROM cart c 
                           JOIN products p ON c.product_id = p.id 
                           WHERE c.id = :cart_id 
                           AND ((c.user_id = :user_id AND :user_id IS NOT NULL) OR 
                                (c.session_id = :session_id AND :session_id IS NOT NULL))";
            $productStmt = $this->pdo->prepare($productSql);
            $productStmt->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
            $productStmt->bindParam(':user_id', $userId);
            $productStmt->bindParam(':session_id', $sessionId);
            $productStmt->execute();
            $cartItem = $productStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$cartItem) {
                return ['success' => false, 'message' => 'Cart item not found'];
            }
            
            if ($cartItem['stock_quantity'] < $quantity) {
                return ['success' => false, 'message' => 'Insufficient stock'];
            }
            
            $updateSql = "UPDATE cart SET quantity = :quantity WHERE id = :id";
            $updateStmt = $this->pdo->prepare($updateSql);
            $updateStmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $updateStmt->bindParam(':id', $cartId, PDO::PARAM_INT);
            
            if ($updateStmt->execute()) {
                return ['success' => true, 'message' => 'Cart updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update cart'];
            }
        } catch(PDOException $e) {
            error_log("Error updating cart item: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }
    
    // Remove item from cart
    public function removeFromCart($cartId, $userId = null) {
        try {
            if (!$userId) {
                $userId = null;
                $sessionId = session_id();
            } else {
                $sessionId = null;
            }
            
            $sql = "DELETE FROM cart WHERE id = :id AND 
                    ((user_id = :user_id AND :user_id IS NOT NULL) OR 
                     (session_id = :session_id AND :session_id IS NOT NULL))";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $cartId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':session_id', $sessionId);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Item removed from cart'];
            } else {
                return ['success' => false, 'message' => 'Failed to remove item from cart'];
            }
        } catch(PDOException $e) {
            error_log("Error removing from cart: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }
    
    // Clear entire cart
    public function clearCart($userId = null) {
        try {
            if (!$userId) {
                $userId = null;
                $sessionId = session_id();
            } else {
                $sessionId = null;
            }
            
            $sql = "DELETE FROM cart WHERE 
                    ((user_id = :user_id AND :user_id IS NOT NULL) OR 
                     (session_id = :session_id AND :session_id IS NOT NULL))";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':session_id', $sessionId);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error clearing cart: " . $e->getMessage());
            return false;
        }
    }
    
    // Get cart count for display
    public function getCartCount($userId = null) {
        try {
            if (!$userId) {
                $sessionId = session_id();
            } else {
                $sessionId = null;
            }
            
            $sql = "SELECT SUM(quantity) as total_items FROM cart WHERE 
                    ((user_id = :user_id AND :user_id IS NOT NULL) OR 
                     (session_id = :session_id AND :session_id IS NOT NULL))";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':session_id', $sessionId);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total_items'] ? $result['total_items'] : 0;
        } catch(PDOException $e) {
            error_log("Error getting cart count: " . $e->getMessage());
            return 0;
        }
    }
}

// Order management functions
class OrderManager {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getConnection();
    }
    
    // Create new order
    public function createOrder($userId, $shippingAddress, $billingAddress, $paymentMethod, $cartItems) {
        try {
            $this->pdo->beginTransaction();
            
            // Calculate total amount
            $totalAmount = 0;
            foreach ($cartItems['items'] as $item) {
                $totalAmount += $item['subtotal'];
            }
            
            // Insert order
            $orderSql = "INSERT INTO orders (user_id, total_amount, shipping_address, billing_address, payment_method) 
                         VALUES (:user_id, :total_amount, :shipping_address, :billing_address, :payment_method)";
            $orderStmt = $this->pdo->prepare($orderSql);
            $orderStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $orderStmt->bindParam(':total_amount', $totalAmount, PDO::PARAM_STR);
            $orderStmt->bindParam(':shipping_address', $shippingAddress);
            $orderStmt->bindParam(':billing_address', $billingAddress);
            $orderStmt->bindParam(':payment_method', $paymentMethod);
            $orderStmt->execute();
            
            $orderId = $this->pdo->lastInsertId();
            
            // Insert order items and update stock
            foreach ($cartItems['items'] as $item) {
                // Insert order item
                $orderItemSql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                                 VALUES (:order_id, :product_id, :quantity, :price)";
                $orderItemStmt = $this->pdo->prepare($orderItemSql);
                $orderItemStmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
                $orderItemStmt->bindParam(':product_id', $item['product_id'], PDO::PARAM_INT);
                $orderItemStmt->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
                $orderItemStmt->bindParam(':price', $item['price'], PDO::PARAM_STR);
                $orderItemStmt->execute();
                
                // Update product stock
                $updateStockSql = "UPDATE products SET stock_quantity = stock_quantity - :quantity 
                                   WHERE id = :product_id";
                $updateStockStmt = $this->pdo->prepare($updateStockSql);
                $updateStockStmt->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
                $updateStockStmt->bindParam(':product_id', $item['product_id'], PDO::PARAM_INT);
                $updateStockStmt->execute();
            }
            
            // Clear user's cart
            $clearCartSql = "DELETE FROM cart WHERE user_id = :user_id";
            $clearCartStmt = $this->pdo->prepare($clearCartSql);
            $clearCartStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $clearCartStmt->execute();
            
            $this->pdo->commit();
            return $orderId;
        } catch(PDOException $e) {
            $this->pdo->rollback();
            error_log("Error creating order: " . $e->getMessage());
            return false;
        }
    }
    
    // Get order by ID
    public function getOrderById($orderId, $userId = null) {
        try {
            $sql = "SELECT o.*, u.username, u.email FROM orders o 
                    JOIN users u ON o.user_id = u.id 
                    WHERE o.id = :order_id";
            
            if ($userId) {
                $sql .= " AND o.user_id = :user_id";
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
            
            if ($userId) {
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error fetching order: " . $e->getMessage());
            return false;
        }
    }
    
    // Get all orders for a user
    public function getUserOrders($userId) {
        try {
            $sql = "SELECT o.*, COUNT(oi.id) as item_count FROM orders o 
                    LEFT JOIN order_items oi ON o.id = oi.order_id
                    WHERE o.user_id = :user_id 
                    GROUP BY o.id 
                    ORDER BY o.order_date DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error fetching user orders: " . $e->getMessage());
            return false;
        }
    }
    
    // Get all orders (for admin)
    public function getAllOrders() {
        try {
            $sql = "SELECT o.*, u.username, u.email, COUNT(oi.id) as item_count FROM orders o 
                    JOIN users u ON o.user_id = u.id
                    LEFT JOIN order_items oi ON o.id = oi.order_id
                    GROUP BY o.id 
                    ORDER BY o.order_date DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error fetching all orders: " . $e->getMessage());
            return false;
        }
    }
    
    // Update order status
    public function updateOrderStatus($orderId, $status) {
        try {
            $sql = "UPDATE orders SET status = :status WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $orderId, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating order status: " . $e->getMessage());
            return false;
        }
    }
}
?>