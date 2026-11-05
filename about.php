<?php
require_once 'includes/user_manager.php';
require_once 'includes/cart_manager.php';
require_once 'includes/nav_helper.php';

SessionManager::init();
$userId    = SessionManager::get('user_id');
$cartCount = (new CartManager())->getCartCount($userId);

renderHead('About Us — Novel Cosmetics');
renderNav('about', $cartCount);
?>

<!-- ══ HERO ═════════════════════════════════════════════ -->
<section class="about-hero">
  <div class="about-hero-inner container" style="max-width:1240px;padding:0 1.5rem;">
    <div class="col-lg-7">
      <div class="section-tag fade-up"><i class="fas fa-leaf"></i> Our Story</div>
      <h1 class="fade-up d1">Made with nature,<br>made for you.</h1>
      <p class="fade-up d2">We believe beauty should never cost the earth — literally. Novel Cosmetics was born to prove that pure, natural skincare can be powerful, affordable, and kind to our planet.</p>
    </div>
  </div>
</section>

<!-- ══ STORY ═════════════════════════════════════════════ -->
<section class="about-story">
  <div class="container" style="max-width:1240px;">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="section-tag green"><i class="fas fa-clock"></i> Since 2020</div>
        <h2 class="section-title">How we started</h2>
        <p style="color:var(--c-gray-600);line-height:1.8;margin-bottom:1rem;">Novel Cosmetics was founded with one simple mission: to give everyone access to high-quality, chemical-free beauty products that work. We started in a small kitchen, experimenting with organic botanicals and natural extracts.</p>
        <p style="color:var(--c-gray-600);line-height:1.8;margin-bottom:2rem;">Today, every product is still handcrafted with the same care — because we believe the best skincare starts with knowing exactly what goes into it.</p>
        <a href="/novel/products" class="btn-novel btn-novel-primary">
          <i class="fas fa-shopping-bag"></i> Shop Our Products
        </a>
      </div>
      <div class="col-lg-6">
        <div class="row g-3">
          <?php
          $stats = [
            ['2020','Year Founded'],['100%','Natural'],
            ['50+', 'Products'],   ['1000+','Happy Customers'],
          ];
          foreach ($stats as $s): ?>
          <div class="col-6">
            <div class="stat-box">
              <h3><?= $s[0] ?></h3>
              <p><?= $s[1] ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══ VALUES ════════════════════════════════════════════ -->
<section class="values-section">
  <div class="container" style="max-width:1240px;">
    <div class="text-center mb-5">
      <div class="section-tag"><i class="fas fa-star"></i> Core Values</div>
      <h2 class="section-title">What we stand for</h2>
    </div>
    <div class="row g-3">
      <?php
      $vals = [
        ['Purity',       'We use only the finest ingredients with full transparency. No hidden chemicals.'],
        ['Integrity',    'We stand behind every product we sell with honest marketing and real results.'],
        ['Sustainability','Our packaging is recyclable and our production is designed to minimise waste.'],
        ['Compassion',   'Cruelty-free always. We will never compromise on animal welfare.'],
      ];
      foreach ($vals as $v): ?>
      <div class="col-sm-6 col-lg-3">
        <div class="value-card">
          <h5><?= $v[0] ?></h5>
          <p><?= $v[1] ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══ WHY US ════════════════════════════════════════════ -->
<section class="brand-promise">
  <div class="container" style="max-width:1240px;">
    <div class="text-center mb-5">
      <div class="section-tag"><i class="fas fa-award"></i> Why Choose Novel</div>
      <h2 class="section-title">The Novel difference</h2>
      <p class="section-sub" style="color:rgba(255,255,255,.5);">Six reasons thousands of customers trust us with their skincare.</p>
    </div>
    <div class="row g-3">
      <?php
      $features = [
        ['fas fa-leaf',         'Natural Ingredients',   'Sourced from organic farms with full traceability.'],
        ['fas fa-shield-halved','Safe & Gentle',          'Formulated for all skin types including sensitive.'],
        ['fas fa-recycle',      'Eco Packaging',          'Minimalist, recyclable packaging we are proud of.'],
        ['fas fa-heart',        'Cruelty-Free',           'Certified cruelty-free — always.'],
        ['fas fa-award',        'Quality Assured',        'Rigorous batch testing before every product ships.'],
        ['fas fa-truck-fast',   'Fast Shipping',          'Same-day dispatch on orders placed before 3pm.'],
      ];
      foreach ($features as $f): ?>
      <div class="col-6 col-md-4 col-lg-2" style="--bs-gutter-x:1rem;">
        <div class="promise-card">
          <div class="promise-icon"><i class="fas <?= $f[0] ?>"></i></div>
          <h5><?= $f[1] ?></h5>
          <p><?= $f[2] ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php renderFooter(); ?>
