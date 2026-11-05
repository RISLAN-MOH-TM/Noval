<?php
require_once 'includes/product_manager.php';
require_once 'includes/user_manager.php';
require_once 'includes/cart_manager.php';
require_once 'includes/nav_helper.php';

SessionManager::init();

if (!SessionManager::isLoggedIn()) {
    header('Location: /novel/login');
    exit;
}

$userManager = new UserManager();
$userId      = SessionManager::get('user_id');
$user        = $userManager->getUserById($userId);
$cartCount   = (new CartManager())->getCartCount($userId);

// Order history — graceful fallback if OrderManager doesn't exist
$orders = [];
if (class_exists('OrderManager')) {
    try { 
        $result = (new OrderManager())->getUserOrders($userId);
        $orders = is_array($result) ? $result : [];
    }
    catch (Exception $e) { $orders = []; }
}

$msgOk = $msgErr = '';
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';

/* ── Profile update ─────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $result = $userManager->updateUser(
        $userId,
        trim($_POST['username']),
        trim($_POST['email']),
        trim($_POST['first_name']  ?? ''),
        trim($_POST['last_name']   ?? ''),
        trim($_POST['phone']       ?? ''),
        trim($_POST['address']     ?? ''),
        trim($_POST['city']        ?? ''),
        trim($_POST['state']       ?? ''),
        trim($_POST['zip_code']    ?? ''),
        trim($_POST['country']     ?? '')
    );
    if ($result) {
        $msgOk  = 'Profile updated successfully!';
        $user   = $userManager->getUserById($userId);
        SessionManager::set('username', $user['username']);
    } else {
        $msgErr = 'Failed to update profile. Please try again.';
    }
    $activeTab = 'profile';
}

/* ── Password change ────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $cur  = $_POST['current_password'] ?? '';
    $new  = $_POST['new_password']     ?? '';
    $conf = $_POST['confirm_password'] ?? '';

    if ($new !== $conf) {
        $msgErr = 'New passwords do not match.';
    } elseif (strlen($new) < 6) {
        $msgErr = 'Password must be at least 6 characters.';
    } else {
        $auth = $userManager->authenticateUser($user['username'], $cur);
        if ($auth && $auth['id'] == $userId) {
            $r = $userManager->updateUserPassword($userId, $new);
            $msgOk  = $r ? 'Password updated successfully!' : '';
            $msgErr = $r ? '' : 'Failed to update password.';
        } else {
            $msgErr = 'Current password is incorrect.';
        }
    }
    $activeTab = 'security';
}

$initial  = strtoupper(substr($user['username'], 0, 1));
$fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));

renderHead('My Profile — Novel Cosmetics');
renderNav('', $cartCount);

/* ── Helper: status badge ────────────────────────── */
function statusStyle($s) {
    return match(strtolower($s)) {
        'delivered'  => 'background:#ecfdf5;color:#059669;',
        'processing' => 'background:#eff6ff;color:#2563eb;',
        'shipped'    => 'background:#f0f9ff;color:#0891b2;',
        'pending'    => 'background:#fefce8;color:#ca8a04;',
        'cancelled'  => 'background:#fdf0e8;color:#a0522d;',
        default      => 'background:#f1f5f9;color:#64748b;',
    };
}
?>

