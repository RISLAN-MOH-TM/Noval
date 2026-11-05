<?php
require_once '../includes/product_manager.php';
require_once '../includes/user_manager.php';
require_once '../includes/cart_manager.php';
require_once 'includes/admin_layout.php';

SessionManager::init();
if (!SessionManager::isLoggedIn()) { header('Location: ../login.php?redirect=admin/orders.php'); exit; }
if (!SessionManager::isAdmin())    { header('Location: ../novel/home'); exit; }

// OrderManager may not exist — guard gracefully
$orders     = [];
$order      = null;
$orderItems = [];
$action     = isset($_GET['action']) ? $_GET['action'] : 'view';
$orderId    = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (class_exists('OrderManager')) {
    $orderManager = new OrderManager();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status']) && $orderId > 0) {
        $result  = $orderManager->updateOrderStatus($orderId, $_POST['status']);
        $message = $result ? 'Order status updated.' : null;
        $error   = $result ? null : 'Failed to update status.';
    }

    if ($action === 'details' && $orderId > 0) {
        $order = $orderManager->getOrderById($orderId);
        try {
            $pdo   = getConnection();
            $stmt  = $pdo->prepare("SELECT oi.*, p.name, p.image_url FROM order_items oi
                                    JOIN products p ON oi.product_id = p.id
                                    WHERE oi.order_id = :oid");
            $stmt->bindParam(':oid', $orderId, PDO::PARAM_INT);
            $stmt->execute();
            $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) { $orderItems = []; }
    }

    $orders = $orderManager->getAllOrders();
}

function orderStatusBadge($status) {
    $map = [
        'pending'    => 'amber',
        'processing' => 'blue',
        'shipped'    => 'cyan',
        'delivered'  => 'green',
        'cancelled'  => 'red',
    ];
    $color = $map[$status] ?? 'gray';
    return '<span class="abadge ' . $color . '">' . ucfirst($status) . '</span>';
}
function paymentBadge($status) {
    $map = ['completed'=>'green','pending'=>'amber','failed'=>'red'];
    $color = $map[$status] ?? 'gray';
    return '<span class="abadge ' . $color . '">' . ucfirst($status) . '</span>';
}

adminHead('Orders');
adminSidebar('orders');
?>

<div class="page-head">
    <div class="page-head-left">
        <h1><?php echo $action === 'details' ? 'Order #' . $orderId : 'Orders'; ?></h1>
        <p><?php echo $action === 'details' ? 'Full order details and status management.' : 'View and manage customer orders.'; ?></p>
    </div>
    <?php if ($action === 'details'): ?>
    <div class="page-head-right">
        <a href="orders.php" class="abtn abtn-ghost"><i class="fas fa-arrow-left"></i> Back to Orders</a>
    </div>
    <?php endif; ?>
</div>

<?php if (isset($message)): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $message; ?></div>
<?php endif; ?>
<?php if (isset($error)): ?>
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
<?php endif; ?>

<?php if ($action === 'view'): ?>

<?php if (empty($orders)): ?>
    <div class="acard">
        <div class="acard-body" style="text-align:center;padding:4rem;">
            <i class="fas fa-bag-shopping" style="font-size:3rem;color:var(--tx-muted);margin-bottom:1rem;display:block;"></i>
            <h5 style="color:var(--tx-muted);font-weight:500;">No orders yet</h5>
            <p style="color:var(--tx-muted);">Orders placed on the store will appear here.</p>
        </div>
    </div>
<?php else: ?>
<div class="acard">
    <div class="acard-head">
        <h5><i class="fas fa-bag-shopping"></i> All Orders (<?php echo count($orders); ?>)</h5>
    </div>
    <div class="acard-body p0">
        <div style="overflow-x:auto;">
        <table class="atable">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $ord): ?>
                <tr>
                    <td data-label="Order"><strong>#<?php echo $ord['id']; ?></strong></td>
                    <td data-label="Customer">
                        <div class="d-flex align-items-center gap-2">
                            <div class="user-av" style="width:28px;height:28px;font-size:.72rem;">
                                <?php echo strtoupper(substr($ord['username'], 0, 1)); ?>
                            </div>
                            <?php echo htmlspecialchars($ord['username']); ?>
                        </div>
                    </td>
                    <td data-label="Total"><strong>RS <?php echo number_format($ord['total_amount'], 2); ?></strong></td>
                    <td data-label="Status"><?php echo orderStatusBadge($ord['status']); ?></td>
                    <td data-label="Payment"><?php echo paymentBadge($ord['payment_status']); ?></td>
                    <td data-label="Date" style="color:var(--tx-muted);font-size:.82rem;">
                        <?php echo date('M j, Y', strtotime($ord['order_date'])); ?>
                    </td>
                    <td data-label="Actions">
                        <a href="orders.php?action=details&id=<?php echo $ord['id']; ?>"
                           class="abtn abtn-outline-primary abtn-sm">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php elseif ($action === 'details' && $order): ?>

