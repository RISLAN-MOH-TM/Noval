<?php
require_once 'includes/user_manager.php';
require_once 'includes/cart_manager.php';
require_once 'includes/nav_helper.php';

SessionManager::init();
$userId    = SessionManager::get('user_id');
$cartCount = (new CartManager())->getCartCount($userId);

$msgOk = $msgErr = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $msg     = trim($_POST['message'] ?? '');
    if ($name && $email && $subject && $msg) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msgOk = "Thank you, $name! We'll be in touch soon.";
        } else {
            $msgErr = 'Please enter a valid email address.';
        }
    } else {
        $msgErr = 'Please fill in all fields.';
    }
}

renderHead('Contact Us — Novel Cosmetics');
renderNav('contact', $cartCount);
?>

<!-- ══ HERO ═════════════════════════════════════════════ -->
<section class="contact-hero">
  <div class="contact-hero-inner container" style="max-width:1240px;padding:0 1.5rem;">
    <div class="col-lg-6">
      <div class="section-tag fade-up"><i class="fas fa-envelope"></i> Get in Touch</div>
      <h1 class="fade-up d1">We'd love to<br>hear from you.</h1>
      <p class="fade-up d2">Questions about our products? Need help with an order? Reach out — we usually reply within a few hours.</p>
    </div>
  </div>
</section>

<!-- ══ MAIN ══════════════════════════════════════════════ -->
<section class="contact-main">
  <div class="container" style="max-width:1240px;">
    <div class="row g-4">

      <!-- Info -->
      <div class="col-lg-4">
        <div class="contact-info-card">
          <h5 style="font-weight:800;color:var(--c-gray-900);margin-bottom:1.5rem;">Contact Information</h5>
          <div class="c-info-item">
            <div class="c-info-icon"><i class="fas fa-phone"></i></div>
            <div><h6>Phone</h6><p>0779191742</p></div>
          </div>
          <div class="c-info-item">
            <div class="c-info-icon"><i class="fas fa-envelope"></i></div>
            <div><h6>Email</h6><p>info@novelcosmetics.com</p></div>
          </div>
          <div class="c-info-item">
            <div class="c-info-icon"><i class="fas fa-map-marker-alt"></i></div>
            <div><h6>Address</h6><p>52/A, Deens Road, T/D-5,<br>Akkaraipattu 32400</p></div>
          </div>
          <div class="c-info-item">
            <div class="c-info-icon"><i class="fas fa-clock"></i></div>
            <div><h6>Hours</h6><p>Mon–Sat: 8am – 6pm</p></div>
          </div>

          <div style="margin-top:1.5rem;">
            <p style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--c-gray-400);margin-bottom:.75rem;">Also reach us on</p>
            <div style="display:flex;gap:.6rem;">
              <a href="https://wa.me/94779191742" target="_blank"
                 style="width:38px;height:38px;border-radius:10px;background:#dcfce7;color:#15803d;display:flex;align-items:center;justify-content:center;font-size:1.1rem;transition:var(--tr);"
                 onmouseover="this.style.background='#15803d';this.style.color='#fff'"
                 onmouseout="this.style.background='#dcfce7';this.style.color='#15803d'">
                <i class="fab fa-whatsapp"></i>
              </a>
              <a href="#" style="width:38px;height:38px;border-radius:10px;background:var(--c-purple-light);color:var(--c-purple);display:flex;align-items:center;justify-content:center;font-size:1rem;transition:var(--tr);"
                 onmouseover="this.style.background='var(--c-purple)';this.style.color='#fff'"
                 onmouseout="this.style.background='var(--c-purple-light)';this.style.color='var(--c-purple)'">
                <i class="fab fa-instagram"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Form -->
      <div class="col-lg-8">
        <div class="contact-form-card">
          <h5 style="font-weight:800;color:var(--c-gray-900);margin-bottom:1.5rem;">Send us a message</h5>

          <?php if ($msgOk): ?>
          <div class="alert-novel success"><i class="fas fa-check-circle"></i><?= htmlspecialchars($msgOk) ?></div>
          <?php endif; ?>
          <?php if ($msgErr): ?>
          <div class="alert-novel danger"><i class="fas fa-exclamation-circle"></i><?= htmlspecialchars($msgErr) ?></div>
          <?php endif; ?>

          <form method="POST">
            <div class="row g-3 mb-0">
              <div class="col-sm-6">
                <label class="form-label-custom">Your Name *</label>
                <input type="text" name="name" class="form-control-custom" placeholder="Full name" required>
              </div>
              <div class="col-sm-6">
                <label class="form-label-custom">Email *</label>
                <input type="email" name="email" class="form-control-custom" placeholder="you@example.com" required>
              </div>
              <div class="col-12">
                <label class="form-label-custom">Subject *</label>
                <input type="text" name="subject" class="form-control-custom" placeholder="How can we help?" required>
              </div>
              <div class="col-12">
                <label class="form-label-custom">Message *</label>
                <textarea name="message" class="form-control-custom" placeholder="Write your message here..." required></textarea>
              </div>
              <div class="col-12">
                <button type="submit" class="btn-novel btn-novel-primary" style="width:100%;justify-content:center;">
                  <i class="fas fa-paper-plane"></i> Send Message
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</section>

<?php renderFooter(); ?>
