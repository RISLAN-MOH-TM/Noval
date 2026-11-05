<?php
require_once 'includes/user_manager.php';

SessionManager::init();
if (SessionManager::isLoggedIn()) { header('Location: /novel/home'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username && $password) {
        $userManager = new UserManager();
        $user = $userManager->authenticateUser($username, $password);
        if ($user) {
            SessionManager::set('user_id',   $user['id']);
            SessionManager::set('username',  $user['username']);
            SessionManager::set('user_type', $user['user_type']);
            $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '/novel/home';
            header('Location: ' . $redirect); exit;
        } else {
            $error = 'Incorrect username or password. Please try again.';
        }
    } else {
        $error = 'Please fill in both fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<base href="/novel/">
<link rel="icon" href="favicon.png" type="image/png">
<title>Sign In — Novel Cosmetics</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="css/style.css">
<style>
  body { padding-top: 0 !important; }

  /* ── Suppress native browser password reveal icon ── */
  input[type="password"]::-ms-reveal,
  input[type="password"]::-ms-clear,
  input[type="password"]::-webkit-credentials-auto-fill-button,
  input[type="password"]::-webkit-textfield-decoration-container { display: none !important; }

  /* ── Password field wrapper ── */
  .pwd-wrap { position: relative; margin-bottom: 1rem; }
  .pwd-wrap .lf-input { padding-right: 3rem; margin-bottom: 0; }

  /* ── Custom eye toggle button ── */
  .pwd-toggle {
    position: absolute;
    right: .9rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    padding: .25rem;
    border-radius: 6px;
    color: var(--c-gray-400);
    font-size: .95rem;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color .2s;
  }
  .pwd-toggle:hover { color: var(--c-sage); }
  .pwd-toggle:focus { outline: none; box-shadow: 0 0 0 2px rgba(90,122,90,.2); border-radius: 6px; }
</style>
</head>
<body style="padding-top:0;">

<div class="login-page">

  <!-- ── LEFT VISUAL ─────────────────────────────────── -->
  <div class="login-visual">
    <!-- Floating botanical blobs -->
    <div style="position:absolute;top:-80px;right:-80px;width:300px;height:300px;border-radius:50%;
                background:radial-gradient(circle,rgba(90,122,90,.18),transparent 70%);pointer-events:none;"></div>
    <div style="position:absolute;bottom:60px;right:30px;width:180px;height:180px;border-radius:50%;
                background:radial-gradient(circle,rgba(200,169,126,.12),transparent 70%);pointer-events:none;"></div>

    <div class="login-visual-badge">
      <span></span> 100% Natural Cosmetics
    </div>
    <h2>Beauty from<br><em>the earth</em>,<br>for you.</h2>
    <p>Sign in to discover handcrafted cosmetics made with pure botanical ingredients — zero chemicals, pure results.</p>

    <!-- Trust pills -->
    <div style="display:flex;gap:.6rem;flex-wrap:wrap;margin-top:2rem;position:relative;">
      <?php foreach (['🌿 Organic','🐰 Cruelty-Free','♻️ Eco-Friendly'] as $t): ?>
      <span style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);
                   color:rgba(255,255,255,.6);font-size:.72rem;padding:.3rem .75rem;border-radius:8px;">
        <?= $t ?>
      </span>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ── RIGHT FORM ──────────────────────────────────── -->
  <div class="login-form-side">
    <div class="login-form-box">

      <img src="images/logo.png" alt="Novel Cosmetics" class="lf-logo">

      <h3>Welcome back</h3>
      <p class="lf-sub">Sign in to your account to continue.</p>

      <?php if ($error): ?>
      <div style="display:flex;align-items:center;gap:.65rem;padding:.8rem 1rem;
                  background:#fdf0e8;border:1px solid #f5cba7;border-radius:10px;
                  color:#8b4513;font-size:.85rem;font-weight:500;margin-bottom:1.25rem;">
        <i class="fas fa-exclamation-circle"></i>
        <?= htmlspecialchars($error) ?>
      </div>
      <?php endif; ?>

      <form method="POST">
        <div>
          <label class="lf-label">Username or Email</label>
          <input type="text" name="username" class="lf-input"
                 placeholder="Enter your username" required
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>
        <div>
          <label class="lf-label">Password</label>
          <div class="pwd-wrap">
            <input type="password" name="password" id="pwdField" class="lf-input"
                   placeholder="••••••••" required autocomplete="current-password">
            <button type="button" class="pwd-toggle" id="pwdToggle"
                    onclick="togglePwd()" aria-label="Toggle password visibility">
              <i class="fas fa-eye" id="pwdEye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="lf-btn">
          <i class="fas fa-arrow-right-to-bracket"></i> Sign In
        </button>
      </form>

      <p class="lf-back">
        <a href="/novel/home"><i class="fas fa-arrow-left me-1"></i>Back to store</a>
      </p>
    </div>
  </div>

</div>

<script>
function togglePwd() {
  const f = document.getElementById('pwdField');
  const e = document.getElementById('pwdEye');
  if (f.type === 'password') { f.type = 'text'; e.className = 'fas fa-eye-slash'; }
  else { f.type = 'password'; e.className = 'fas fa-eye'; }
}
</script>
</body>
</html>
