<?php
/**
 * Admin Layout Helper
 * Usage: adminHead($title); — outputs everything up to <body>
 *        adminSidebar($activePage); — outputs sidebar + topbar
 *        adminFoot(); — closes layout + outputs scripts
 */

function adminHead($title = 'Admin Panel') {
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($title) . ' — Novel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<div class="admin-wrap">';
}

function adminSidebar($activePage = 'dashboard') {
    $user = SessionManager::get('username');
    $initial = strtoupper(substr($user ?: 'A', 0, 1));

    $nav = [
        'dashboard'  => ['icon' => 'fa-gauge-high',   'label' => 'Dashboard',  'href' => 'index.php'],
        'products'   => ['icon' => 'fa-box',           'label' => 'Products',   'href' => 'products.php'],
        'categories' => ['icon' => 'fa-tags',          'label' => 'Categories', 'href' => 'categories.php'],
        'users'      => ['icon' => 'fa-users',         'label' => 'Users',      'href' => 'users.php'],
    ];

    $pages = [
        'dashboard'  => 'Dashboard',
        'products'   => 'Products',
        'categories' => 'Categories',
        'users'      => 'Users',
    ];
    $pageTitle = $pages[$activePage] ?? 'Admin';

    echo '<div class="admin-sidebar" id="adminSidebar">
        <div class="sb-brand">
            <div class="sb-brand-icon"><i class="fas fa-leaf"></i></div>
            <div class="sb-brand-text">
                <strong>Novel Cosmetics</strong>
                <span>Admin Panel</span>
            </div>
        </div>
        <div class="sb-nav">';

    echo '<div class="sb-label">Main Menu</div>';

    foreach ($nav as $key => $item) {
        $active = $activePage === $key ? ' active' : '';
        echo '<a href="' . $item['href'] . '" class="' . trim($active) . '">
                <i class="fas ' . $item['icon'] . '"></i>
                ' . $item['label'] . '
              </a>';
    }

    echo '</div>
        <div class="sb-footer">
            <a href="/novel/home"><i class="fas fa-store"></i> Visit Store</a>
            <a href="/novel/logout"><i class="fas fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>

    <!-- Overlay -->
    <div class="sb-overlay" id="sbOverlay"></div>

    <!-- Main -->
    <div class="admin-main" id="adminMain">
        <header class="admin-topbar">
            <button class="tb-toggle" id="tbToggle"><i class="fas fa-bars"></i></button>
            <div class="tb-breadcrumb">
                <span>Novel Admin</span>
                <i class="fas fa-chevron-right"></i>
                <span class="crumb-page">' . $pageTitle . '</span>
            </div>
            <div class="tb-actions">
                <a href="/novel/home" class="abtn abtn-ghost abtn-sm" style="text-decoration:none;">
                    <i class="fas fa-arrow-up-right-from-square"></i> Store
                </a>
                <div class="tb-user">
                    <div class="av">' . $initial . '</div>
                    ' . htmlspecialchars($user) . '
                </div>
            </div>
        </header>
        <div class="admin-body">';
}

function adminFoot() {
    echo '        </div><!-- /admin-body -->
    </div><!-- /admin-main -->
</div><!-- /admin-wrap -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function(){
    const sidebar  = document.getElementById("adminSidebar");
    const overlay  = document.getElementById("sbOverlay");
    const toggle   = document.getElementById("tbToggle");
    if (!toggle) return;
    function open()  { sidebar.classList.add("open");  overlay.classList.add("show"); }
    function close() { sidebar.classList.remove("open"); overlay.classList.remove("show"); }
    toggle.addEventListener("click", () => sidebar.classList.contains("open") ? close() : open());
    overlay.addEventListener("click", close);
    window.addEventListener("resize", () => { if(window.innerWidth > 1024) close(); });
})();
</script>
</body></html>';
}
