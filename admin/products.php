<?php
require_once '../includes/product_manager.php';
require_once '../includes/user_manager.php';
require_once 'includes/admin_layout.php';

SessionManager::init();
if (!SessionManager::isLoggedIn()) { header('Location: ../login.php?redirect=admin/products.php'); exit; }
if (!SessionManager::isAdmin())    { header('Location: ../novel/home'); exit; }

$productManager  = new ProductManager();
$categoryManager = new CategoryManager();

$action    = isset($_GET['action']) ? $_GET['action'] : 'view';
$productId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product']) || isset($_POST['edit_product'])) {
        $name          = trim($_POST['name']);
        $description   = trim($_POST['description']);
        $price         = (float)$_POST['price'];
        $categoryId    = (int)$_POST['category_id'];
        $stockQuantity = (int)$_POST['stock_quantity'];
        $isActive      = isset($_POST['is_active']) ? 1 : 0;

        $image_url = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $uploadDir = '../images/products/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $fileName  = time() . '_' . basename($_FILES['image']['name']);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
                $image_url = $fileName;
            }
        }

        if (isset($_POST['add_product'])) {
            $result = $productManager->createProduct($name, $description, $price, $categoryId, $image_url, $stockQuantity);
            $message = $result ? 'Product added successfully!' : null;
            $error   = $result ? null : 'Failed to add product.';
            if ($result) $action = 'view';
        } else {
            if ($image_url === null) {
                $existing = $productManager->getProductById($productId);
                if ($existing) $image_url = $existing['image_url'];
            }
            $result = $productManager->updateProduct($productId, $name, $description, $price, $categoryId, $image_url, $stockQuantity, $isActive);
            $message = $result ? 'Product updated successfully!' : null;
            $error   = $result ? null : 'Failed to update product.';
            if ($result) $action = 'view';
        }
    } elseif (isset($_POST['delete_product']) && $productId > 0) {
        // Grab the image filename BEFORE deleting the DB row
        $imageToDelete = $productManager->getProductImage($productId);

        $result  = $productManager->deleteProduct($productId);
        if ($result) {
            // Delete the physical image file if one exists
            if ($imageToDelete) {
                $imagePath = __DIR__ . '/../images/products/' . $imageToDelete;
                if (file_exists($imagePath) && is_file($imagePath)) {
                    unlink($imagePath);
                }
            }
            $message = 'Product deleted successfully.';
        } else {
            $error = 'Failed to delete product.';
        }
        $action = 'view';
    }
}

$product  = ($action === 'edit' && $productId > 0) ? $productManager->getProductById($productId) : null;
$products = $productManager->getAllProducts();
$categories = $categoryManager->getAllCategories();

adminHead('Products');
adminSidebar('products');
?>

<div class="page-head">
    <div class="page-head-left">
        <h1><?php echo $action === 'view' ? 'Products' : ($action === 'add' ? 'Add Product' : 'Edit Product'); ?></h1>
        <p><?php echo $action === 'view' ? 'Manage your product catalog and inventory.' : 'Fill in the details below.'; ?></p>
    </div>
    <div class="page-head-right">
        <?php if ($action === 'view'): ?>
            <a href="products.php?action=add" class="abtn abtn-success">
                <i class="fas fa-plus"></i> Add Product
            </a>
        <?php else: ?>
            <a href="products.php" class="abtn abtn-ghost">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($message)): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $message; ?></div>
<?php endif; ?>
<?php if (isset($error)): ?>
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
<?php endif; ?>

