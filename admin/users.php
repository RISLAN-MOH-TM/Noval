<?php
require_once '../includes/product_manager.php';
require_once '../includes/user_manager.php';
require_once 'includes/admin_layout.php';

SessionManager::init();
if (!SessionManager::isLoggedIn()) { header('Location: ../login.php?redirect=admin/users.php'); exit; }
if (!SessionManager::isAdmin())    { header('Location: ../novel/home'); exit; }

$userManager = new UserManager();

$action = isset($_GET['action']) ? $_GET['action'] : 'view';
$userId = isset($_GET['id'])     ? (int)$_GET['id'] : 0;

// ── Handle POST actions ──────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['promote_user']) && $userId > 0) {
        $result  = $userManager->promoteToAdmin($userId);
        $message = $result ? 'User promoted to admin.' : null;
        $error   = $result ? null : 'Failed to promote user.';
        $action  = 'view';

    } elseif (isset($_POST['demote_user']) && $userId > 0) {
        $result  = $userManager->demoteToUser($userId);
        $message = $result ? 'Admin demoted to customer.' : null;
        $error   = $result ? null : 'Failed to demote user.';
        $action  = 'view';

    } elseif (isset($_POST['update_user']) && $userId > 0) {
        $result = $userManager->updateUser(
            $userId,
            trim($_POST['username']),
            trim($_POST['email']),
            trim($_POST['first_name']),
            trim($_POST['last_name']),
            trim($_POST['phone'])
        );
        // Update password if provided
        if (!empty(trim($_POST['new_password'] ?? ''))) {
            $userManager->updateUserPassword($userId, trim($_POST['new_password']));
        }
        $message = $result ? 'User updated successfully.' : null;
        $error   = $result ? null : 'Failed to update user.';
        $action  = 'view';
    }
}

// ── Data ─────────────────────────────────────────────
$editUser  = ($action === 'edit' && $userId > 0) ? $userManager->getUserById($userId) : null;
$users     = $userManager->getAllUsers();
$admins    = array_filter($users, fn($u) => $u['user_type'] === 'admin');
$customers = array_filter($users, fn($u) => $u['user_type'] === 'customer');

$pageTitle = $action === 'edit' ? 'Edit User' : 'Users';
adminHead($pageTitle);
adminSidebar('users');
?>

<!-- Page Head -->
<div class="page-head">
    <div class="page-head-left">
        <h1><?= $pageTitle ?></h1>
        <p><?= $action === 'edit' ? 'Update user details and permissions.' : 'Manage accounts and permissions for your store.' ?></p>
    </div>
    <?php if ($action === 'edit'): ?>
    <div class="page-head-right">
        <a href="users.php" class="abtn abtn-ghost"><i class="fas fa-arrow-left"></i> Back to Users</a>
    </div>
    <?php endif; ?>
</div>

<!-- Stats (list view only) -->
<?php if ($action === 'view'): ?>
<div class="stat-grid" style="grid-template-columns:repeat(auto-fit,minmax(160px,1fr));margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-users"></i></div>
        <div class="stat-info"><strong><?= count($users) ?></strong><span>Total Users</span></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-crown"></i></div>
        <div class="stat-info"><strong><?= count($admins) ?></strong><span>Admins</span></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-user"></i></div>
        <div class="stat-info"><strong><?= count($customers) ?></strong><span>Customers</span></div>
    </div>
</div>
<?php endif; ?>

<!-- Alerts -->
<?php if (isset($message)): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if (isset($error)): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- ══════════════════════════════════════════════════════
     EDIT FORM
══════════════════════════════════════════════════════ -->
<?php if ($action === 'edit' && $editUser): ?>