<!-- ══ PROFILE HERO ════════════════════════════════ -->
<section style="background:linear-gradient(135deg,#1a1209 0%,#2d1e0f 55%,#1a2a18 100%);
                padding:3.5rem 0 2.5rem;position:relative;overflow:hidden;">
  <div style="position:absolute;inset:0;
       background:url('data:image/svg+xml,%3Csvg width=60 height=60 xmlns=http://www.w3.org/2000/svg%3E%3Ccircle cx=30 cy=30 r=1.5 fill=%23fff fill-opacity=.04/%3E%3C/svg%3E');
       pointer-events:none;"></div>
  <div style="position:relative;z-index:1;max-width:1100px;margin:0 auto;padding:0 1.5rem;
              display:flex;align-items:center;gap:1.75rem;flex-wrap:wrap;">
    <!-- Avatar -->
    <div style="width:80px;height:80px;border-radius:50%;flex-shrink:0;
                background:linear-gradient(135deg,var(--c-sage),var(--c-sage-dark));
                display:flex;align-items:center;justify-content:center;
                font-size:2rem;font-weight:700;color:#fff;
                box-shadow:0 6px 24px rgba(90,122,90,.35);">
      <?= $initial ?>
    </div>
    <div>
      <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;
                  color:rgba(255,255,255,.5);margin-bottom:.3rem;">My Account</div>
      <h1 style="font-family:'New York','Georgia',serif;font-size:clamp(1.6rem,3vw,2.2rem);
                 color:#fff;font-weight:600;margin:0 0 .3rem;letter-spacing:-.03em;">
        Hello, <?= htmlspecialchars($user['username']) ?>!
      </h1>
      <p style="color:rgba(255,255,255,.5);margin:0;font-size:.875rem;">
        <?= $fullName ?: $user['email'] ?>
        · Member since <?= date('M Y', strtotime($user['created_at'] ?? 'now')) ?>
      </p>
    </div>
    <!-- Quick stats -->
    <div style="margin-left:auto;display:flex;gap:1.5rem;flex-wrap:wrap;">
      <?php foreach ([
        [is_array($orders) ? count($orders) : 0, 'Orders'],
        [SessionManager::isAdmin() ? '👑' : '👤', SessionManager::isAdmin() ? 'Admin' : 'Customer'],
      ] as $s): ?>
      <div style="text-align:center;">
        <strong style="display:block;font-size:1.4rem;font-weight:800;color:#fff;line-height:1;">
          <?= $s[0] ?>
        </strong>
        <span style="font-size:.68rem;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:.06em;">
          <?= $s[1] ?>
        </span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══ BODY ════════════════════════════════════════ -->
