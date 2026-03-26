<?php
require_once 'includes/header.php';
// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard_" . $_SESSION['role'] . ".php");
    exit();
}
?>

<div class="container">
    <div class="auth-container">
        <div class="logo-text">Rooh<span>Bharat</span></div>
        <p class="text-center text-muted mb-4">Civic Action Network</p>
        
        <ul class="nav nav-pills nav-justified mb-3" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-login-tab" data-bs-toggle="pill" data-bs-target="#pills-login" type="button" role="tab">Login</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-register-tab" data-bs-toggle="pill" data-bs-target="#pills-register" type="button" role="tab">Register</button>
            </li>
        </ul>
        
        <div class="tab-content" id="pills-tabContent">
            <!-- Login Form -->
            <div class="tab-pane fade show active" id="pills-login" role="tabpanel">
                <form action="api/auth.php" method="POST">
                    <input type="hidden" name="action" value="login">
                    <div class="mb-3">
                        <label class="form-label">Mock Aadhaar / Mobile No.</label>
                        <input type="text" name="mobile" class="form-control" required placeholder="Enter your 10-digit number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
            
            <!-- Register Form -->
            <div class="tab-pane fade" id="pills-register" role="tabpanel">
                <form action="api/auth.php" method="POST">
                    <input type="hidden" name="action" value="register">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mobile No.</label>
                        <input type="text" name="mobile" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="citizen">Citizen</option>
                            <option value="official">Government Official</option>
                            <option value="youth">Youth / Volunteer</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-secondary w-100" style="background-color: var(--secondary); border-color: var(--secondary);">Register (Aadhaar Mock)</button>
                </form>
            </div>
        </div>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger mt-3 py-2 text-center text-sm">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
