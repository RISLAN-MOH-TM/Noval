<?php
require_once 'includes/product_manager.php';
require_once 'includes/user_manager.php';
require_once 'includes/cart_manager.php';
require_once 'includes/nav_helper.php';

SessionManager::init();

$productManager  = new ProductManager();
$categoryManager = new CategoryManager();
$cartManager     = new CartManager();

$products   = $productManager->getAllProducts(8);
$categories = $categoryManager->getAllCategories();
$userId     = SessionManager::get('user_id');
$cartCount  = $cartManager->getCartCount($userId);

renderHead("Nature's Glow — Novel Cosmetics | 100% Natural Beauty");
renderNav('home', $cartCount);
?>

<!-- ══ HERO ═══════════════════════════════════════════ -->
<section class="hero">
  <div class="hero-bg">
    <img src="images/hero-new.png" alt="Pure Natural Beauty" fetchpriority="high">
  </div>
  <div class="hero-content">
    <div class="col-lg-6">
      <div class="hero-eyebrow fade-up">
        <span class="pulse"></span>
        100% Natural &amp; Organic
      </div>
      <h1 class="hero-title fade-up d1">
        Beauty rooted<br>in <em>nature</em>.
      </h1>
      <p class="hero-desc fade-up d2">
        Handcrafted cosmetics made with pure botanical ingredients — zero chemicals, zero compromise. Discover what nature can do for your skin.
      </p>
      <div class="hero-cta fade-up d3">
        <a href="/novel/products" class="btn-novel btn-novel-primary">
          <i class="fas fa-shopping-bag"></i> Shop Collection
        </a>
        <a href="/novel/about" class="btn-novel btn-novel-white">
          Our Story
        </a>
      </div>
      <div class="hero-badges fade-up d3">
        <span class="hero-badge"><i class="fas fa-leaf"></i> Natural</span>
        <span class="hero-badge"><i class="fas fa-heart"></i> Cruelty-Free</span>
        <span class="hero-badge"><i class="fas fa-recycle"></i> Eco-Friendly</span>
      </div>
    </div>
  </div>
</section>

<!-- ══ TRUST BAR ═══════════════════════════════════════ -->
<div class="trust-bar">
  <div class="trust-bar-inner">
    <div class="trust-item"><i class="fas fa-leaf"></i> 100% Natural Ingredients</div>
    <div class="trust-item"><i class="fas fa-flask"></i> Zero Harmful Chemicals</div>
    <div class="trust-item"><i class="fas fa-heart"></i> Cruelty-Free Certified</div>
    <div class="trust-item"><i class="fas fa-recycle"></i> Eco-Friendly Packaging</div>
    <div class="trust-item"><i class="fas fa-shield-halved"></i> Dermatologist Tested</div>
  </div>
</div>

<!-- ══ FEATURED PRODUCTS ═══════════════════════════════ -->
<section class="products-section">
  <div class="container" style="max-width:1240px;">
    <div class="section-head text-center mb-5">
      <div class="section-tag">
        <i class="fas fa-star"></i> Featured
      </div>
      <h2 class="section-title">Our bestsellers</h2>
      <p class="section-sub">
        The products your skin will thank you for — tried, tested, and loved.
      </p>
    </div>

    <?php if ($products): ?>
    <div class="row g-3">
      <?php foreach ($products as $p):
        $img   = $p['image_url'] ? 'images/products/' . $p['image_url'] : 'images/default-product.jpg';
        $inStock = $p['stock_quantity'] > 0;
      ?>
      <div class="col-6 col-md-4 col-lg-3">
        <div class="pcard">
          <?php if (!$inStock): ?>
            <span class="pcard-label sold-out">Sold Out</span>
          <?php elseif ($p['stock_quantity'] <= 5): ?>
            <span class="pcard-label low">Low Stock</span>
          <?php else: ?>
            <span class="pcard-label">Natural</span>
          <?php endif; ?>

          <div class="pcard-img">
            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
            <?php if ($inStock): ?>
            <div class="pcard-overlay">
              <a href="/novel/product/<?= $p['id'] ?>" class="pcard-overlay-btn">
                <i class="fas fa-eye me-1"></i> Quick View
              </a>
            </div>
            <?php endif; ?>
          </div>

          <div class="pcard-body">
            <div class="pcard-cat"><i class="fas fa-leaf me-1"></i>Natural</div>
            <div class="pcard-name"><?= htmlspecialchars($p['name']) ?></div>
            <div class="pcard-price"><span class="cur">RS</span><?= number_format($p['price'], 2) ?></div>
            <?php if ($inStock): ?>
              <div class="pcard-stock <?= $p['stock_quantity']<=5?'low-s':'' ?>">
                <i class="fas fa-check-circle me-1"></i>
                <?= $p['stock_quantity']<=5 ? 'Only '.$p['stock_quantity'].' left' : 'In Stock' ?>
              </div>
            <?php else: ?>
              <div class="pcard-stock none"><i class="fas fa-times-circle me-1"></i>Out of Stock</div>
            <?php endif; ?>
            <div class="pcard-actions">
              <a href="/novel/product/<?= $p['id'] ?>" class="btn-detail"><i class="fas fa-eye"></i> Details</a>
              <?php if ($inStock): ?>
              <a href="/novel/product/<?= $p['id'] ?>" class="btn-cart"><i class="fas fa-cart-plus"></i> Add</a>
              <?php else: ?>
              <button class="btn-cart" disabled style="opacity:.4;cursor:not-allowed;"><i class="fas fa-ban"></i></button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="text-center" style="color:var(--c-gray-400);">No products yet.</p>
    <?php endif; ?>

    <div class="text-center mt-5">
      <a href="/novel/products" class="btn-novel btn-novel-outline">
        View All Products <i class="fas fa-arrow-right ms-1"></i>
      </a>
    </div>
  </div>
