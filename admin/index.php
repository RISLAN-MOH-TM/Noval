<?php
require_once '../includes/product_manager.php';
require_once '../includes/user_manager.php';
require_once '../includes/cart_manager.php';
require_once 'includes/admin_layout.php';

SessionManager::init();
if (!SessionManager::isLoggedIn()) { header('Location: ../login.php?redirect=admin/index.php'); exit; }
if (!SessionManager::isAdmin())    { header('Location: ../novel/home'); exit; }

$productManager  = new ProductManager();
$categoryManager = new CategoryManager();
$userManager     = new UserManager();

$totalProducts  = count($productManager->getAllProducts());
$totalUsers     = count($userManager->getAllUsers());
$totalCategories= count($categoryManager->getAllCategories());

// Low-stock products (qty <= 5)
$allProducts = $productManager->getAllProducts();
$lowStock    = array_filter($allProducts, fn($p) => $p['stock_quantity'] <= 5 && $p['stock_quantity'] > 0);
$outOfStock  = array_filter($allProducts, fn($p) => $p['stock_quantity'] == 0);

adminHead('Dashboard');
adminSidebar('dashboard');
?>

<!-- Page Header -->
<div class="page-head">
    <div class="page-head-left">
        <h1>Dashboard</h1>
        <p>Welcome back, <strong><?php echo htmlspecialchars(SessionManager::get('username')); ?></strong> — here's your store overview.</p>
    </div>
    <div class="page-head-right">
        <a href="products.php?action=add" class="abtn abtn-success">
            <i class="fas fa-plus"></i> Add Product
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-box"></i></div>
        <div class="stat-info">
            <strong><?php echo $totalProducts; ?></strong>
            <span>Total Products</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <strong><?php echo $totalUsers; ?></strong>
            <span>Registered Users</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><i class="fas fa-tags"></i></div>
        <div class="stat-info">
            <strong><?php echo $totalCategories; ?></strong>
            <span>Categories</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-triangle-exclamation"></i></div>
        <div class="stat-info">
            <strong><?php echo count($lowStock) + count($outOfStock); ?></strong>
            <span>Stock Alerts</span>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="acard">
            <div class="acard-head">
                <h5><i class="fas fa-bolt"></i> Quick Actions</h5>
            </div>
            <div class="acard-body">
                <div class="qa-grid" style="grid-template-columns:1fr 1fr;">
                    <a href="products.php?action=add" class="qa-card">
                        <div class="qa-icon" style="background:var(--a-green-light);color:var(--a-green);">
                            <i class="fas fa-plus"></i>
                        </div>
                        Add Product
                    </a>
                    <a href="categories.php?action=add" class="qa-card">
                        <div class="qa-icon" style="background:var(--a-purple-light);color:var(--a-purple);">
                            <i class="fas fa-tags"></i>
                        </div>
                        Add Category
                    </a>
                    <a href="users.php" class="qa-card">
                        <div class="qa-icon" style="background:var(--a-blue-light);color:var(--a-blue);">
                            <i class="fas fa-users"></i>
                        </div>
                        Manage Users
                    </a>
                    <a href="products.php" class="qa-card">
                        <div class="qa-icon" style="background:var(--a-amber-light);color:var(--a-amber);">
                            <i class="fas fa-list"></i>
                        </div>
                        All Products
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Alerts -->
    <div class="col-lg-8">
        <div class="acard">
            <div class="acard-head">
                <h5><i class="fas fa-triangle-exclamation"></i> Stock Alerts</h5>
                <a href="products.php" class="abtn abtn-ghost abtn-sm">View All</a>
            </div>
            <div class="acard-body p0">
                <?php if (empty($lowStock) && empty($outOfStock)): ?>
                    <div style="padding:2rem;text-align:center;color:var(--tx-muted);">
                        <i class="fas fa-circle-check" style="font-size:2rem;color:var(--a-green);margin-bottom:.5rem;display:block;"></i>
                        All products are well stocked!
                    </div>
                <?php else: ?>
                <div style="overflow-x:auto;">
                <table class="atable">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_merge(array_values($outOfStock), array_values($lowStock)) as $p): ?>
                        <tr>
                            <td data-label="Product">
                                <div class="d-flex align-items-center gap-2">
                                    <?php if ($p['image_url']): ?>
                                        <img src="../images/products/<?php echo htmlspecialchars($p['image_url']); ?>" class="prod-thumb" alt="">
                                    <?php else: ?>
                                        <div class="prod-thumb d-flex align-items-center justify-content-center" style="background:var(--bg-page);font-size:.9rem;color:var(--tx-muted);">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="prod-info">
                                        <strong><?php echo htmlspecialchars($p['name']); ?></strong>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Category">
                                <span class="abadge gray"><?php echo htmlspecialchars($p['category_name']); ?></span>
                            </td>
                            <td data-label="Stock">
                                <strong style="color:<?php echo $p['stock_quantity']==0?'var(--a-red)':'var(--a-amber)';?>">
                                    <?php echo $p['stock_quantity']; ?>
                                </strong>
                            </td>
                            <td data-label="Status">
                                <?php if ($p['stock_quantity'] == 0): ?>
                                    <span class="abadge red"><i class="fas fa-times-circle"></i> Out of Stock</span>
                                <?php else: ?>
                                    <span class="abadge amber"><i class="fas fa-exclamation-circle"></i> Low Stock</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="products.php?action=edit&id=<?php echo $p['id']; ?>" class="abtn abtn-outline-primary abtn-sm">
                                    <i class="fas fa-pen"></i> Edit
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Products -->
<div class="acard mt-4">
    <div class="acard-head">
        <h5><i class="fas fa-box"></i> Recent Products</h5>
        <a href="products.php" class="abtn abtn-ghost abtn-sm">View All</a>
    </div>
    <div class="acard-body p0">
        <div style="overflow-x:auto;">
        <table class="atable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($allProducts, 0, 6) as $prod): ?>
                <tr>
                    <td data-label="ID"><span style="color:var(--tx-muted);font-size:.78rem;">#<?php echo $prod['id']; ?></span></td>
                    <td data-label="Product">
                        <div class="d-flex align-items-center gap-2">
                            <?php if ($prod['image_url']): ?>
                                <img src="../images/products/<?php echo htmlspecialchars($prod['image_url']); ?>" class="prod-thumb" alt="">
                            <?php else: ?>
                                <div class="prod-thumb d-flex align-items-center justify-content-center" style="background:var(--bg-page);color:var(--tx-muted);">
                                    <i class="fas fa-image"></i>
                                </div>
                            <?php endif; ?>
                            <strong><?php echo htmlspecialchars($prod['name']); ?></strong>
                        </div>
                    </td>
                    <td data-label="Category"><span class="abadge gray"><?php echo htmlspecialchars($prod['category_name']); ?></span></td>
                    <td data-label="Price"><strong>RS <?php echo number_format($prod['price'], 2); ?></strong></td>
                    <td data-label="Stock">
                        <?php if ($prod['stock_quantity'] == 0): ?>
                            <span class="abadge red"><?php echo $prod['stock_quantity']; ?></span>
                        <?php elseif ($prod['stock_quantity'] <= 5): ?>
                            <span class="abadge amber"><?php echo $prod['stock_quantity']; ?></span>
                        <?php else: ?>
                            <span class="abadge green"><?php echo $prod['stock_quantity']; ?></span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Status">
                        <span class="abadge <?php echo $prod['is_active'] ? 'green' : 'gray'; ?>">
                            <?php echo $prod['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>
                    <td data-label="Actions">
                        <div class="d-flex gap-1">
                            <a href="products.php?action=edit&id=<?php echo $prod['id']; ?>" class="abtn abtn-outline-primary abtn-sm abtn-icon" title="Edit">
                                <i class="fas fa-pen"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<?php adminFoot(); ?>
