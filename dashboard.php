<?php
require_once 'config/config.php';
require_login();

require_once 'classes/AccountSource.php';
require_once 'classes/Transaction.php';

$accountObj = new AccountSource();
$transactionObj = new Transaction();

$org_id = $_SESSION['organization_id'];

// Get total balances
$balances = $accountObj->getTotalBalance($org_id);

// Get all accounts
$accounts = $accountObj->getAccounts($org_id);

// Get recent transactions
$recent_transactions = $transactionObj->getTransactions($org_id, ['limit' => 10]);

// Get statistics
$stats = $transactionObj->getStatistics($org_id, 'month');

// Calculate income and expense for this month
$total_income = 0;
$total_expense = 0;
foreach ($stats as $stat) {
    if ($stat['transaction_type'] === 'income') {
        $total_income = $stat['total'];
    } elseif ($stat['transaction_type'] === 'expense') {
        $total_expense = $stat['total'];
    }
}

// Get anomalies
$db = Database::getInstance();
$conn = $db->getConnection();
$stmt = $conn->prepare("
    SELECT * FROM anomaly_logs 
    WHERE organization_id = ? AND is_resolved = 0
    ORDER BY detected_at DESC
    LIMIT 5
");
$stmt->execute([$org_id]);
$anomalies = $stmt->fetchAll();

// Get Organization Details (for Access Code)
$org_access_code = '';
if ($_SESSION['role'] === 'admin') {
    $stmt = $conn->prepare("SELECT member_access_code FROM organizations WHERE id = ?");
    $stmt->execute([$org_id]);
    $result = $stmt->fetch();
    $org_access_code = $result['member_access_code'] ?? 'N/A';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo generate_csrf_token(); ?>">
    <title>Dashboard - Cash Management System</title>
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
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 700;
            color: white;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card-gradient {
            position: relative;
            overflow: hidden;
        }
        
        .stat-card-gradient::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            pointer-events: none;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .quick-action-btn {
            padding: 1.5rem;
            text-align: center;
            background: var(--dark-card);
            border: 2px dashed var(--dark-border);
            border-radius: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: var(--text-light);
        }
        
        .quick-action-btn:hover {
            border-color: var(--primary-color);
            background: rgba(79, 70, 229, 0.05);
            transform: translateY(-2px);
        }
        
        .quick-action-btn i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .anomaly-alert {
            background: rgba(239, 68, 68, 0.1);
            border-left: 4px solid var(--danger-color);
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        .transaction-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .transaction-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid var(--dark-border);
            transition: all 0.3s ease;
        }
        
        .transaction-item:hover {
            background: rgba(79, 70, 229, 0.03);
        }
        
        .transaction-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-right: 1rem;
        }
        
        .transaction-icon.income {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }
        
        .transaction-icon.expense {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
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
                <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="transactions.php"><i class="fas fa-exchange-alt"></i> Transactions</a></li>
                <li><a href="accounts.php"><i class="fas fa-wallet"></i> Accounts</a></li>
                <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                <li><a href="anomalies.php"><i class="fas fa-shield-alt"></i> AI Alerts</a></li>
                <?php if ($_SESSION['role'] !== 'member'): ?>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <?php endif; ?>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="top-bar">
                <div>
                    <h1>Dashboard</h1>
                    <p class="text-muted">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</p>
                </div>
                
                <div class="user-info">
                    <div>
                        <div style="font-weight: 600;"><?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
                        <div class="text-muted" style="font-size: 0.875rem;">
                            <?php echo ucfirst($_SESSION['role']); ?>
                        </div>
                    </div>
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
                    </div>
                </div>
            </div>
            
            <!-- Admin Access Code Display -->
            <?php if ($_SESSION['role'] === 'admin' && !empty($org_access_code)): ?>
            <div class="card mb-4" style="background: linear-gradient(to right, rgba(79, 70, 229, 0.1), rgba(124, 58, 237, 0.1)); border: 1px solid rgba(99, 102, 241, 0.2);">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h4 style="margin: 0 0 0.5rem 0; color: var(--primary-color);">📢 Member Access Code</h4>
                        <p class="text-muted" style="margin: 0;">Share this code with your team members to give them view-only access.</p>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div style="font-family: monospace; font-size: 1.5rem; letter-spacing: 2px; font-weight: bold; background: rgba(0,0,0,0.2); padding: 0.5rem 1rem; border-radius: 0.5rem; color: white;">
                            <?php echo htmlspecialchars($org_access_code); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Anomaly Alerts -->
            <?php if (!empty($anomalies)): ?>
                <div class="mb-4">
                    <h3 class="mb-3"><i class="fas fa-exclamation-triangle text-warning"></i> AI Anomaly Alerts</h3>
                    <?php foreach ($anomalies as $anomaly): ?>
                        <div class="anomaly-alert">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="badge badge-<?php echo $anomaly['severity']; ?> mb-2">
                                        <?php echo strtoupper($anomaly['severity']); ?>
                                    </span>
                                    <p style="margin-bottom: 0.5rem; font-weight: 600;">
                                        <?php echo htmlspecialchars($anomaly['description']); ?>
                                    </p>
                                    <p class="text-muted" style="margin: 0; font-size: 0.875rem;">
                                        💡 <?php echo htmlspecialchars($anomaly['suggested_action']); ?>
                                    </p>
                                </div>
                                <button class="btn btn-sm btn-outline" onclick="resolveAnomaly(<?php echo $anomaly['id']; ?>)">
                                    Resolve
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card stat-card-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="stat-label" style="color: rgba(255,255,255,0.8);">Total Balance</div>
                    <div class="stat-value" style="color: white;">
                        <?php echo format_currency($balances['total_balance'] ?? 0); ?>
                    </div>
                    <div style="color: rgba(255,255,255,0.7); font-size: 0.875rem;">
                        From <?php echo $balances['total_accounts'] ?? 0; ?> accounts
                    </div>
                </div>
                
                <div class="stat-card stat-card-gradient" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <div class="stat-label" style="color: rgba(255,255,255,0.8);">Income This Month</div>
                    <div class="stat-value" style="color: white;">
                        <?php echo format_currency($total_income); ?>
                    </div>
                    <div style="color: rgba(255,255,255,0.7); font-size: 0.875rem;">
                        <i class="fas fa-arrow-up"></i> Total income
                    </div>
                </div>
                
                <div class="stat-card stat-card-gradient" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                    <div class="stat-label" style="color: rgba(255,255,255,0.8);">Expenses This Month</div>
                    <div class="stat-value" style="color: white;">
                        <?php echo format_currency($total_expense); ?>
                    </div>
                    <div style="color: rgba(255,255,255,0.7); font-size: 0.875rem;">
                        <i class="fas fa-arrow-down"></i> Total expense
                    </div>
                </div>
                
                <div class="stat-card stat-card-gradient" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <div class="stat-label" style="color: rgba(255,255,255,0.8);">Net Balance</div>
                    <div class="stat-value" style="color: white;">
                        <?php echo format_currency($total_income - $total_expense); ?>
                    </div>
                    <div style="color: rgba(255,255,255,0.7); font-size: 0.875rem;">
                        This month's profit/loss
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions (Admin Only) -->
            <?php if ($_SESSION['role'] !== 'member'): ?>
            <h3 class="mb-3">Quick Actions</h3>
            <div class="quick-actions mb-4">
                <a href="#" class="quick-action-btn" onclick="openAddTransactionModal('income'); return false;">
                    <i class="fas fa-plus-circle text-success"></i>
                    <div style="font-weight: 600;">Add Income</div>
                </a>
                <a href="#" class="quick-action-btn" onclick="openAddTransactionModal('expense'); return false;">
                    <i class="fas fa-minus-circle text-danger"></i>
                    <div style="font-weight: 600;">Add Expense</div>
                </a>
                <a href="accounts.php" class="quick-action-btn">
                    <i class="fas fa-wallet text-primary"></i>
                    <div style="font-weight: 600;">Manage Accounts</div>
                </a>
                <a href="reports.php" class="quick-action-btn">
                    <i class="fas fa-file-alt text-warning"></i>
                    <div style="font-weight: 600;">View Reports</div>
                </a>
            </div>
            <?php endif; ?>
            
            <!-- Recent Transactions -->
            <div class="row">
                <div class="col-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 style="margin: 0;">Recent Transactions</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recent_transactions)): ?>
                                <p class="text-muted text-center p-4">No transactions yet. Add your first transaction!</p>
                            <?php else: ?>
                                <ul class="transaction-list">
                                    <?php foreach ($recent_transactions as $transaction): ?>
                                        <li class="transaction-item">
                                            <div class="d-flex align-items-center">
                                                <div class="transaction-icon <?php echo $transaction['transaction_type']; ?>">
                                                    <i class="fas fa-<?php echo $transaction['transaction_type'] === 'income' ? 'arrow-up' : 'arrow-down'; ?>"></i>
                                                </div>
                                                <div>
                                                    <div style="font-weight: 600;">
                                                        <?php echo htmlspecialchars($transaction['category']); ?>
                                                    </div>
                                                    <div class="text-muted" style="font-size: 0.875rem;">
                                                        <?php echo htmlspecialchars($transaction['account_name']); ?> • 
                                                        <?php echo date('d M Y', strtotime($transaction['transaction_date'])); ?>
                                                    </div>
                                                    <?php if ($transaction['description']): ?>
                                                        <div class="text-muted" style="font-size: 0.75rem;">
                                                            <?php echo htmlspecialchars($transaction['description']); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div style="font-weight: 700; font-size: 1.125rem;" class="text-<?php echo $transaction['transaction_type'] === 'income' ? 'success' : 'danger'; ?>">
                                                <?php echo $transaction['transaction_type'] === 'income' ? '+' : '-'; ?>
                                                <?php echo format_currency($transaction['amount']); ?>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 style="margin: 0;">Accounts Overview</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($accounts)): ?>
                                <p class="text-muted text-center p-4">No accounts yet. Add your first account!</p>
                            <?php else: ?>
                                <ul style="list-style: none; padding: 0; margin: 0;">
                                    <?php foreach ($accounts as $account): ?>
                                        <li style="padding: 1rem 0; border-bottom: 1px solid var(--dark-border);">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div>
                                                    <div style="font-weight: 600;">
                                                        <?php echo htmlspecialchars($account['name']); ?>
                                                    </div>
                                                    <div class="text-muted" style="font-size: 0.75rem; text-transform: uppercase;">
                                                        <?php echo htmlspecialchars($account['type']); ?>
                                                    </div>
                                                </div>
                                                <span class="badge badge-<?php echo $account['current_balance'] >= 0 ? 'success' : 'danger'; ?>">
                                                    <?php echo $account['status']; ?>
                                                </span>
                                            </div>
                                            <div style="font-size: 1.25rem; font-weight: 700;">
                                                <?php echo format_currency($account['current_balance']); ?>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Add Transaction Modal -->
    <div id="addTransactionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add Transaction</h3>
                <button class="modal-close">&times;</button>
            </div>
            <form id="addTransactionForm">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="hidden" name="type" id="transactionType">
                
                <div class="form-group">
                    <label class="form-label">Account</label>
                    <select name="account_id" class="form-control form-select" required>
                        <option value="">Select Account</option>
                        <?php foreach ($accounts as $account): ?>
                            <option value="<?php echo $account['id']; ?>">
                                <?php echo htmlspecialchars($account['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control" placeholder="e.g., Food, Salary" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Amount</label>
                    <input type="number" name="amount" class="form-control" placeholder="0" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description (Optional)</label>
                    <textarea name="description" class="form-control" placeholder="Add notes about this transaction"></textarea>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> Save Transaction
                    </button>
                    <button type="button" class="btn btn-outline btn-block" onclick="closeModal()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="assets/js/main.js"></script>
    <script>
        const modal = new Modal('addTransactionModal');
        
        function openAddTransactionModal(type) {
            document.getElementById('transactionType').value = type;
            document.getElementById('modalTitle').textContent = 'Add ' + (type === 'income' ? 'Income' : 'Expense');
            modal.show();
        }
        
        function closeModal() {
            modal.hide();
        }
        
        // Handle transaction form submission
        document.getElementById('addTransactionForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const submitBtn = e.target.querySelector('button[type="submit"]');
            
            setLoading(submitBtn, true);
            
            try {
                const response = await fetch('api/transactions.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.status) {
                    showAlert(result.message, 'success');
                    modal.hide();
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
        
        // Resolve anomaly
        async function resolveAnomaly(id) {
            if (!await confirmAction('Mark this alert as resolved?')) return;
            
            try {
                const response = await fetch('api/anomalies.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'resolve',
                        anomaly_id: id,
                        csrf_token: getCsrfToken()
                    })
                });
                
                const result = await response.json();
                if (result.status) {
                    showAlert('Alert resolved successfully', 'success');
                    location.reload();
                }
            } catch (error) {
                showAlert('Failed to resolve alert', 'danger');
            }
        }
    </script>
</body>
</html>
