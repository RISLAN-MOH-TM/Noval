<?php
/**
 * Shared navbar + footer — Novel Cosmetics
 * renderHead($title, $extraCss)
 * renderNav($activePage, $cartCount)
 * renderFooter()
 */

function renderHead(string $title = "Novel Cosmetics", string $extraCss = ""): void {
    echo '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<base href="/novel/">
<link rel="icon" href="favicon.png" type="image/png">
<title>' . htmlspecialchars($title) . '</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=New+York:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">' . $extraCss . '
</head>
<body>';
}

function renderNav(string $active = 'home', int $cartCount = 0): void {
    $links = [
        'home'     => ['href' => '/novel/home',    'label' => 'Home'],
        'products' => ['href' => '/novel/products', 'label' => 'Products'],
        'about'    => ['href' => '/novel/about',    'label' => 'About'],
        'contact'  => ['href' => '/novel/contact',  'label' => 'Contact'],
    ];
    $isLoggedIn = SessionManager::isLoggedIn();
    $isAdmin    = SessionManager::isAdmin();
    $username   = SessionManager::get('username');
    $initial    = $username ? strtoupper(substr($username, 0, 1)) : 'U';

    /* ── Desktop navbar ──────────────────────────────── */
    echo '
<header class="site-header" id="siteHeader">
  <div class="nav-inner">
    <a class="nav-logo" href="/novel/home">
      <img src="images/logo.png" alt="Novel Cosmetics">
    </a>

    <!-- Desktop links -->
    <ul class="nav-links" id="desktopNav">';

    foreach ($links as $key => $link) {
        $cls = $active === $key ? ' active' : '';
        echo '<li><a href="' . $link['href'] . '" class="' . trim($cls) . '">' . $link['label'] . '</a></li>';
    }

    // Cart
    echo '<li><a href="/novel/cart" class="nav-cart-btn" title="Cart">
        <i class="fas fa-shopping-bag"></i>';
    if ($cartCount > 0) echo '<span class="nav-cart-badge">' . $cartCount . '</span>';
    echo '</a></li>';

    // Admin
    if ($isAdmin) {
        echo '<li><a href="/novel/admin/index.php" class="nav-admin-btn"><i class="fas fa-crown"></i> Admin</a></li>';
    }

    // User / Login
    if ($isLoggedIn) {
        echo '<li class="nav-user-dropdown" id="userDropdownLi">
          <button class="nav-user-btn" id="userDropdownBtn" type="button"
                  aria-expanded="false" aria-haspopup="true">' . $initial . '</button>
        </li>';
    }

    echo '</ul>

    <!-- Hamburger — only visible on mobile via CSS -->
    <button class="nav-toggle" id="navToggle" aria-label="Toggle menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>';

    /* ── Mobile slide-down menu — separate from desktop nav ── */
    echo '
<div id="mobileMenu" aria-hidden="true" style="
    display:none;
    position:fixed;
    top:64px; left:0; right:0; bottom:0;
    background:var(--c-cream,#faf8f5);
    z-index:10000;
    overflow-y:auto;
    padding:1rem;
    flex-direction:column;
    gap:.25rem;
    border-top:2px solid var(--c-sage,#5a7a5a);
    box-shadow:0 8px 32px rgba(0,0,0,.15);
">';

    // Nav links
    foreach ($links as $key => $link) {
        $isAct = $active === $key;
        echo '<a href="' . $link['href'] . '" style="
            display:flex;align-items:center;gap:.75rem;
            padding:.875rem 1rem;border-radius:10px;
            font-size:1rem;font-weight:' . ($isAct ? '700' : '500') . ';
            color:' . ($isAct ? 'var(--c-sage,#5a7a5a)' : 'var(--c-gray-700,#4a3f35)') . ';
            background:' . ($isAct ? 'var(--c-sage-light,#f0f5f0)' : 'transparent') . ';
            border-left:3px solid ' . ($isAct ? 'var(--c-sage,#5a7a5a)' : 'transparent') . ';
            text-decoration:none;transition:background .2s,color .2s;">
            <i class="fas fa-' . ['home'=>'house','products'=>'box','about'=>'info-circle','contact'=>'envelope'][$key] . '" style="width:18px;text-align:center;opacity:.7;"></i>
            ' . $link['label'] . '
        </a>';
    }

    // Cart
    echo '<a href="/novel/cart" style="
        display:flex;align-items:center;gap:.75rem;
        padding:.875rem 1rem;border-radius:10px;
        font-size:1rem;font-weight:500;
        color:var(--c-gray-700,#4a3f35);background:transparent;
        text-decoration:none;border-left:3px solid transparent;">
        <i class="fas fa-shopping-bag" style="width:18px;text-align:center;opacity:.7;"></i>
        Cart' . ($cartCount > 0 ? ' <span style="margin-left:auto;background:var(--c-rose,#c0392b);color:#fff;font-size:.7rem;font-weight:700;padding:.1rem .5rem;border-radius:20px;">' . $cartCount . '</span>' : '') . '
    </a>';

    // Admin
    if ($isAdmin) {
        echo '<a href="/novel/admin/index.php" style="
            display:flex;align-items:center;gap:.75rem;
            padding:.875rem 1rem;border-radius:10px;
            font-size:1rem;font-weight:600;
            color:var(--c-rose,#c0392b);background:#fff0f3;
            text-decoration:none;border-left:3px solid transparent;">
            <i class="fas fa-crown" style="width:18px;text-align:center;"></i>
            Admin Panel
        </a>';
    }

    // Divider
    echo '<div style="height:1px;background:var(--c-gray-200,#e5ddd4);margin:.5rem 0;"></div>';

    // User or nothing
    if ($isLoggedIn) {
        echo '<a href="/novel/profile" style="
            display:flex;align-items:center;gap:.75rem;
            padding:.875rem 1rem;border-radius:10px;
            font-size:1rem;font-weight:500;color:var(--c-gray-700,#4a3f35);
            text-decoration:none;border-left:3px solid transparent;">
            <span style="width:32px;height:32px;border-radius:50%;
                         background:linear-gradient(135deg,var(--c-sage,#5a7a5a),var(--c-sage-dark,#3d5c3d));
                         color:#fff;display:flex;align-items:center;justify-content:center;
                         font-size:.85rem;font-weight:700;flex-shrink:0;">' . $initial . '</span>
            My Profile
        </a>
        <a href="/novel/logout" style="
            display:flex;align-items:center;gap:.75rem;
            padding:.875rem 1rem;border-radius:10px;
            font-size:1rem;font-weight:500;color:var(--c-clay,#a0522d);
            background:var(--c-clay-light,#fdf0e8);text-decoration:none;
            border-left:3px solid transparent;margin-top:.25rem;">
            <i class="fas fa-right-from-bracket" style="width:18px;text-align:center;"></i>
            Logout
        </a>';
    } else {
        // Not logged in — no login button in mobile nav
    }

    echo '</div>

<!-- Overlay behind mobile menu -->
<div id="mobileOverlay" onclick="closeNav()" style="
    display:none;position:fixed;inset:0;
    background:rgba(0,0,0,.45);z-index:9999;"></div>';

    /* ── User dropdown (desktop) ── */
    if ($isLoggedIn) {
        echo '
<div class="nav-user-menu" id="userDropdownMenu" role="menu" aria-hidden="true">
  <div class="num-header">
    <div class="num-avatar">' . $initial . '</div>
    <div>
      <div class="num-name">' . htmlspecialchars($username) . '</div>
      <div class="num-role">' . ($isAdmin ? 'Administrator' : 'Customer') . '</div>
    </div>
  </div>
  <div class="num-divider"></div>
  <a href="/novel/profile" class="num-item"><i class="fas fa-user"></i> My Profile</a>
  <a href="/novel/cart" class="num-item"><i class="fas fa-shopping-bag"></i> My Cart' .
    ($cartCount > 0 ? ' <span class="num-badge">' . $cartCount . '</span>' : '') . '</a>' .
    ($isAdmin ? '<a href="/novel/admin/index.php" class="num-item"><i class="fas fa-crown"></i> Admin Panel</a>' : '') . '
  <div class="num-divider"></div>
  <a href="/novel/logout" class="num-item num-item-danger"><i class="fas fa-right-from-bracket"></i> Logout</a>
</div>';
    }
}

function renderFooter(): void {
    echo '<footer class="site-footer">
  <div class="footer-inner">
    <div class="footer-grid">
      <div class="footer-brand">
        <img src="images/logo.png" alt="Novel Cosmetics">
        <p>Pure natural cosmetics for healthy, glowing skin. Made with organic ingredients and a love for the environment.</p>
      </div>
      <div class="footer-col">
        <h6>Navigation</h6>
        <ul>
          <li><a href="/novel/home">Home</a></li>
          <li><a href="/novel/products">Products</a></li>
          <li><a href="/novel/about">About Us</a></li>
          <li><a href="/novel/contact">Contact</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h6>Policies</h6>
        <ul>
          <li><a href="#">Privacy Policy</a></li>
          <li><a href="#">Shipping Info</a></li>
          <li><a href="#">Returns</a></li>
          <li><a href="#">FAQ</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h6>Contact</h6>
        <div class="footer-contact-item"><i class="fas fa-map-marker-alt"></i><span>52/A, Deens Road, Akkaraipattu 32400</span></div>
        <div class="footer-contact-item"><i class="fas fa-phone"></i><span>0779191742</span></div>
        <div class="footer-contact-item"><i class="fas fa-envelope"></i><span>info@novelcosmetics.com</span></div>
        ' . (!SessionManager::isLoggedIn() ? '
        <a href="/novel/login" class="footer-signin-btn" style="margin-top:1rem;">
          <i class="fas fa-sign-in-alt"></i> Sign In
        </a>' : '') . '
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; 2026 Novel Cosmetics. All rights reserved.</p>
      <div class="footer-bottom-right">
        <div class="footer-social">
          <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
          <a href="#" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
        </div>
      </div>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
  /* ── Scroll shadow ── */
  var hdr = document.getElementById("siteHeader");
  if (hdr) window.addEventListener("scroll", function () {
    hdr.classList.toggle("scrolled", window.scrollY > 10);
  }, { passive: true });

  /* ── MOBILE NAV ── */
  var btn     = document.getElementById("navToggle");
  var menu    = document.getElementById("mobileMenu");
  var overlay = document.getElementById("mobileOverlay");

  function openNav() {
    menu.style.display    = "flex";
    overlay.style.display = "block";
    btn.setAttribute("aria-expanded", "true");
    menu.setAttribute("aria-hidden", "false");
    document.body.style.overflow = "hidden";
  }

  window.closeNav = function () {
    menu.style.display    = "none";
    overlay.style.display = "none";
    btn.setAttribute("aria-expanded", "false");
    menu.setAttribute("aria-hidden", "true");
    document.body.style.overflow = "";
  };

  if (btn) btn.addEventListener("click", function () {
    menu.style.display === "flex" ? closeNav() : openNav();
  });

  /* Close when a menu link is clicked */
  if (menu) menu.querySelectorAll("a").forEach(function (a) {
    a.addEventListener("click", closeNav);
  });

  /* Close on resize to desktop */
  window.addEventListener("resize", function () {
    if (window.innerWidth > 768) closeNav();
  });

  /* ── Desktop user dropdown ── */
  var dropBtn  = document.getElementById("userDropdownBtn");
  var dropMenu = document.getElementById("userDropdownMenu");

  if (dropBtn && dropMenu) {
    function positionMenu() {
      var rect = dropBtn.getBoundingClientRect();
      dropMenu.style.top   = (rect.bottom + window.scrollY + 8) + "px";
      dropMenu.style.left  = "auto";
      dropMenu.style.right = (window.innerWidth - rect.right) + "px";
    }
    function openDrop() {
      positionMenu();
      dropMenu.classList.add("open");
      dropBtn.setAttribute("aria-expanded", "true");
    }
    function closeDrop() {
      dropMenu.classList.remove("open");
      dropBtn.setAttribute("aria-expanded", "false");
    }
    dropBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      dropMenu.classList.contains("open") ? closeDrop() : openDrop();
    });
    document.addEventListener("click", function (e) {
      if (!dropMenu.contains(e.target) && e.target !== dropBtn) closeDrop();
    });
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") closeDrop();
    });
    window.addEventListener("scroll",  positionMenu, { passive: true });
    window.addEventListener("resize",  positionMenu, { passive: true });
  }
})();
</script>';
}
