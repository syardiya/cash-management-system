<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Cash Management System</title>
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
            max-width: 600px;
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
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
        }
        
        .auth-title {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .auth-subtitle {
            color: var(--text-muted);
            font-size: 1rem;
        }
        
        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1.25rem;
        }
        
        .input-group .form-control {
            padding-left: 3rem;
        }
        
        .password-strength {
            margin-top: 0.5rem;
            height: 4px;
            background: var(--dark-border);
            border-radius: 2px;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            transition: all 0.3s ease;
            width: 0%;
        }
        
        .strength-weak { background: #ef4444; width: 33%; }
        .strength-medium { background: #f59e0b; width: 66%; }
        .strength-strong { background: #10b981; width: 100%; }
        
        .auth-links {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .auth-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .auth-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fas fa-wallet"></i>
                </div>
                <h1 class="auth-title">Create Account</h1>
                <p class="auth-subtitle">Register your organization to start managing finances</p>
            </div>
            
            <div id="alert-container"></div>
            
            <form id="registerForm" method="POST" action="api/register.php">
                <h3 class="mb-3" style="font-size: 1.25rem; color: var(--text-light);">Organization Information</h3>
                
                <div class="input-group">
                    <i class="fas fa-building input-icon"></i>
                    <input 
                        type="text" 
                        name="org_name" 
                        class="form-control" 
                        placeholder="Organization Name"
                        required
                    >
                </div>
                
                <div class="input-group">
                    <i class="fas fa-envelope input-icon"></i>
                    <input 
                        type="email" 
                        name="org_email" 
                        class="form-control" 
                        placeholder="Organization Email"
                        required
                    >
                </div>
                
                <div class="row">
                    <div class="col-6">
                        <div class="input-group">
                            <i class="fas fa-phone input-icon"></i>
                            <input 
                                type="text" 
                                name="org_phone" 
                                class="form-control" 
                                placeholder="Phone (Optional)"
                            >
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group">
                            <i class="fas fa-map-marker-alt input-icon"></i>
                            <input 
                                type="text" 
                                name="org_address" 
                                class="form-control" 
                                placeholder="Address (Optional)"
                            >
                        </div>
                    </div>
                </div>
                
                <h3 class="mb-3 mt-4" style="font-size: 1.25rem; color: var(--text-light);">Admin Account</h3>
                
                <div class="input-group">
                    <i class="fas fa-user input-icon"></i>
                    <input 
                        type="text" 
                        name="full_name" 
                        class="form-control" 
                        placeholder="Full Name"
                        required
                    >
                </div>
                
                <div class="input-group">
                    <i class="fas fa-user-circle input-icon"></i>
                    <input 
                        type="text" 
                        name="username" 
                        class="form-control" 
                        placeholder="Username"
                        required
                        minlength="3"
                    >
                </div>
                
                <div class="input-group">
                    <i class="fas fa-envelope input-icon"></i>
                    <input 
                        type="email" 
                        name="email" 
                        class="form-control" 
                        placeholder="Your Email"
                        required
                    >
                </div>
                
                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input 
                        type="password" 
                        name="password" 
                        id="password"
                        class="form-control" 
                        placeholder="Password"
                        required
                        minlength="8"
                    >
                    <div class="password-strength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>
                    <small class="text-muted" style="font-size: 0.75rem; display: block; margin-top: 0.5rem;">
                        Must contain uppercase, lowercase, number, and special character
                    </small>
                </div>
                
                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input 
                        type="password" 
                        name="confirm_password" 
                        id="confirmPassword"
                        class="form-control" 
                        placeholder="Confirm Password"
                        required
                    >
                </div>
                
                <div class="mb-3">
                    <label class="d-flex align-items-start gap-2" style="cursor: pointer;">
                        <input type="checkbox" name="terms" required style="cursor: pointer; margin-top: 0.25rem;">
                        <span style="font-size: 0.875rem; color: var(--text-muted);">
                            I agree to the <a href="#" style="color: var(--primary-color);">Terms & Conditions</a> and <a href="#" style="color: var(--primary-color);">Privacy Policy</a>
                        </span>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>
            
            <div class="auth-links">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>
    
    <script src="assets/js/main.js"></script>
    <script>
        // Password strength checker
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            strengthBar.className = 'password-strength-bar';
            
            if (strength <= 2) {
                strengthBar.classList.add('strength-weak');
            } else if (strength <= 4) {
                strengthBar.classList.add('strength-medium');
            } else {
                strengthBar.classList.add('strength-strong');
            }
        });
        
        // Form submission
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            
            // Check password match
            const password = form.querySelector('[name="password"]').value;
            const confirmPassword = form.querySelector('[name="confirm_password"]').value;
            
            if (password !== confirmPassword) {
                showAlert('Passwords do not match!', 'danger');
                return;
            }
            
            setLoading(submitBtn, true);
            
            try {
                const response = await fetch('api/register.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.status) {
                    showAlert(result.message, 'success');
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                } else {
                    showAlert(result.message, 'danger');
                    setLoading(submitBtn, false);
                }
            } catch (error) {
                showAlert('An error occurred. Please try again.', 'danger');
                setLoading(submitBtn, false);
            }
        });
    </script>
</body>
</html>
