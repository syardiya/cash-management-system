<?php
/**
 * Forgot Password Page
 * Placeholder for password recovery functionality
 */
require_once 'config/config.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Cash Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .auth-card {
            width: 100%;
            max-width: 450px;
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1.5rem;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .auth-logo {
            width: 70px;
            height: 70px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
        }
        
        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .input-icon {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fas fa-lock-open"></i>
                </div>
                <h2>Reset Password</h2>
                <p class="text-muted">Enter your email and we'll send you instructions</p>
            </div>
            
            <div id="alert-container"></div>
            
            <form id="forgotForm">
                <div class="input-group">
                    <input type="email" name="email" class="form-control" placeholder="Email Address" required style="padding-left: 3.5rem;">
                    <i class="fas fa-envelope input-icon"></i>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    Send Link
                </button>
            </form>
            
            <div class="text-center mt-4">
                <a href="login.php" class="text-primary" style="text-decoration: none;">Back to Login</a>
            </div>
        </div>
    </div>
    
    <script src="assets/js/main.js"></script>
    <script>
        document.getElementById('forgotForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = this.querySelector('button');
            const formData = new FormData(this);
            setLoading(btn, true);
            
            try {
                const response = await fetch('api/forgot-password.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.status) {
                    showAlert(result.message, 'success');
                } else {
                    showAlert(result.message, 'danger');
                }
            } catch (err) {
                showAlert('Terjadi kesalahan koneksi.', 'danger');
            } finally {
                setLoading(btn, false);
            }
        });
    </script>
</body>
</html>
