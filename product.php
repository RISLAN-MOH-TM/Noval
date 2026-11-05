<?php
require_once 'includes/product_manager.php';
require_once 'includes/user_manager.php';
require_once 'includes/cart_manager.php';
require_once 'includes/nav_helper.php';

SessionManager::init();

$productManager = new ProductManager();
$cartManager    = new CartManager();

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$productId) { header('Location: /novel/products'); exit; }

$product = $productManager->getProductById($productId);
if (!$product) { header('Location: /novel/products'); exit; }

$msgOk = $msgErr = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $qty    = max(1, (int)($_POST['quantity'] ?? 1));
    $userId = SessionManager::get('user_id');
    $result = $cartManager->addToCart($productId, $qty, $userId);
    if ($result['success']) $msgOk  = $result['message'];
    else                    $msgErr = $result['message'];
}

$userId    = SessionManager::get('user_id');
$cartCount = $cartManager->getCartCount($userId);
$related   = array_filter(
    $productManager->getProductsByCategory($product['category_id'], 4),
    fn($p) => $p['id'] != $productId
);

$img = $product['image_url'] ? 'images/products/' . $product['image_url'] : 'images/default-product.jpg';
$inStock = $product['stock_quantity'] > 0;

// WhatsApp checkout
$waNum = '94779191742';
$waMsg = "Hello, I'd like to order:\n\n*Product:* {$product['name']}\n*Qty:* 1\n*Price:* RS " . number_format($product['price'],2) . "\n\nPlease confirm availability.";
$waUrl = "https://wa.me/{$waNum}?text=" . urlencode($waMsg);

renderHead(htmlspecialchars($product['name']) . ' — Novel Cosmetics');
renderNav('products', $cartCount);
?>

<!-- ══ BREADCRUMB ════════════════════════════════════════ -->
<div style="background:var(--c-gray-50);border-bottom:1px solid var(--c-gray-200);padding:.75rem 0;">
  <div class="container" style="max-width:1240px;">
    <nav style="font-size:.8rem;color:var(--c-gray-400);">
      <a href="/novel/home" style="color:var(--c-gray-400);">Home</a>
      <span style="margin:0 .5rem;">›</span>
      <a href="/novel/products" style="color:var(--c-gray-400);">Products</a>
      <span style="margin:0 .5rem;">›</span>
      <span style="color:var(--c-gray-800);"><?= htmlspecialchars($product['name']) ?></span>
    </nav>
  </div>
</div>

<!-- ══ PRODUCT DETAIL ════════════════════════════════════ -->
<section class="product-detail-page">
  <div class="container" style="max-width:1240px;">
    <div class="row g-5">

      <!-- Image -->
      <div class="col-lg-5">
        <div class="product-img-gallery">
          <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        </div>
      </div>

      <!-- Info -->
      <div class="col-lg-7">
        <div class="product-cat-label"><i class="fas fa-leaf me-1"></i><?= htmlspecialchars($product['category_name']) ?></div>
        <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>

        <div class="product-price"><span class="cur">RS </span><?= number_format($product['price'], 2) ?></div>

        <span class="product-stock-tag <?= $inStock ? 'in' : 'out' ?>">
          <i class="fas <?= $inStock ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
          <?= $inStock ? 'In Stock (' . $product['stock_quantity'] . ' available)' : 'Out of Stock' ?>
        </span>

        <p class="product-desc"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

        <?php if ($msgOk): ?>
        <div class="alert-novel success mb-3"><i class="fas fa-check-circle"></i><?= htmlspecialchars($msgOk) ?></div>
        <?php endif; ?>
        <?php if ($msgErr): ?>
        <div class="alert-novel danger mb-3"><i class="fas fa-exclamation-circle"></i><?= htmlspecialchars($msgErr) ?></div>
        <?php endif; ?>

        <?php if ($inStock): ?>
        <form method="POST">
          <div class="qty-row">
            <label style="font-size:.8rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--c-gray-600);">Qty</label>
            <input type="number" class="qty-input" id="quantity" name="quantity"
                   min="1" max="<?= $product['stock_quantity'] ?>" value="1">
          </div>
          <div class="product-actions">
            <button type="submit" name="add_to_cart" class="btn-novel btn-novel-green">
              <i class="fas fa-cart-plus"></i> Add to Cart
            </button>
            <a href="#" id="checkoutBtn" class="btn-novel btn-whatsapp" onclick="doCheckout();return false;">
              <i class="fab fa-whatsapp"></i> Buy via WhatsApp
            </a>
          </div>
        </form>
        <?php else: ?>
        <button class="btn-novel" disabled style="background:var(--c-gray-200);color:var(--c-gray-400);cursor:not-allowed;">
          <i class="fas fa-ban"></i> Out of Stock
        </button>
        <?php endif; ?>

        <!-- Meta pills -->
        <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-top:2rem;padding-top:1.5rem;border-top:1px solid var(--c-gray-200);">
          <?php foreach (['fas fa-leaf|100% Natural','fas fa-flask|Chemical-Free','fas fa-heart|Cruelty-Free','fas fa-recycle|Eco-Friendly'] as $it):
            [$ico,$lbl] = explode('|',$it); ?>
          <span style="display:inline-flex;align-items:center;gap:.4rem;font-size:.75rem;font-weight:600;background:var(--c-gray-100);color:var(--c-gray-600);padding:.3rem .8rem;border-radius:50px;">
            <i class="fas <?= $ico ?>" style="color:var(--c-green);"></i><?= $lbl ?>
          </span>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══ RELATED ═══════════════════════════════════════════ -->
<?php if ($related): ?>
<section class="related-section">
  <div class="container" style="max-width:1240px;">
    <div class="text-center mb-4">
      <h3 style="font-family:var(--font-serif);">You might also like</h3>
    </div>
    <div class="row g-3">
      <?php foreach (array_values($related) as $rp):
        $rimg = $rp['image_url'] ? 'images/products/' . $rp['image_url'] : 'images/default-product.jpg';
      ?>
      <div class="col-6 col-md-4 col-lg-3">
        <div class="pcard">
          <div class="pcard-img">
            <img src="<?= htmlspecialchars($rimg) ?>" alt="<?= htmlspecialchars($rp['name']) ?>" loading="lazy">
            <div class="pcard-overlay">
              <a href="/novel/product/<?= $rp['id'] ?>" class="pcard-overlay-btn">Quick View</a>
            </div>
          </div>
          <div class="pcard-body">
            <div class="pcard-name"><?= htmlspecialchars($rp['name']) ?></div>
            <div class="pcard-price"><span class="cur">RS</span><?= number_format($rp['price'],2) ?></div>
            <div class="pcard-actions mt-2">
              <a href="/novel/product/<?= $rp['id'] ?>" class="btn-detail"><i class="fas fa-eye"></i> View</a>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<script>
function doCheckout() {
    const qty   = document.getElementById('quantity')?.value || 1;
    const name  = <?= json_encode($product['name']) ?>;
    const price = <?= json_encode(number_format($product['price'],2)) ?>;
    const total = (parseFloat(<?= $product['price'] ?>) * parseInt(qty)).toFixed(2);
    const wa    = '<?= $waNum ?>';
    const msg   = `Hello, I'd like to order:\n\n*Product:* ${name}\n*Qty:* ${qty}\n*Price:* RS ${price}\n*Total:* RS ${total}\n\nPlease confirm availability.`;
    window.open(`https://wa.me/${wa}?text=` + encodeURIComponent(msg), '_blank');
}
</script>

<?php renderFooter(); ?>
