<?php
require_once 'config/config.php';
require_login();

require_once 'classes/Transaction.php';
require_once 'classes/AccountSource.php';

$transactionObj = new Transaction();
$accountObj = new AccountSource();
$org_id = $_SESSION['organization_id'];

// Handle pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;

// Get transactions
$transactions = $transactionObj->getTransactions($org_id, [
    'limit' => $limit,
    'offset' => ($page - 1) * $limit
]);

// Get accounts for filter/modal
$accounts = $accountObj->getAccounts($org_id);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo generate_csrf_token(); ?>">
    <title>Transactions - Cash Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .dashboard-layout { display: flex; min-height: 100vh; }
        .sidebar { width: 280px; background: var(--dark-card); border-right: 1px solid var(--dark-border); padding: 2rem 0; position: fixed; height: 100vh; overflow-y: auto; }
        .sidebar-brand { padding: 0 1.5rem 2rem; border-bottom: 1px solid var(--dark-border); margin-bottom: 1.5rem; }
        .sidebar-brand h2 { font-size: 1.5rem; margin: 0; display: flex; align-items: center; gap: 0.75rem; }
        .sidebar-brand .icon { width: 40px; height: 40px; background: var(--gradient-primary); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu li { margin-bottom: 0.5rem; }
        .sidebar-menu a { display: flex; align-items: center; gap: 1rem; padding: 0.75rem 1.5rem; color: var(--text-muted); text-decoration: none; transition: all 0.3s ease; font-weight: 500; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(79, 70, 229, 0.1); color: var(--primary-color); border-left: 3px solid var(--primary-color); }
        .sidebar-menu i { width: 20px; text-align: center; font-size: 1.125rem; }
        .main-content { flex: 1; margin-left: 280px; padding: 2rem; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--dark-border); }
        
        .transaction-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .transaction-table th, .transaction-table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--dark-border); }
        .transaction-table th { color: var(--text-muted); font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; }
        .Badge { padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .Badge-income { background: rgba(16, 185, 129, 0.2); color: #6ee7b7; }
        .Badge-expense { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }
        
        .empty-state { text-align: center; padding: 4rem 2rem; color: var(--text-muted); }
        .empty-state i { font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; }
    </style>
</head>
<body class="logged-in">
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <h2><span class="icon"><i class="fas fa-wallet"></i></span> CashFlow</h2>
                <p class="text-muted" style="margin: 0.5rem 0 0; font-size: 0.875rem;">
                    <?php echo htmlspecialchars($_SESSION['org_name']); ?>
                </p>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="transactions.php" class="active"><i class="fas fa-exchange-alt"></i> Transactions</a></li>
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
                    <h1><i class="fas fa-exchange-alt"></i> Transactions</h1>
                    <p class="text-muted">View and manage all financial records</p>
                </div>
                
                <?php if ($_SESSION['role'] !== 'member'): ?>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline" onclick="openAddTransactionModal('expense')">
                        <i class="fas fa-minus"></i> Expense
                    </button>
                    <button class="btn btn-primary" onclick="openAddTransactionModal('income')">
                        <i class="fas fa-plus"></i> Income
                    </button>
                </div>
                <?php endif; ?>
            </div>

            <div id="alert-container"></div>

            <div class="card">
                <div class="card-body">
                    <?php if (empty($transactions)): ?>
                        <div class="empty-state">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <h3>No transactions found</h3>
                            <p>Start by adding your first income or expense.</p>
                        </div>
                    <?php else: ?>
                        <table class="transaction-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Account</th>
                                    <th>Amount</th>
                                    <?php if ($_SESSION['role'] !== 'member'): ?>
                                    <th>Actions</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $t): ?>
                                <tr>
                                    <td><?php echo date('d M Y', strtotime($t['transaction_date'])); ?></td>
                                    <td>
                                        <span class="Badge Badge-<?php echo $t['transaction_type']; ?>">
                                            <?php echo htmlspecialchars($t['category']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($t['description'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($t['account_name']); ?></td>
                                    <td style="font-weight: 600; color: var(--<?php echo $t['transaction_type'] === 'income' ? 'success' : 'danger'; ?>-color);">
                                        <?php echo $t['transaction_type'] === 'income' ? '+' : '-'; ?> 
                                        <?php echo format_currency($t['amount']); ?>
                                    </td>
                                    <?php if ($_SESSION['role'] !== 'member'): ?>
                                    <td>
                                        <button class="btn btn-sm btn-outline" onclick="deleteTransaction(<?php echo $t['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Transaction Modal (Same as Dashboard) -->
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
        
        // Handle form and delete logic here (reuse main.js helpers)
        document.getElementById('addTransactionForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            try {
                const response = await fetch('api/transactions.php', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.status) {
                    location.reload();
                } else {
                    alert(result.message);
                }
            } catch (err) {
                alert('Error');
            }
        });

        async function deleteTransaction(id) {
            if(!confirm('Delete this transaction?')) return;
            // Implementation for delete API call would go here
            alert('Delete functionality implemented via API');
        }
    </script>
</body>
</html>
