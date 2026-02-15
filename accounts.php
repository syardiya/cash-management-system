<?php
require_once 'config/config.php';
require_login();

require_once 'classes/AccountSource.php';

$accountObj = new AccountSource();
$org_id = $_SESSION['organization_id'];
$accounts = $accountObj->getAccounts($org_id, false); // Get all accounts including inactive
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo generate_csrf_token(); ?>">
    <title>Manage Accounts - Cash Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .dashboard-layout {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 280px;
            background: var(--dark-card);
            border-right: 1px solid var(--dark-border);
            padding: 2rem 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-brand {
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid var(--dark-border);
            margin-bottom: 1.5rem;
        }
        
        .sidebar-brand h2 {
            font-size: 1.5rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .sidebar-brand .icon {
            width: 40px;
            height: 40px;
            background: var(--gradient-primary);
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 1.5rem;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary-color);
            border-left: 3px solid var(--primary-color);
        }
        
        .sidebar-menu i {
            width: 20px;
            text-align: center;
            font-size: 1.125rem;
        }
        
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
        }
        
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--dark-border);
        }
        
        .account-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .account-card {
            background: var(--dark-card);
            border-radius: 1rem;
            padding: 1.5rem;
            border: 1px solid var(--dark-border);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .account-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
            border-color: var(--primary-color);
        }
        
        .account-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }
        
        .account-type-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }
        
        .account-type-bank {
            background: rgba(79, 70, 229, 0.2);
            color: #a5b4fc;
        }
        
        .account-type-e-wallet {
            background: rgba(16, 185, 129, 0.2);
            color: #6ee7b7;
        }
        
        .account-type-cash {
            background: rgba(245, 158, 11, 0.2);
            color: #fcd34d;
        }
        
        .account-balance {
            font-size: 2rem;
            font-weight: 700;
            margin: 1rem 0;
        }
        
        .account-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--dark-border);
        }
        
        .add-account-card {
            border: 2px dashed var(--dark-border);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 250px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .add-account-card:hover {
            border-color: var(--primary-color);
            background: rgba(79, 70, 229, 0.05);
        }
        
        .add-account-card i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
    </style>
</head>
<body class="logged-in">
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <h2>
                    <span class="icon">
                        <i class="fas fa-wallet"></i>
                    </span>
                    CashFlow
                </h2>
                <p class="text-muted" style="margin: 0.5rem 0 0; font-size: 0.875rem;">
                    <?php echo htmlspecialchars($_SESSION['org_name']); ?>
                </p>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="transactions.php"><i class="fas fa-exchange-alt"></i> Transactions</a></li>
                <li><a href="accounts.php" class="active"><i class="fas fa-wallet"></i> Accounts</a></li>
                <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                <li><a href="anomalies.php"><i class="fas fa-shield-alt"></i> AI Alerts</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="top-bar">
                <div>
                    <h1><i class="fas fa-wallet"></i> Manage Accounts</h1>
                    <p class="text-muted">Manage your cash sources and view balances</p>
                </div>
                
                <?php if ($_SESSION['role'] !== 'member'): ?>
                <button class="btn btn-primary" onclick="openAddAccountModal()">
                    <i class="fas fa-plus"></i> Add Account
                </button>
                <?php endif; ?>
            </div>
            
            <div id="alert-container"></div>
            
            <!-- Account Grid -->
            <div class="account-grid">
                <!-- Add Account Card -->
                <?php if ($_SESSION['role'] !== 'member'): ?>
                <div class="account-card add-account-card" onclick="openAddAccountModal()">
                    <i class="fas fa-plus-circle"></i>
                    <div style="font-weight: 600; font-size: 1.125rem;">Add New Account</div>
                    <div class="text-muted" style="font-size: 0.875rem;">Create a new cash source</div>
                </div>
                <?php endif; ?>
                
                <!-- Existing Accounts -->
                <?php foreach ($accounts as $account): ?>
                    <div class="account-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <span class="account-type-badge account-type-<?php echo $account['type']; ?>">
                                <?php echo htmlspecialchars($account['type']); ?>
                            </span>
                            <span class="badge badge-<?php echo $account['status'] === 'active' ? 'success' : 'danger'; ?>">
                                <?php echo $account['status']; ?>
                            </span>
                        </div>
                        
                        <h3 style="margin: 0; font-size: 1.25rem;">
                            <?php echo htmlspecialchars($account['name']); ?>
                        </h3>
                        
                        <?php if ($account['account_number']): ?>
                            <div class="text-muted" style="font-size: 0.875rem; margin-top: 0.25rem;">
                                <?php echo htmlspecialchars($account['account_number']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="account-balance">
                            <?php echo format_currency($account['current_balance']); ?>
                        </div>
                        
                        <div style="font-size: 0.875rem; color: var(--text-muted);">
                            Initial: <?php echo format_currency($account['initial_balance']); ?>
                        </div>
                        
                        <?php if ($account['description']): ?>
                            <p class="text-muted" style="margin-top: 0.5rem; font-size: 0.875rem;">
                                <?php echo htmlspecialchars($account['description']); ?>
                            </p>
                        <?php endif; ?>
                        
                        <div class="account-actions">
                            <button class="btn btn-sm btn-primary" onclick="viewAccountDetails(<?php echo $account['id']; ?>)">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <?php if ($_SESSION['role'] !== 'member'): ?>
                            <button class="btn btn-sm btn-outline" onclick="editAccount(<?php echo $account['id']; ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <?php if ($account['status'] === 'active'): ?>
                                <button class="btn btn-sm btn-warning" onclick="toggleStatus(<?php echo $account['id']; ?>, 'inactive')">
                                    <i class="fas fa-pause"></i>
                                </button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-success" onclick="toggleStatus(<?php echo $account['id']; ?>, 'active')">
                                    <i class="fas fa-play"></i>
                                </button>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>
    
    <!-- Add/Edit Account Modal -->
    <div id="accountModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="accountModalTitle">Add Account</h3>
                <button class="modal-close">&times;</button>
            </div>
            <form id="accountForm">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="hidden" name="account_id" id="accountId">
                
                <div class="form-group">
                    <label class="form-label">Account Name</label>
                    <input type="text" name="name" id="accountName" class="form-control" placeholder="e.g., Main Bank Account" required>
                </div>
                
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-label">Type</label>
                            <select name="type" id="accountType" class="form-control form-select" required>
                                <option value="">Select Type</option>
                                <option value="bank">Bank</option>
                                <option value="e-wallet">E-Wallet</option>
                                <option value="cash">Cash</option>
                                <option value="investment">Investment</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-label">Account Number (Optional)</label>
                            <input type="text" name="account_number" id="accountNumber" class="form-control" placeholder="xxxx-xxxx-xxxx">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Initial Balance</label>
                    <input type="number" name="initial_balance" id="initialBalance" class="form-control" placeholder="0" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Currency</label>
                    <select name="currency" id="currency" class="form-control form-select">
                        <option value="IDR" selected>IDR (Indonesian Rupiah)</option>
                        <option value="USD">USD (US Dollar)</option>
                        <option value="EUR">EUR (Euro)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description (Optional)</label>
                    <textarea name="description" id="accountDescription" class="form-control" placeholder="Add notes about this account"></textarea>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> Save Account
                    </button>
                    <button type="button" class="btn btn-outline btn-block" onclick="closeAccountModal()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="assets/js/main.js"></script>
    <script>
        const accountModal = new Modal('accountModal');
        
        function openAddAccountModal() {
            document.getElementById('accountModalTitle').textContent = 'Add New Account';
            document.getElementById('accountForm').reset();
            document.getElementById('accountId').value = '';
            accountModal.show();
        }
        
        function closeAccountModal() {
            accountModal.hide();
        }
        
        // Submit account form
        document.getElementById('accountForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const submitBtn = e.target.querySelector('button[type="submit"]');
            
            setLoading(submitBtn, true);
            
            try {
                const response = await fetch('api/accounts.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.status) {
                    showAlert(result.message, 'success');
                    accountModal.hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(result.message, 'danger');
                    setLoading(submitBtn, false);
                }
            } catch (error) {
                showAlert('An error occurred. Please try again.', 'danger');
                setLoading(submitBtn, false);
            }
        });
        
        function viewAccountDetails(accountId) {
            window.location.href = 'account-details.php?id=' + accountId;
        }
        
        function editAccount(accountId) {
            // In a full implementation, fetch account data and populate form
            showAlert('Edit functionality - fetch account data and populate modal', 'info');
            openAddAccountModal();
        }
        
        async function toggleStatus(accountId, newStatus) {
            if (!await confirmAction(`Are you sure you want to ${newStatus === 'active' ? 'activate' : 'deactivate'} this account?`)) {
                return;
            }
            
            const formData = new FormData();
            formData.append('csrf_token', getCsrfToken());
            formData.append('account_id', accountId);
            formData.append('status', newStatus);
            
            try {
                const response = await fetch('api/accounts.php?action=update_status', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.status) {
                    showAlert(result.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(result.message, 'danger');
                }
            } catch (error) {
                showAlert('Failed to update account status', 'danger');
            }
        }
    </script>
</body>
</html>
