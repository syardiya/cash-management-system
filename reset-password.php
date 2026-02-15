<?php
/**
 * Reset Password Page
 * Allows users to set a new password using a valid token
 */
require_once 'config/config.php';
require_once 'classes/User.php';

$token = $_GET['token'] ?? '';
$isValid = false;
$message = '';

if (empty($token)) {
    header('Location: login.php');
    exit;
}

// Basic validation of token existence/expiry (optional check before form show)
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT id FROM users WHERE password_reset_token = ? AND password_reset_expires > NOW()");
    $stmt->execute([$token]);
    if ($stmt->fetch()) {
        $isValid = true;
    } else {
        $message = "Tautan reset tidak valid atau sudah kedaluwarsa.";
    }
} catch (PDOException $e) {
    $message = "Terjadi kesalahan sistem.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Cash Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .auth-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .auth-card { width: 100%; max-width: 450px; background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 1.5rem; padding: 3rem; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); }
        .auth-header { text-align: center; margin-bottom: 2rem; }
        .auth-logo { width: 70px; height: 70px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: white; }
        .input-group { position: relative; margin-bottom: 1.5rem; }
        .input-icon { position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.1rem; }
        .form-control { padding-left: 3.5rem !important; }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fas fa-key"></i>
                </div>
                <h2>Set New Password</h2>
                <p class="text-muted">Buat password baru yang kuat untuk keamanan akun Anda.</p>
            </div>
            
            <div id="alert-container">
                <?php if (!$isValid && $message): ?>
                    <div class="alert alert-danger"><?php echo $message; ?></div>
                <?php endif; ?>
            </div>
            
            <?php if ($isValid): ?>
            <form id="resetForm">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="form-group">
                    <label>Password Baru</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 8 karakter" required>
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <div class="input-group">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Ulangi password" required>
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    Update Password
                </button>
            </form>
            <?php else: ?>
                <div class="text-center mt-4">
                    <a href="login.php" class="btn btn-outline btn-block">Kembali ke Login</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="assets/js/main.js"></script>
    <script>
        <?php if ($isValid): ?>
        document.getElementById('resetForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = this.querySelector('button');
            const formData = new FormData(this);
            
            if (formData.get('password') !== formData.get('confirm_password')) {
                showAlert('Password tidak cocok!', 'danger');
                return;
            }
            
            if (formData.get('password').length < 8) {
                showAlert('Password minimal 8 karakter!', 'danger');
                return;
            }
            
            setLoading(btn, true);
            
            try {
                const response = await fetch('api/reset-password.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.status) {
                    showAlert(result.message, 'success');
                    setTimeout(() => window.location.href = 'login.php', 2000);
                } else {
                    showAlert(result.message, 'danger');
                    setLoading(btn, false);
                }
            } catch (err) {
                showAlert('Terjadi kesalahan koneksi.', 'danger');
                setLoading(btn, false);
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>