<div style="background:var(--c-gray-50);min-height:60vh;padding:2.5rem 0 5rem;">
<div style="max-width:1100px;margin:0 auto;padding:0 1.5rem;">

  <!-- Alerts -->
  <?php if ($msgOk): ?>
  <div style="display:flex;align-items:center;gap:.7rem;padding:.9rem 1.25rem;
              background:var(--c-sage-light);border:1px solid var(--c-sage-mid);
              border-radius:12px;color:var(--c-sage);font-size:.875rem;font-weight:500;
              margin-bottom:1.5rem;">
    <i class="fas fa-check-circle"></i><?= htmlspecialchars($msgOk) ?>
  </div>
  <?php endif; ?>
  <?php if ($msgErr): ?>
  <div style="display:flex;align-items:center;gap:.7rem;padding:.9rem 1.25rem;
              background:var(--c-clay-light);border:1px solid #f5cba7;
              border-radius:12px;color:var(--c-clay);font-size:.875rem;font-weight:500;
              margin-bottom:1.5rem;">
    <i class="fas fa-exclamation-circle"></i><?= htmlspecialchars($msgErr) ?>
  </div>
  <?php endif; ?>

  <div style="display:grid;grid-template-columns:220px 1fr;gap:1.75rem;" id="profileLayout">

    <!-- ── SIDEBAR TABS ──────────────────────────── -->
    <aside>
      <div style="background:var(--c-white);border-radius:14px;
                  border:1px solid var(--c-gray-200);overflow:hidden;
                  box-shadow:var(--shadow-sm);position:sticky;top:82px;">
        <?php
        $tabs = [
          'profile'  => ['fas fa-user',        'Profile Details'],
          'orders'   => ['fas fa-shopping-bag', 'Order History'],
          'security' => ['fas fa-lock',         'Security'],
        ];
        foreach ($tabs as $key => [$icon, $label]):
          $active = $activeTab === $key;
        ?>
        <a href="?tab=<?= $key ?>"
           style="display:flex;align-items:center;gap:.75rem;
                  padding:.875rem 1.25rem;text-decoration:none;
                  font-size:.875rem;font-weight:<?= $active?'700':'500' ?>;
                  color:<?= $active?'var(--c-sage)':'var(--c-gray-700)' ?>;
                  background:<?= $active?'var(--c-sage-light)':'transparent' ?>;
                  border-left:3px solid <?= $active?'var(--c-sage)':'transparent' ?>;
                  border-bottom:1px solid var(--c-gray-100);
                  transition:var(--tr);"
           onmouseover="if('<?= $key ?>'!=='<?= $activeTab ?>'){this.style.background='var(--c-gray-50)';this.style.color='var(--c-sage)';}"
           onmouseout="if('<?= $key ?>'!=='<?= $activeTab ?>'){this.style.background='transparent';this.style.color='var(--c-gray-700)';}">
          <i class="fas <?= $icon ?>" style="width:16px;text-align:center;opacity:.75;"></i>
          <?= $label ?>
          <?php if ($key==='orders' && count($orders)>0): ?>
          <span style="margin-left:auto;background:var(--c-sage);color:#fff;
                       font-size:.65rem;font-weight:700;padding:.15rem .5rem;border-radius:20px;">
            <?= count($orders) ?>
          </span>
          <?php endif; ?>
        </a>
        <?php endforeach; ?>

        <!-- Logout -->
        <a href="/novel/logout"
           style="display:flex;align-items:center;gap:.75rem;padding:.875rem 1.25rem;
                  text-decoration:none;font-size:.875rem;font-weight:500;
                  color:var(--c-clay);border-left:3px solid transparent;transition:var(--tr);"
           onmouseover="this.style.background='var(--c-clay-light)';this.style.borderLeftColor='var(--c-clay)';"
           onmouseout="this.style.background='transparent';this.style.borderLeftColor='transparent';">
          <i class="fas fa-right-from-bracket" style="width:16px;text-align:center;"></i>
          Logout
        </a>
      </div>
    </aside>

    <!-- ── MAIN PANEL ────────────────────────────── -->
    <main>

      <!-- ══ PROFILE TAB ══════════════════════════ -->
      <?php if ($activeTab === 'profile'): ?>
      <div style="background:var(--c-white);border-radius:14px;border:1px solid var(--c-gray-200);
                  box-shadow:var(--shadow-sm);overflow:hidden;">
        <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--c-gray-100);
                    display:flex;align-items:center;gap:.6rem;">
          <i class="fas fa-user" style="color:var(--c-sage);"></i>
          <h2 style="margin:0;font-size:1.05rem;font-weight:700;color:var(--c-gray-800);">Profile Information</h2>
        </div>
        <div style="padding:1.75rem;">
          <form method="POST">
            <!-- Personal Info -->
            <p style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;
                      color:var(--c-gray-400);margin-bottom:.9rem;">Personal Details</p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
              <div>
                <label class="lf-label">Username *</label>
                <input type="text" name="username" class="lf-input"
                       value="<?= htmlspecialchars($user['username']) ?>" required>
              </div>
              <div>
                <label class="lf-label">Email *</label>
                <input type="email" name="email" class="lf-input"
                       value="<?= htmlspecialchars($user['email']) ?>" required>
              </div>
              <div>
                <label class="lf-label">First Name</label>
                <input type="text" name="first_name" class="lf-input"
                       value="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
                       placeholder="First name">
              </div>
              <div>
                <label class="lf-label">Last Name</label>
                <input type="text" name="last_name" class="lf-input"
                       value="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
                       placeholder="Last name">
              </div>
            </div>
            <div style="margin-bottom:1.5rem;">
              <label class="lf-label">Phone</label>
              <input type="tel" name="phone" class="lf-input"
                     value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                     placeholder="Phone number">
            </div>

            <!-- Address -->
            <p style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;
                      color:var(--c-gray-400);margin-bottom:.9rem;">Shipping Address</p>
            <div style="margin-bottom:1rem;">
              <label class="lf-label">Street Address</label>
              <textarea name="address" class="lf-input"
                        style="resize:vertical;min-height:80px;"
                        placeholder="Street address"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1rem;">
              <div>
                <label class="lf-label">City</label>
                <input type="text" name="city" class="lf-input"
                       value="<?= htmlspecialchars($user['city'] ?? '') ?>"
                       placeholder="City">
              </div>
              <div>
                <label class="lf-label">State / Province</label>
                <input type="text" name="state" class="lf-input"
                       value="<?= htmlspecialchars($user['state'] ?? '') ?>"
                       placeholder="State">
              </div>
              <div>
                <label class="lf-label">ZIP / Postal Code</label>
                <input type="text" name="zip_code" class="lf-input"
                       value="<?= htmlspecialchars($user['zip_code'] ?? '') ?>"
                       placeholder="ZIP">
              </div>
            </div>
            <div style="margin-bottom:1.75rem;">
              <label class="lf-label">Country</label>
              <input type="text" name="country" class="lf-input"
                     value="<?= htmlspecialchars($user['country'] ?? '') ?>"
                     placeholder="Country">
            </div>

            <div style="display:flex;justify-content:flex-end;">
              <button type="submit" name="update_profile" class="lf-btn"
                      style="width:auto;padding:.8rem 2rem;">
                <i class="fas fa-floppy-disk"></i> Save Changes
              </button>
            </div>
          </form>
        </div>
      </div>


      <!-- ══ ORDERS TAB ═══════════════════════════ -->
      <?php elseif ($activeTab === 'orders'): ?>
      <div style="background:var(--c-white);border-radius:14px;border:1px solid var(--c-gray-200);
                  box-shadow:var(--shadow-sm);overflow:hidden;">
        <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--c-gray-100);
                    display:flex;align-items:center;gap:.6rem;">
          <i class="fas fa-shopping-bag" style="color:var(--c-sage);"></i>
          <h2 style="margin:0;font-size:1.05rem;font-weight:700;color:var(--c-gray-800);">Order History</h2>
        </div>

        <?php if ($orders): ?>
        <div style="overflow-x:auto;">
          <table style="width:100%;border-collapse:collapse;">
            <thead>
              <tr style="background:var(--c-gray-50);">
                <?php foreach (['Order','Date','Items','Total','Status',''] as $h): ?>
                <th style="padding:.75rem 1.2rem;font-size:.68rem;font-weight:700;text-transform:uppercase;
                           letter-spacing:.07em;color:var(--c-gray-400);white-space:nowrap;
                           border-bottom:1px solid var(--c-gray-200);text-align:left;">
                  <?= $h ?>
                </th>
                <?php endforeach; ?>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orders as $ord): ?>
              <tr style="border-bottom:1px solid var(--c-gray-100);"
                  onmouseover="this.style.background='var(--c-gray-50)'"
                  onmouseout="this.style.background='transparent'">
                <td style="padding:.875rem 1.2rem;font-weight:700;color:var(--c-gray-800);font-size:.875rem;">
                  #<?= $ord['id'] ?>
                </td>
                <td style="padding:.875rem 1.2rem;color:var(--c-gray-600);font-size:.82rem;">
                  <?= date('M j, Y', strtotime($ord['order_date'])) ?>
                </td>
                <td style="padding:.875rem 1.2rem;color:var(--c-gray-600);font-size:.82rem;">
                  <?= $ord['item_count'] ?? '—' ?> items
                </td>
                <td style="padding:.875rem 1.2rem;font-weight:700;color:var(--c-gray-800);font-size:.875rem;">
                  RS <?= number_format($ord['total_amount'], 2) ?>
                </td>
                <td style="padding:.875rem 1.2rem;">
                  <span style="display:inline-flex;align-items:center;font-size:.72rem;font-weight:700;
                               padding:.25rem .75rem;border-radius:20px;<?= statusStyle($ord['status']) ?>">
                    <?= ucfirst($ord['status']) ?>
                  </span>
                </td>
                <td style="padding:.875rem 1.2rem;text-align:right;">
                  <a href="/novel/order_details?order_id=<?= $ord['id'] ?>"
                     style="display:inline-flex;align-items:center;gap:.35rem;
                            font-size:.78rem;font-weight:600;color:var(--c-sage);
                            background:var(--c-sage-light);padding:.35rem .85rem;
                            border-radius:8px;text-decoration:none;transition:var(--tr);"
                     onmouseover="this.style.background='var(--c-sage)';this.style.color='#fff';"
                     onmouseout="this.style.background='var(--c-sage-light)';this.style.color='var(--c-sage)';">
                    <i class="fas fa-eye"></i> View
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <?php else: ?>
        <div style="text-align:center;padding:5rem 2rem;">
          <div style="width:80px;height:80px;border-radius:50%;background:var(--c-sage-light);
                      display:flex;align-items:center;justify-content:center;
                      margin:0 auto 1.25rem;font-size:2rem;color:var(--c-sage);">
            <i class="fas fa-bag-shopping"></i>
          </div>
          <h4 style="font-weight:700;color:var(--c-gray-800);margin-bottom:.4rem;">No orders yet</h4>
          <p style="color:var(--c-gray-400);margin-bottom:1.5rem;font-size:.875rem;">
            You haven't placed any orders. Start shopping to see them here.
          </p>
          <a href="/novel/products" class="btn-novel btn-novel-primary">
            <i class="fas fa-shopping-bag"></i> Shop Now
          </a>
        </div>
        <?php endif; ?>
      </div>


      <!-- ══ SECURITY TAB ══════════════════════════ -->
      <?php elseif ($activeTab === 'security'): ?>
      <div style="background:var(--c-white);border-radius:14px;border:1px solid var(--c-gray-200);
                  box-shadow:var(--shadow-sm);overflow:hidden;">
        <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--c-gray-100);
                    display:flex;align-items:center;gap:.6rem;">
          <i class="fas fa-lock" style="color:var(--c-sage);"></i>
          <h2 style="margin:0;font-size:1.05rem;font-weight:700;color:var(--c-gray-800);">Security Settings</h2>
        </div>
        <div style="padding:1.75rem;">
          <form method="POST">
            <p style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;
                      color:var(--c-gray-400);margin-bottom:.9rem;">Change Password</p>

            <div style="margin-bottom:1rem;">
              <label class="lf-label">Current Password *</label>
              <div style="position:relative;">
                <input type="password" name="current_password" id="curPwd" class="lf-input"
                       placeholder="Enter current password" required
                       style="padding-right:2.75rem;">
                <button type="button" onclick="toggleField('curPwd','curEye')"
                        style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);
                               background:none;border:none;cursor:pointer;color:var(--c-gray-400);font-size:.9rem;">
                  <i class="fas fa-eye" id="curEye"></i>
                </button>
              </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.75rem;">
              <div>
                <label class="lf-label">New Password *</label>
                <div style="position:relative;">
                  <input type="password" name="new_password" id="newPwd" class="lf-input"
                         placeholder="Min. 6 characters" required minlength="6"
                         style="padding-right:2.75rem;" oninput="checkMatch()">
                  <button type="button" onclick="toggleField('newPwd','newEye')"
                          style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);
                                 background:none;border:none;cursor:pointer;color:var(--c-gray-400);font-size:.9rem;">
                    <i class="fas fa-eye" id="newEye"></i>
                  </button>
                </div>
              </div>
              <div>
                <label class="lf-label">Confirm New Password *</label>
                <input type="password" name="confirm_password" id="confPwd" class="lf-input"
                       placeholder="Repeat new password" required oninput="checkMatch()">
              </div>
            </div>

            <div id="pwdMsg" style="display:none;font-size:.8rem;margin-bottom:1rem;
                                    color:var(--c-clay);display:flex;align-items:center;gap:.4rem;">
              <i class="fas fa-exclamation-circle"></i>
              <span id="pwdMsgText"></span>
            </div>

            <!-- Password strength bar -->
            <div style="margin-bottom:1.5rem;" id="strengthWrap" style="display:none;">
              <div style="font-size:.72rem;color:var(--c-gray-400);margin-bottom:.35rem;">Password strength</div>
              <div style="height:4px;background:var(--c-gray-200);border-radius:4px;overflow:hidden;">
                <div id="strengthBar" style="height:100%;width:0;border-radius:4px;transition:width .3s;"></div>
              </div>
            </div>

            <div style="display:flex;justify-content:flex-end;">
              <button type="submit" name="change_password" id="pwdSubmit" class="lf-btn"
                      style="width:auto;padding:.8rem 2rem;">
                <i class="fas fa-lock"></i> Update Password
              </button>
            </div>
          </form>

          <!-- Security info -->
          <div style="margin-top:2rem;padding:1.25rem;background:var(--c-sage-light);
                      border-radius:12px;border:1px solid var(--c-sage-mid);">
            <h6 style="font-size:.82rem;font-weight:700;color:var(--c-sage);margin-bottom:.75rem;">
              <i class="fas fa-shield-halved me-1"></i> Security Tips
            </h6>
            <ul style="margin:0;padding-left:1.2rem;font-size:.82rem;color:var(--c-gray-600);line-height:1.8;">
              <li>Use at least 8 characters with a mix of letters, numbers and symbols.</li>
              <li>Never reuse passwords from other sites.</li>
              <li>Do not share your password with anyone.</li>
            </ul>
          </div>
        </div>
      </div>
      <?php endif; ?>

    </main>
  </div><!-- /profileLayout -->