<div class="row g-4">
    <!-- Order Info -->
    <div class="col-md-6">
        <div class="acard">
            <div class="acard-head"><h5><i class="fas fa-receipt"></i> Order Info</h5></div>
            <div class="acard-body">
                <table style="width:100%;font-size:.875rem;border-collapse:collapse;">
                    <tr><td style="padding:.4rem 0;color:var(--tx-muted);width:45%;">Order ID</td><td><strong>#<?php echo $order['id']; ?></strong></td></tr>
                    <tr><td style="padding:.4rem 0;color:var(--tx-muted);">Date</td><td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td></tr>
                    <tr><td style="padding:.4rem 0;color:var(--tx-muted);">Status</td><td><?php echo orderStatusBadge($order['status']); ?></td></tr>
                    <tr><td style="padding:.4rem 0;color:var(--tx-muted);">Payment</td><td><?php echo paymentBadge($order['payment_status']); ?></td></tr>
                    <tr><td style="padding:.4rem 0;color:var(--tx-muted);">Method</td><td><?php echo ucfirst(str_replace('_',' ',$order['payment_method'])); ?></td></tr>
                    <tr><td style="padding:.4rem 0;color:var(--tx-muted);">Total</td><td><strong style="color:var(--a-purple);font-size:1rem;">RS <?php echo number_format($order['total_amount'], 2); ?></strong></td></tr>
                </table>
            </div>
        </div>
    </div>
    <!-- Customer Info -->
    <div class="col-md-6">
        <div class="acard">
            <div class="acard-head"><h5><i class="fas fa-user"></i> Customer</h5></div>
            <div class="acard-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="user-av" style="width:44px;height:44px;font-size:1.1rem;">
                        <?php echo strtoupper(substr($order['username'], 0, 1)); ?>
                    </div>
                    <div>
                        <strong><?php echo htmlspecialchars($order['username']); ?></strong><br>
                        <span style="font-size:.8rem;color:var(--tx-muted);"><?php echo htmlspecialchars($order['email']); ?></span>
                    </div>
                </div>
                <?php if (!empty($order['shipping_address'])): ?>
                <div style="background:var(--bg-page);border-radius:var(--radius-sm);padding:.875rem;font-size:.825rem;">
                    <div style="font-weight:700;margin-bottom:.35rem;font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;color:var(--tx-muted);">Shipping Address</div>
                    <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Order Items -->
<div class="acard">
    <div class="acard-head"><h5><i class="fas fa-box"></i> Order Items</h5></div>
    <div class="acard-body p0">
        <div style="overflow-x:auto;">
        <table class="atable">
            <thead>
                <tr><th>Product</th><th>Unit Price</th><th>Qty</th><th>Subtotal</th></tr>
            </thead>
            <tbody>
                <?php foreach ($orderItems as $item): ?>
                <tr>
                    <td data-label="Product">
                        <div class="d-flex align-items-center gap-2">
                            <?php if ($item['image_url']): ?>
                                <img src="../images/products/<?php echo htmlspecialchars($item['image_url']); ?>" class="prod-thumb" alt="">
                            <?php endif; ?>
                            <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                        </div>
                    </td>
                    <td data-label="Unit Price">RS <?php echo number_format($item['price'], 2); ?></td>
                    <td data-label="Qty"><span class="abadge gray"><?php echo $item['quantity']; ?></span></td>
                    <td data-label="Subtotal"><strong>RS <?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align:right;padding:.9rem 1.1rem;font-weight:700;color:var(--tx-head);">Grand Total</td>
                    <td style="padding:.9rem 1.5rem;font-size:1.1rem;font-weight:800;color:var(--a-purple);">
                        RS <?php echo number_format($order['total_amount'], 2); ?>
                    </td>
                </tr>
            </tfoot>
        </table>
        </div>
    </div>
</div>

<!-- Update Status -->
<div class="acard">
    <div class="acard-head"><h5><i class="fas fa-rotate"></i> Update Status</h5></div>
    <div class="acard-body">
        <form method="POST" class="d-flex gap-3 align-items-end flex-wrap">
            <div style="flex:1;min-width:200px;">
                <label class="form-label">Order Status</label>
                <select class="form-control" name="status" required>
                    <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                        <option value="<?php echo $s; ?>" <?php echo $order['status'] === $s ? 'selected' : ''; ?>>
                            <?php echo ucfirst($s); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="update_status" class="abtn abtn-primary">
                <i class="fas fa-rotate"></i> Update
            </button>
        </form>
    </div>
</div>

<?php endif; ?>

<?php adminFoot(); ?>