<?php if ($action === 'view'): ?>
<!-- ── PRODUCTS TABLE ── -->
<div class="acard">
    <div class="acard-head">
        <h5><i class="fas fa-box"></i> All Products (<?php echo count($products); ?>)</h5>
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
                <?php foreach ($products as $prod): ?>
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
                            <div class="prod-info">
                                <strong><?php echo htmlspecialchars($prod['name']); ?></strong>
                                <span><?php echo htmlspecialchars(substr($prod['description'], 0, 40)) . '...'; ?></span>
                            </div>
                        </div>
                    </td>
                    <td data-label="Category">
                        <span class="abadge gray"><?php echo htmlspecialchars($prod['category_name']); ?></span>
                    </td>
                    <td data-label="Price"><strong>RS <?php echo number_format($prod['price'], 2); ?></strong></td>
                    <td data-label="Stock">
                        <?php if ($prod['stock_quantity'] == 0): ?>
                            <span class="abadge red"><i class="fas fa-times-circle"></i> <?php echo $prod['stock_quantity']; ?></span>
                        <?php elseif ($prod['stock_quantity'] <= 5): ?>
                            <span class="abadge amber"><i class="fas fa-exclamation-circle"></i> <?php echo $prod['stock_quantity']; ?></span>
                        <?php else: ?>
                            <span class="abadge green"><i class="fas fa-check-circle"></i> <?php echo $prod['stock_quantity']; ?></span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Status">
                        <span class="abadge <?php echo $prod['is_active'] ? 'green' : 'gray'; ?>">
                            <?php echo $prod['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>
                    <td data-label="Actions">
                        <div class="d-flex gap-1">
                            <a href="products.php?action=edit&id=<?php echo $prod['id']; ?>"
                               class="abtn abtn-outline-primary abtn-sm" title="Edit">
                                <i class="fas fa-pen"></i> Edit
                            </a>
                            <form method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this product permanently?');">
                                <input type="hidden" name="delete_product" value="1">
                                <input type="hidden" name="id" value="<?php echo $prod['id']; ?>">
                                <button type="submit" class="abtn abtn-outline-danger abtn-sm" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<?php else: ?>
<!-- ── ADD / EDIT FORM ── -->
<form method="POST" enctype="multipart/form-data">
    <?php if ($action === 'edit'): ?>
        <input type="hidden" name="id" value="<?php echo $productId; ?>">
    <?php endif; ?>

    <div class="row g-4">
        <!-- Left: Main details -->
        <div class="col-lg-8">
            <div class="acard">
                <div class="acard-head">
                    <h5><i class="fas fa-circle-info"></i> Product Information</h5>
                </div>
                <div class="acard-body">
                    <div class="form-section-title">Basic Details</div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Product Name <span style="color:var(--a-red)">*</span></label>
                            <input type="text" class="form-control" name="name"
                                   value="<?php echo $product ? htmlspecialchars($product['name']) : ''; ?>"
                                   placeholder="e.g. Rose Face Serum" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Price (RS) <span style="color:var(--a-red)">*</span></label>
                            <input type="number" class="form-control" name="price" step="0.01" min="0"
                                   value="<?php echo $product ? $product['price'] : ''; ?>"
                                   placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Category <span style="color:var(--a-red)">*</span></label>
                            <select class="form-control" name="category_id" required>
                                <option value="">— Select Category —</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"
                                        <?php echo ($product && $product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stock Quantity <span style="color:var(--a-red)">*</span></label>
                            <input type="number" class="form-control" name="stock_quantity" min="0"
                                   value="<?php echo $product ? $product['stock_quantity'] : '0'; ?>"
                                   placeholder="0" required>
                        </div>
                    </div>

                    <div class="form-section-title">Description</div>
                    <div class="mb-3">
                        <label class="form-label">Product Description <span style="color:var(--a-red)">*</span></label>
                        <textarea class="form-control" name="description" rows="5"
                                  placeholder="Describe the product, ingredients, benefits..."
                                  required><?php echo $product ? htmlspecialchars($product['description']) : ''; ?></textarea>
                    </div>

                    <div class="form-section-title">Visibility</div>
                    <div class="d-flex align-items-center gap-3 p-3" style="background:var(--bg-page);border-radius:var(--radius-sm);">
                        <div class="form-check form-switch mb-0">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                   <?php echo (!$product || $product['is_active']) ? 'checked' : ''; ?>>
                        </div>
                        <div>
                            <label for="is_active" style="font-weight:600;color:var(--tx-head);cursor:pointer;">Active Product</label>
                            <div style="font-size:.8rem;color:var(--tx-muted);">Product will be visible to customers on the store.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Image -->
        <div class="col-lg-4">
            <div class="acard">
                <div class="acard-head">
                    <h5><i class="fas fa-image"></i> Product Image</h5>
                </div>
                <div class="acard-body">
                    <?php if ($product && $product['image_url']): ?>
                        <div class="text-center mb-3">
                            <img src="../images/products/<?php echo htmlspecialchars($product['image_url']); ?>"
                                 style="width:100%;max-height:200px;object-fit:cover;border-radius:var(--radius-sm);border:1px solid var(--border);"
                                 alt="Current Image">
                            <p style="font-size:.75rem;color:var(--tx-muted);margin-top:.5rem;">Current image — upload new to replace</p>
                        </div>
                    <?php else: ?>
                        <label for="image" class="img-upload-zone mb-3 d-block">
                            <i class="fas fa-cloud-arrow-up"></i>
                            <p>Click to upload image</p>
                            <p style="font-size:.72rem;">PNG, JPG, WEBP — max 2MB</p>
                        </label>
                    <?php endif; ?>
                    <label class="form-label">Upload <?php echo $product ? 'New ' : ''; ?>Image</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <p style="font-size:.75rem;color:var(--tx-muted);margin-top:.5rem;">Recommended: 600×600 px</p>
                </div>
            </div>

            <!-- Save card -->
            <div class="acard">
                <div class="acard-body">
                    <?php if ($action === 'add'): ?>
                        <button type="submit" name="add_product" class="abtn abtn-success w-100 mb-2">
                            <i class="fas fa-plus"></i> Add Product
                        </button>
                    <?php else: ?>
                        <button type="submit" name="edit_product" class="abtn abtn-primary w-100 mb-2">
                            <i class="fas fa-floppy-disk"></i> Save Changes
                        </button>
                    <?php endif; ?>
                    <a href="products.php" class="abtn abtn-ghost w-100" style="text-decoration:none;">
                        <i class="fas fa-xmark"></i> Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
<?php endif; ?>

<?php adminFoot(); ?>