<form method="POST" action="users.php?action=edit&id=<?= $editUser['id'] ?>">
    <div class="row g-4">

        <!-- Left: User details -->
        <div class="col-lg-8">
            <div class="acard">
                <div class="acard-head">
                    <h5><i class="fas fa-user"></i> User Information</h5>
                    <span class="abadge <?= $editUser['user_type']==='admin'?'red':'green' ?>">
                        <i class="fas <?= $editUser['user_type']==='admin'?'fa-crown':'fa-user' ?>"></i>
                        <?= ucfirst($editUser['user_type']) ?>
                    </span>
                </div>
                <div class="acard-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Username <span style="color:var(--a-red)">*</span></label>
                            <input type="text" class="form-control" name="username"
                                   value="<?= htmlspecialchars($editUser['username']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span style="color:var(--a-red)">*</span></label>
                            <input type="email" class="form-control" name="email"
                                   value="<?= htmlspecialchars($editUser['email']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name"
                                   value="<?= htmlspecialchars($editUser['first_name'] ?? '') ?>"
                                   placeholder="First name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name"
                                   value="<?= htmlspecialchars($editUser['last_name'] ?? '') ?>"
                                   placeholder="Last name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone"
                                   value="<?= htmlspecialchars($editUser['phone'] ?? '') ?>"
                                   placeholder="Phone number">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Member Since</label>
                            <input type="text" class="form-control" value="<?= date('M j, Y', strtotime($editUser['created_at'])) ?>" disabled style="background:var(--bg-page);">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Password section -->
            <div class="acard">
                <div class="acard-head">
                    <h5><i class="fas fa-lock"></i> Change Password</h5>
                    <span style="font-size:.75rem;color:var(--tx-muted);">Leave blank to keep current password</span>
                </div>
                <div class="acard-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="new_password"
                                   placeholder="Enter new password (min 6 chars)"
                                   minlength="6">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmPwd"
                                   placeholder="Re-enter new password">
                        </div>
                    </div>
                    <div id="pwdMismatch" style="display:none;color:var(--a-red);font-size:.8rem;margin-top:.5rem;">
                        <i class="fas fa-exclamation-circle"></i> Passwords do not match.
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Actions + Role -->
        <div class="col-lg-4">
            <!-- Save card -->
            <div class="acard">
                <div class="acard-body">
                    <button type="submit" name="update_user" class="abtn abtn-primary w-100 mb-2" id="saveBtn">
                        <i class="fas fa-floppy-disk"></i> Save Changes
                    </button>
                    <a href="users.php" class="abtn abtn-ghost w-100" style="text-decoration:none;">
                        <i class="fas fa-xmark"></i> Cancel
                    </a>
                </div>
            </div>

            <!-- Role management -->
            <?php if ($editUser['id'] != SessionManager::get('user_id')): ?>
            <div class="acard">
                <div class="acard-head">
                    <h5><i class="fas fa-shield-halved"></i> Role</h5>
                </div>
                <div class="acard-body">
                    <p style="font-size:.82rem;color:var(--tx-muted);margin-bottom:1rem;">
                        Current role: <strong><?= ucfirst($editUser['user_type']) ?></strong>
                    </p>
                    <?php if ($editUser['user_type'] === 'customer'): ?>
                    <form method="POST" action="users.php?id=<?= $editUser['id'] ?>"
                          onsubmit="return confirm('Promote this user to admin?');">
                        <input type="hidden" name="promote_user" value="1">
                        <button type="submit" class="abtn abtn-outline-primary w-100">
                            <i class="fas fa-arrow-up"></i> Promote to Admin
                        </button>
                    </form>
                    <?php else: ?>
                    <form method="POST" action="users.php?id=<?= $editUser['id'] ?>"
                          onsubmit="return confirm('Demote this admin to customer?');">
                        <input type="hidden" name="demote_user" value="1">
                        <button type="submit" class="abtn abtn-ghost w-100">
                            <i class="fas fa-arrow-down"></i> Demote to Customer
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- User meta -->
            <div class="acard">
                <div class="acard-head"><h5><i class="fas fa-circle-info"></i> Account Info</h5></div>
                <div class="acard-body">
                    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem;">
                        <div class="user-av" style="width:48px;height:48px;font-size:1.2rem;flex-shrink:0;">
                            <?= strtoupper(substr($editUser['username'],0,1)) ?>
                        </div>
                        <div>
                            <div style="font-weight:700;color:var(--tx-head);"><?= htmlspecialchars($editUser['username']) ?></div>
                            <div style="font-size:.78rem;color:var(--tx-muted);">User #<?= $editUser['id'] ?></div>
                        </div>
                    </div>
                    <div style="font-size:.8rem;color:var(--tx-muted);line-height:2;">
                        <div><i class="fas fa-envelope me-2"></i><?= htmlspecialchars($editUser['email']) ?></div>
                        <div><i class="fas fa-calendar-days me-2"></i>Joined <?= date('M j, Y', strtotime($editUser['created_at'])) ?></div>
                        <?php if (!empty($editUser['phone'])): ?>
                        <div><i class="fas fa-phone me-2"></i><?= htmlspecialchars($editUser['phone']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// Validate password match before submit
document.getElementById('saveBtn').closest('form').addEventListener('submit', function(e) {
    const np = document.querySelector('[name="new_password"]').value;
    const cp = document.getElementById('confirmPwd').value;
    const msg = document.getElementById('pwdMismatch');
    if (np && np !== cp) {
        msg.style.display = 'block';
        e.preventDefault();
        document.getElementById('confirmPwd').focus();
    } else {
        msg.style.display = 'none';
    }
});
document.getElementById('confirmPwd').addEventListener('input', function() {
    const np = document.querySelector('[name="new_password"]').value;
    const msg = document.getElementById('pwdMismatch');
    msg.style.display = (np && this.value && np !== this.value) ? 'block' : 'none';
});
</script>

<?php else: ?>
<!-- ══════════════════════════════════════════════════════
     USERS TABLE
══════════════════════════════════════════════════════ -->
<div class="acard">
    <div class="acard-head">
        <h5><i class="fas fa-users"></i> All Users (<?= count($users) ?>)</h5>
    </div>
    <div class="acard-body p0">
        <div style="overflow-x:auto;">
        <table class="atable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Full Name</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td data-label="ID">
                        <span style="color:var(--tx-muted);font-size:.78rem;">#<?= $user['id'] ?></span>
                    </td>
                    <td data-label="User">
                        <div class="d-flex align-items-center gap-2">
                            <div class="user-av"><?= strtoupper(substr($user['username'],0,1)) ?></div>
                            <strong><?= htmlspecialchars($user['username']) ?></strong>
                        </div>
                    </td>
                    <td data-label="Email" style="color:var(--tx-muted);">
                        <i class="fas fa-envelope" style="margin-right:.35rem;font-size:.75rem;"></i>
                        <?= htmlspecialchars($user['email']) ?>
                    </td>
                    <td data-label="Name">
                        <?= htmlspecialchars(trim($user['first_name'] . ' ' . $user['last_name'])) ?: '—' ?>
                    </td>
                    <td data-label="Role">
                        <?php if ($user['user_type'] === 'admin'): ?>
                            <span class="abadge red"><i class="fas fa-crown"></i> Admin</span>
                        <?php else: ?>
                            <span class="abadge green"><i class="fas fa-user"></i> Customer</span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Joined" style="color:var(--tx-muted);font-size:.82rem;">
                        <i class="fas fa-calendar-days" style="margin-right:.35rem;"></i>
                        <?= date('M j, Y', strtotime($user['created_at'])) ?>
                    </td>
                    <td data-label="Actions">
                        <div class="d-flex gap-1 flex-wrap">
                            <!-- Edit button — always available -->
                            <a href="users.php?action=edit&id=<?= $user['id'] ?>"
                               class="abtn abtn-outline-primary abtn-sm" title="Edit user">
                                <i class="fas fa-pen"></i> Edit
                            </a>

                            <!-- Role toggle -->
                            <?php if ($user['user_type'] === 'customer'): ?>
                                <form method="POST" action="users.php?id=<?= $user['id'] ?>" class="d-inline"
                                      onsubmit="return confirm('Promote this user to admin?');">
                                    <input type="hidden" name="promote_user" value="1">
                                    <button type="submit" class="abtn abtn-ghost abtn-sm" title="Promote to admin">
                                        <i class="fas fa-arrow-up"></i>
                                    </button>
                                </form>
                            <?php elseif ($user['user_type'] === 'admin' && $user['id'] != SessionManager::get('user_id')): ?>
                                <form method="POST" action="users.php?id=<?= $user['id'] ?>" class="d-inline"
                                      onsubmit="return confirm('Demote this admin to customer?');">
                                    <input type="hidden" name="demote_user" value="1">
                                    <button type="submit" class="abtn abtn-ghost abtn-sm" title="Demote to customer">
                                        <i class="fas fa-arrow-down"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="abadge amber"><i class="fas fa-lock"></i> You</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php adminFoot(); ?>
