<?php
require_once 'includes/product_manager.php';
require_once 'includes/user_manager.php';
require_once 'includes/cart_manager.php';
require_once 'includes/nav_helper.php';

SessionManager::init();

$cartManager = new CartManager();
$userId      = SessionManager::get('user_id');
$cart        = $cartManager->getCartItems($userId);
$msgOk = $msgErr = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        $r = $cartManager->updateCartItem((int)$_POST['cart_id'], (int)$_POST['quantity'], $userId);
        if ($r['success']) $msgOk  = $r['message'];
        else               $msgErr = $r['message'];
    } elseif (isset($_POST['remove_item'])) {
        $r = $cartManager->removeFromCart((int)$_POST['cart_id'], $userId);
        if ($r['success']) $msgOk  = $r['message'];
        else               $msgErr = $r['message'];
    } elseif (isset($_POST['clear_cart'])) {
        $cartManager->clearCart($userId);
        $msgOk = 'Cart cleared.';
    }
    $cart = $cartManager->getCartItems($userId);
}

renderHead('Shopping Cart — Novel Cosmetics');
renderNav('cart', $cart['count']);
?>

<section class="cart-page">
  <div class="container" style="max-width:1100px;">

    <!-- Page head -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
      <div>
        <h1 style="font-family:var(--font-serif);font-size:2rem;margin-bottom:.2rem;">Your Cart</h1>
        <p style="color:var(--c-gray-400);margin:0;"><?= $cart['count'] ?> item<?= $cart['count']!=1?'s':'' ?></p>
      </div>
      <?php if ($cart['count'] > 0): ?>
      <form method="POST">
        <button type="submit" name="clear_cart"
                class="btn-novel btn-novel-ghost btn-novel-sm"
                onclick="return confirm('Clear your entire cart?')">
          <i class="fas fa-trash"></i> Clear Cart
        </button>
      </form>
      <?php endif; ?>
    </div>

    <?php if ($msgOk): ?>
    <div class="alert-novel success"><i class="fas fa-check-circle"></i><?= htmlspecialchars($msgOk) ?></div>
    <?php endif; ?>
    <?php if ($msgErr): ?>
    <div class="alert-novel danger"><i class="fas fa-exclamation-circle"></i><?= htmlspecialchars($msgErr) ?></div>
    <?php endif; ?>

    <?php if ($cart['count'] > 0): ?>
    <div class="row g-4">

      <!-- Items -->
      <div class="col-lg-8">
        <div class="cart-table-card">
          <?php foreach ($cart['items'] as $item):
            $img = $item['image_url'] ? 'images/products/' . $item['image_url'] : 'images/default-product.jpg';
          ?>
          <div class="cart-item-row">
            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="cart-thumb">
            <div class="cart-item-info">
              <strong><?= htmlspecialchars($item['name']) ?></strong>
              <span>RS <?= number_format($item['price'],2) ?> each</span>
            </div>
            <form method="POST" class="cart-qty-form">
              <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
              <input type="number" class="cart-qty-input" name="quantity"
                     value="<?= $item['quantity'] ?>" min="1"
                     max="<?= $item['stock_quantity'] ?>">
              <button type="submit" name="update_cart"
                      style="background:var(--c-gray-100);border:none;border-radius:8px;padding:.4rem .7rem;cursor:pointer;color:var(--c-gray-600);font-size:.8rem;transition:var(--tr);"
                      title="Update">
                <i class="fas fa-rotate-right"></i>
              </button>
            </form>
            <div class="cart-subtotal">RS <?= number_format($item['subtotal'],2) ?></div>
            <form method="POST">
              <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
              <button type="submit" name="remove_item" class="cart-remove-btn" title="Remove">
                <i class="fas fa-trash"></i>
              </button>
            </form>
          </div>
          <?php endforeach; ?>

          <!-- Footer row -->
          <div style="padding:1rem 1.5rem;border-top:1px solid var(--c-gray-200);">
            <a href="/novel/products" class="btn-novel btn-novel-ghost btn-novel-sm">
              <i class="fas fa-arrow-left"></i> Continue Shopping
            </a>
          </div>
        </div>
      </div>

      <!-- Summary -->
      <div class="col-lg-4">
        <div class="order-summary-card">
          <h5>Order Summary</h5>
          <div class="summary-row">
            <span>Subtotal (<?= $cart['count'] ?> items)</span>
            <span>RS <?= number_format($cart['total'],2) ?></span>
          </div>
          <div class="summary-row">
            <span>Tax (8%)</span>
            <span>RS <?= number_format($cart['total']*.08,2) ?></span>
          </div>
          <div class="summary-row">
            <span>Shipping</span>
            <span style="color:var(--c-green);font-weight:600;">Free</span>
          </div>
          <div class="summary-row total">
            <span>Total</span>
            <span>RS <?= number_format($cart['total']*1.08,2) ?></span>
          </div>

          <?php
          $waNum = '94779191742';
          $waMsg = "Hello, I would like to order:\n\n";
          foreach ($cart['items'] as $it) {
              $waMsg .= "*Product:* {$it['name']}\n*Qty:* {$it['quantity']}\n*Price:* RS " . number_format($it['price'],2) . "\n---\n";
          }
          $waMsg .= "\n*Total:* RS " . number_format($cart['total']*1.08,2) . "\n\nPlease confirm availability.";
          $waUrl = "https://wa.me/{$waNum}?text=" . urlencode($waMsg);
          ?>

          <a href="<?= $waUrl ?>" target="_blank"
             class="btn-novel btn-whatsapp"
             style="width:100%;justify-content:center;margin-top:1.25rem;border-radius:12px;">
            <i class="fab fa-whatsapp"></i> Checkout via WhatsApp
          </a>

          <p style="font-size:.72rem;color:var(--c-gray-400);text-align:center;margin-top:.75rem;">
            <i class="fas fa-lock me-1"></i>Secure checkout via WhatsApp
          </p>
        </div>
      </div>
    </div>

    <?php else: ?>
    <!-- Empty cart -->
    <div style="text-align:center;padding:5rem 2rem;background:#fff;border-radius:var(--card-radius);border:1px solid var(--c-gray-200);">
      <div style="width:90px;height:90px;border-radius:50%;background:var(--c-purple-light);display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;font-size:2.2rem;color:var(--c-purple);">
        <i class="fas fa-shopping-bag"></i>
      </div>
      <h4 style="font-family:var(--font-serif);margin-bottom:.5rem;">Your cart is empty</h4>
      <p style="color:var(--c-gray-400);margin-bottom:1.5rem;">Add some beautiful natural products to get started.</p>
      <a href="/novel/products" class="btn-novel btn-novel-primary">
        <i class="fas fa-shopping-bag"></i> Start Shopping
      </a>
    </div>
    <?php endif; ?>

  </div>
</section>

<?php renderFooter(); ?>