</section>

<!-- ══ CATEGORIES ══════════════════════════════════════ -->
<?php if ($categories): ?>
<section class="categories-section">
  <div class="container" style="max-width:1240px;">

    <!-- Header row -->
    <div style="display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:2.25rem;">
      <div>
        <div class="section-tag green"><i class="fas fa-tags"></i> Browse</div>
        <h2 class="section-title" style="margin-bottom:0;">Shop by category</h2>
      </div>
      <div style="display:flex;gap:.6rem;align-items:center;">
        <button class="cat-nav-btn" id="catPrev" aria-label="Previous">
          <i class="fas fa-chevron-left"></i>
        </button>
        <button class="cat-nav-btn" id="catNext" aria-label="Next">
          <i class="fas fa-chevron-right"></i>
        </button>
        <a href="/novel/products"
           style="display:inline-flex;align-items:center;gap:.4rem;font-size:.82rem;
                  font-weight:600;color:var(--c-sage);text-decoration:none;
                  padding:.5rem 1rem;border-radius:8px;background:var(--c-sage-light);
                  transition:var(--tr);"
           onmouseover="this.style.background='var(--c-sage)';this.style.color='#fff'"
           onmouseout="this.style.background='var(--c-sage-light)';this.style.color='var(--c-sage)'">
          View All <i class="fas fa-arrow-right"></i>
        </a>
      </div>
    </div>

    <!-- Scrollable cards -->
    <div class="cat-scroll-track" id="catTrack">
      <?php foreach ($categories as $cat): ?>
      <a href="/novel/products/category/<?= $cat['id'] ?>" class="cat-scroll-card">
        <div class="cat-scroll-icon">
          <i class="fas fa-spa"></i>
        </div>
        <h5><?= htmlspecialchars($cat['name']) ?></h5>
        <p><?= htmlspecialchars(substr($cat['description'] ?? '', 0, 55)) ?></p>
      </a>
      <?php endforeach; ?>

      <!-- "All products" card at the end -->
      <a href="/novel/products" class="cat-scroll-card"
         style="border-style:dashed;">
        <div class="cat-scroll-icon" style="background:var(--c-gray-100);color:var(--c-gray-400);">
          <i class="fas fa-th-large"></i>
        </div>
        <h5 style="color:var(--c-gray-600);">All Products</h5>
        <p>Browse everything</p>
      </a>
    </div>

  </div>
</section>

<script>
(function(){
  var track = document.getElementById('catTrack');
  var cardW = 200 + 18; // card width + gap
  document.getElementById('catPrev')?.addEventListener('click', function(){
    track.scrollBy({ left: -cardW * 2, behavior: 'smooth' });
  });
  document.getElementById('catNext')?.addEventListener('click', function(){
    track.scrollBy({ left: cardW * 2, behavior: 'smooth' });
  });
})();
</script>
<?php endif; ?>

<!-- ══ BRAND PROMISE ═══════════════════════════════════ -->
<section class="brand-promise">
  <div class="container" style="max-width:1240px;">
    <div class="text-center mb-5">
      <div class="section-tag"><i class="fas fa-award"></i> Why Choose Us</div>
      <h2 class="section-title">Our promise to you</h2>
      <p class="section-sub" style="color:rgba(255,255,255,.5);">Everything we make starts with a commitment to your skin and the planet.</p>
    </div>
    <div class="row g-3">
      <?php
      $promises = [
        ['fas fa-leaf',         'Pure Ingredients',   'Sourced from organic farms. No fillers, no synthetics.'],
        ['fas fa-flask',        'Zero Chemicals',      'We never use parabens, sulfates or artificial preservatives.'],
        ['fas fa-heart',        'Cruelty-Free',        'None of our products are tested on animals. Ever.'],
        ['fas fa-recycle',      'Eco-Friendly',        'Recyclable packaging and sustainable production methods.'],
        ['fas fa-shield-halved','Dermatologist Tested','Safe for all skin types, including sensitive skin.'],
        ['fas fa-truck-fast',   'Fast Delivery',       'Orders dispatched within 24 hours across Sri Lanka.'],
      ];
      foreach ($promises as $pr): ?>
      <div class="col-6 col-md-4 col-lg-2" style="--bs-gutter-x:1rem;">
        <div class="promise-card">
          <div class="promise-icon"><i class="fas <?= $pr[0] ?>"></i></div>
          <h5><?= $pr[1] ?></h5>
          <p><?= $pr[2] ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php renderFooter(); ?>
