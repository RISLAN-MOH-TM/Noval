<?php
require_once '../includes/product_manager.php';
require_once '../includes/user_manager.php';
require_once 'includes/admin_layout.php';

SessionManager::init();
if (!SessionManager::isLoggedIn()) { header('Location: ../login.php?redirect=admin/categories.php'); exit; }
if (!SessionManager::isAdmin())    { header('Location: ../novel/home'); exit; }

$categoryManager = new CategoryManager();
$productManager  = new ProductManager();

$action     = isset($_GET['action']) ? $_GET['action'] : 'view';
$categoryId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category']) || isset($_POST['edit_category'])) {
        $name        = trim($_POST['name']);
        $description = trim($_POST['description']);

        if (isset($_POST['add_category'])) {
            $result  = $categoryManager->createCategory($name, $description);
            $message = $result ? 'Category added successfully!' : null;
            $error   = $result ? null : 'Failed to add category.';
            if ($result) $action = 'view';
        } else {
            $result  = $categoryManager->updateCategory($categoryId, $name, $description);
            $message = $result ? 'Category updated successfully!' : null;
            $error   = $result ? null : 'Failed to update category.';
            if ($result) $action = 'view';
        }
    } elseif (isset($_POST['delete_category']) && $categoryId > 0) {
        $result  = $categoryManager->deleteCategory($categoryId);
        $message = $result ? 'Category deleted.' : null;
        $error   = $result ? null : 'Failed to delete category.';
        $action  = 'view';
    }
}

$category   = ($action === 'edit' && $categoryId > 0) ? $categoryManager->getCategoryById($categoryId) : null;
$categories = $categoryManager->getAllCategories();

adminHead('Categories');
adminSidebar('categories');
?>

<div class="page-head">
    <div class="page-head-left">
        <h1><?php echo $action === 'view' ? 'Categories' : ($action === 'add' ? 'Add Category' : 'Edit Category'); ?></h1>
        <p>Organise your products into logical groups.</p>
    </div>
    <div class="page-head-right">
        <?php if ($action === 'view'): ?>
            <a href="categories.php?action=add" class="abtn abtn-success">
                <i class="fas fa-plus"></i> Add Category
            </a>
        <?php else: ?>
            <a href="categories.php" class="abtn abtn-ghost">
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
<div class="acard">
    <div class="acard-head">
        <h5><i class="fas fa-tags"></i> All Categories (<?php echo count($categories); ?>)</h5>
    </div>
    <div class="acard-body p0">
        <div style="overflow-x:auto;">
        <table class="atable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Products</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat):
                    $catProducts = $productManager->getProductsByCategory($cat['id']);
                    $count = count($catProducts);
                ?>
                <tr>
                    <td data-label="ID"><span style="color:var(--tx-muted);font-size:.78rem;">#<?php echo $cat['id']; ?></span></td>
                    <td data-label="Name">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:34px;height:34px;border-radius:8px;background:var(--a-purple-light);
                                        display:flex;align-items:center;justify-content:center;color:var(--a-purple);font-size:.85rem;">
                                <i class="fas fa-tag"></i>
                            </div>
                            <strong><?php echo htmlspecialchars($cat['name']); ?></strong>
                        </div>
                    </td>
                    <td data-label="Description" style="max-width:300px;">
                        <span style="color:var(--tx-muted);">
                            <?php echo htmlspecialchars(substr($cat['description'], 0, 70)); ?>
                            <?php if (strlen($cat['description']) > 70) echo '...'; ?>
                        </span>
                    </td>
                    <td data-label="Products">
                        <span class="abadge <?php echo $count > 0 ? 'purple' : 'gray'; ?>">
                            <?php echo $count; ?> product<?php echo $count != 1 ? 's' : ''; ?>
                        </span>
                    </td>
                    <td data-label="Actions">
                        <div class="d-flex gap-1">
                            <a href="categories.php?action=edit&id=<?php echo $cat['id']; ?>"
                               class="abtn abtn-outline-primary abtn-sm">
                                <i class="fas fa-pen"></i> Edit
                            </a>
                            <form method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this category? Products inside will lose their category.');">
                                <input type="hidden" name="delete_category" value="1">
                                <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                <button type="submit" class="abtn abtn-outline-danger abtn-sm">
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
<form method="POST">
    <?php if ($action === 'edit'): ?>
        <input type="hidden" name="id" value="<?php echo $categoryId; ?>">
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="acard">
                <div class="acard-head">
                    <h5><i class="fas fa-circle-info"></i> Category Details</h5>
                </div>
                <div class="acard-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name <span style="color:var(--a-red)">*</span></label>
                        <input type="text" class="form-control" name="name"
                               value="<?php echo $category ? htmlspecialchars($category['name']) : ''; ?>"
                               placeholder="e.g. Face Care, Lip Care..." required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Description <span style="color:var(--a-red)">*</span></label>
                        <textarea class="form-control" name="description" rows="4"
                                  placeholder="Brief description shown on the store..."
                                  required><?php echo $category ? htmlspecialchars($category['description']) : ''; ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="acard">
                <div class="acard-body">
                    <?php if ($action === 'add'): ?>
                        <button type="submit" name="add_category" class="abtn abtn-success w-100 mb-2">
                            <i class="fas fa-plus"></i> Add Category
                        </button>
                    <?php else: ?>
                        <button type="submit" name="edit_category" class="abtn abtn-primary w-100 mb-2">
                            <i class="fas fa-floppy-disk"></i> Save Changes
                        </button>
                    <?php endif; ?>
                    <a href="categories.php" class="abtn abtn-ghost w-100" style="text-decoration:none;">
                        <i class="fas fa-xmark"></i> Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
<?php endif; ?>

<?php adminFoot(); ?>
