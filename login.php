<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cash Management System</title>
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
            position: relative;
            overflow: hidden;
        }
        
        /* Background animation elements */
        .auth-bg-shape {
            position: absolute;
            opacity: 0.1;
            z-index: -1;
        }
        
        .shape-1 {
            top: -10%;
            left: -10%;
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, var(--primary-color) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .shape-2 {
            bottom: -10%;
            right: -10%;
            width: 40vw;
            height: 40vw;
            background: radial-gradient(circle, var(--secondary-color) 0%, transparent 70%);
            border-radius: 50%;
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
            transform: translateY(0);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .auth-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.4);
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 2.5rem;
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
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .auth-title {
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #fff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }
        
        .auth-subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
        }
        
        .auth-tabs {
            display: flex;
            margin-bottom: 2rem;
            background: rgba(15, 23, 42, 0.5);
            padding: 0.5rem;
            border-radius: 0.75rem;
            position: relative;
        }
        
        .auth-tab {
            flex: 1;
            text-align: center;
            padding: 0.75rem;
            cursor: pointer;
            border-radius: 0.5rem;
            font-weight: 600;
            color: var(--text-muted);
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }
        
        .auth-tab.active {
            color: white;
            background: var(--primary-color);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
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
            transition: color 0.3s ease;
        }
        
        .form-control {
            width: 100%;
            padding: 1rem 1rem 1rem 3.5rem;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.75rem;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            background: rgba(15, 23, 42, 0.8);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            outline: none;
        }
        
        .form-control:focus + .input-icon {
            color: var(--primary-color);
        }
        
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
        }
        
        .btn-submit:active {
            transform: translateY(0);
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .auth-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        
        .auth-footer a:hover {
            color: #818cf8;
            text-decoration: underline;
        }

        /* Hidden class */
        .d-none {
            display: none;
        }
    </style>
</head>
<body>
    <div class="auth-bg-shape shape-1"></div>
    <div class="auth-bg-shape shape-2"></div>
    
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fas fa-wallet"></i>
                </div>
                <h1 class="auth-title">Welcome Back</h1>
                <p class="auth-subtitle">Manage your organization's finances securely</p>
            </div>
            
            <div id="alert-container"></div>
            
            <div class="auth-tabs">
                <div class="auth-tab active" onclick="switchTab('admin')">
                    <i class="fas fa-user-shield me-2"></i> Admin Login
                </div>
                <div class="auth-tab" onclick="switchTab('member')">
                    <i class="fas fa-users me-2"></i> Member Access
                </div>
            </div>
            
            <form id="loginForm" method="POST" action="api/login.php">
                <input type="hidden" name="login_type" id="loginType" value="admin">
                
                <!-- Admin Inputs -->
                <div id="adminInputs">
                    <div class="input-group">
                        <input 
                            type="text" 
                            name="username" 
                            class="form-control" 
                            placeholder="Username or Email"
                            autocomplete="username"
                        >
                        <i class="fas fa-user input-icon"></i>
                    </div>
                    
                    <div class="input-group">
                        <input 
                            type="password" 
                            name="password" 
                            class="form-control" 
                            placeholder="Password"
                            autocomplete="current-password"
                        >
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                </div>

                <!-- Member Inputs -->
                <div id="memberInputs" class="d-none">
                    <div class="input-group">
                        <input 
                            type="text" 
                            name="org_slug" 
                            class="form-control" 
                            placeholder="Organization Slug (e.g. demo-org)"
                        >
                        <i class="fas fa-building input-icon"></i>
                    </div>
                    
                    <div class="input-group">
                        <input 
                            type="text" 
                            name="access_code" 
                            class="form-control" 
                            placeholder="Access Code (e.g. X7B9A2)"
                            maxlength="6"
                            style="text-transform: uppercase; letter-spacing: 2px; font-weight: bold;"
                        >
                        <i class="fas fa-key input-icon"></i>
                    </div>
                    <div class="text-center text-muted mb-3" style="font-size: 0.85rem;">
                        <i class="fas fa-info-circle"></i> View-only access for organization members
                    </div>
                </div>
                
                <div class="mb-4 text-end" id="forgotPasswordLink">
                    <a href="forgot-password.php" style="color: var(--text-muted); font-size: 0.9rem; text-decoration: none;">Forgot Password?</a>
                </div>
                
                <button type="submit" class="btn-submit">
                    <span id="btnText">Sign In</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
            
            <div class="auth-footer" id="registerFooter">
                <p style="color: var(--text-muted);">
                    Don't have an organization? <a href="register.php">Create Account</a>
                </p>
            </div>
        </div>
    </div>
    
    <script src="assets/js/main.js"></script>
    <script>
        function switchTab(type) {
            const tabs = document.querySelectorAll('.auth-tab');
            const adminInputs = document.getElementById('adminInputs');
            const memberInputs = document.getElementById('memberInputs');
            const loginType = document.getElementById('loginType');
            const forgotLink = document.getElementById('forgotPasswordLink');
            const registerFooter = document.getElementById('registerFooter');
            const btnText = document.getElementById('btnText');
            
            tabs.forEach(tab => tab.classList.remove('active'));
            
            if (type === 'admin') {
                tabs[0].classList.add('active');
                adminInputs.classList.remove('d-none');
                memberInputs.classList.add('d-none');
                loginType.value = 'admin';
                forgotLink.style.display = 'block';
                registerFooter.style.display = 'block';
                btnText.innerText = 'Sign In';
                
                // Toggle required
                adminInputs.querySelectorAll('input').forEach(i => i.required = true);
                memberInputs.querySelectorAll('input').forEach(i => i.required = false);
            } else {
                tabs[1].classList.add('active');
                adminInputs.classList.add('d-none');
                memberInputs.classList.remove('d-none');
                loginType.value = 'member';
                forgotLink.style.display = 'none';
                registerFooter.style.display = 'none'; // Members don't register, admins do
                btnText.innerText = 'Access Organization';
                
                // Toggle required
                adminInputs.querySelectorAll('input').forEach(i => i.required = false);
                memberInputs.querySelectorAll('input').forEach(i => i.required = true);
            }
        }

        // Initialize required fields
        switchTab('admin');
        
        // Form submission
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnContent = submitBtn.innerHTML;
            
            setLoading(submitBtn, true);
            
            try {
                const response = await fetch('api/login.php', {
                    method: 'POST',
                    body: formData
                });
                
                // Handle non-JSON response (debugging)
                const text = await response.text();
                let result;
                try {
                    result = JSON.parse(text);
                } catch (err) {
                    console.error('Server response:', text);
                    throw new Error('Invalid server response');
                }
                
                if (result.status) {
                    showAlert(result.message, 'success');
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 1000);
                } else {
                    showAlert(result.message, 'danger');
                    setLoading(submitBtn, false);
                    submitBtn.innerHTML = originalBtnContent;
                }
            } catch (error) {
                console.error(error);
                showAlert('An error occurred. Please try again.', 'danger');
                setLoading(submitBtn, false);
                submitBtn.innerHTML = originalBtnContent;
            }
        });
    </script>
</body>
</html>