</div>
</div>

<?php renderFooter(); ?>

<style>
/* Profile layout responsive */
@media (max-width: 768px) {
  #profileLayout { grid-template-columns: 1fr !important; }
  #profileLayout aside { position: static !important; }
}
/* Reuse login form input styles */
.lf-label {
  display: block;
  font-size: .72rem; font-weight: 700; text-transform: uppercase;
  letter-spacing: .06em; color: var(--c-gray-600); margin-bottom: .4rem;
}
.lf-input {
  width: 100%; padding: .75rem 1rem; font-family: var(--font-sans);
  font-size: .9rem; color: var(--c-gray-800);
  background: var(--c-gray-50); border: 1.5px solid var(--c-gray-200);
  border-radius: 10px; transition: var(--tr); outline: none;
  display: block;
}
.lf-input:focus { border-color: var(--c-sage); background: #fff; box-shadow: 0 0 0 3px rgba(90,122,90,.1); }
.lf-btn {
  width: 100%; padding: .875rem; border: none; border-radius: 10px;
  background: var(--c-sage); color: #fff; font-family: var(--font-sans);
  font-size: .9rem; font-weight: 600; cursor: pointer; transition: var(--tr);
  display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
}
.lf-btn:hover { background: var(--c-sage-dark); box-shadow: var(--shadow-sage); transform: translateY(-1px); }
</style>

<script>
function toggleField(id, eyeId) {
  const f = document.getElementById(id);
  const e = document.getElementById(eyeId);
  f.type = f.type === 'password' ? 'text' : 'password';
  e.className = f.type === 'text' ? 'fas fa-eye-slash' : 'fas fa-eye';
}

function checkMatch() {
  const np   = document.getElementById('newPwd').value;
  const cp   = document.getElementById('confPwd').value;
  const msg  = document.getElementById('pwdMsg');
  const txt  = document.getElementById('pwdMsgText');
  const bar  = document.getElementById('strengthBar');
  const wrap = document.getElementById('strengthWrap');

  // Strength
  if (np.length > 0) {
    wrap.style.display = 'block';
    let s = 0;
    if (np.length >= 8)           s += 30;
    if (/[A-Z]/.test(np))         s += 20;
    if (/[0-9]/.test(np))         s += 25;
    if (/[^A-Za-z0-9]/.test(np))  s += 25;
    bar.style.width = s + '%';
    bar.style.background = s < 40 ? '#ef4444' : s < 70 ? '#f59e0b' : '#059669';
  } else {
    wrap.style.display = 'none';
  }

  // Match
  if (cp.length > 0 && np !== cp) {
    msg.style.display = 'flex';
    txt.textContent = 'Passwords do not match.';
    document.getElementById('pwdSubmit').disabled = true;
    document.getElementById('pwdSubmit').style.opacity = '.5';
  } else {
    msg.style.display = 'none';
    document.getElementById('pwdSubmit').disabled = false;
    document.getElementById('pwdSubmit').style.opacity = '1';
  }
}
</script>
