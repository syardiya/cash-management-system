<?php
require_once 'config/config.php';
require_login();

$db = Database::getInstance();
$conn = $db->getConnection();

// Get anomalies
$stmt = $conn->prepare("
    SELECT a.*, 
           t.amount, t.category, t.transaction_date,
           u.full_name as resolved_by_name
    FROM anomaly_logs a
    LEFT JOIN transactions t ON a.transaction_id = t.id
    LEFT JOIN users u ON a.resolved_by = u.id
    WHERE a.organization_id = ?
    ORDER BY a.is_resolved ASC, a.detected_at DESC
");
$stmt->execute([$_SESSION['organization_id']]);
$anomalies = $stmt->fetchAll();

// Group by resolved status
$active_anomalies = array_filter($anomalies, fn($a) => !$a['is_resolved']);
$resolved_anomalies = array_filter($anomalies, fn($a) => $a['is_resolved']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo generate_csrf_token(); ?>">
    <title>AI Anomaly Alerts - Cash Management System</title>
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
        
        .anomaly-card {
            background: var(--dark-card);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid;
            transition: all 0.3s ease;
        }
        
        .anomaly-card:hover {
            transform: translateX(4px);
            box-shadow: var(--shadow-lg);
        }
        
        .anomaly-card.critical {
            border-color: #dc2626;
            background: rgba(220, 38, 38, 0.05);
        }
        
        .anomaly-card.high {
            border-color: #ef4444;
            background: rgba(239, 68, 68, 0.05);
        }
        
        .anomaly-card.medium {
            border-color: #f59e0b;
            background: rgba(245, 158, 11, 0.05);
        }
        
        .anomaly-card.low {
            border-color: #3b82f6;
            background: rgba(59, 130, 246, 0.05);
        }
        
        .anomaly-card.resolved {
            opacity: 0.6;
            border-color: var(--success-color);
            background: rgba(16, 185, 129, 0.05);
        }
        
        .anomaly-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }
        
        .anomaly-type {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .anomaly-type-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-box {
            background: var(--dark-card);
            padding: 1.5rem;
            border-radius: 1rem;
            border: 1px solid var(--dark-border);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid var(--dark-border);
        }
        
        .tab {
            padding: 1rem 1.5rem;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.3s ease;
            font-weight: 600;
            color: var(--text-muted);
        }
        
        .tab.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
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
                <li><a href="accounts.php"><i class="fas fa-wallet"></i> Accounts</a></li>
                <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                <li><a href="anomalies.php" class="active"><i class="fas fa-shield-alt"></i> AI Alerts</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="top-bar">
                <div>
                    <h1><i class="fas fa-shield-alt"></i> AI Anomaly Detection</h1>
                    <p class="text-muted">Intelligent alerts for suspicious transactions and unusual patterns</p>
                </div>
            </div>
            
            <div id="alert-container"></div>
            
            <!-- Statistics Summary -->
            <div class="stats-summary">
                <div class="stat-box">
                    <div class="stat-number text-danger"><?php echo count($active_anomalies); ?></div>
                    <div class="text-muted">Active Alerts</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number text-success"><?php echo count($resolved_anomalies); ?></div>
                    <div class="text-muted">Resolved</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number text-warning">
                        <?php 
                        $critical = count(array_filter($active_anomalies, fn($a) => $a['severity'] === 'critical'));
                        echo $critical;
                        ?>
                    </div>
                    <div class="text-muted">Critical</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number text-primary"><?php echo count($anomalies); ?></div>
                    <div class="text-muted">Total Detected</div>
                </div>
            </div>
            
            <!-- Tabs -->
            <div class="tabs">
                <div class="tab active" onclick="switchTab('active')">
                    <i class="fas fa-exclamation-circle"></i> Active Alerts (<?php echo count($active_anomalies); ?>)
                </div>
                <div class="tab" onclick="switchTab('resolved')">
                    <i class="fas fa-check-circle"></i> Resolved (<?php echo count($resolved_anomalies); ?>)
                </div>
            </div>
            
            <!-- Active Anomalies -->
            <div id="active-tab" class="tab-content active">
                <?php if (empty($active_anomalies)): ?>
                    <div class="card text-center p-5">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem; margin-bottom: 1rem;"></i>
                        <h3>No Active Alerts</h3>
                        <p class="text-muted">All your transactions look good! Our AI hasn't detected any anomalies.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($active_anomalies as $anomaly): ?>
                        <div class="anomaly-card <?php echo $anomaly['severity']; ?>">
                            <div class="anomaly-header">
                                <div class="flex-1">
                                    <div class="anomaly-type">
                                        <div class="anomaly-type-icon" style="background: rgba(239, 68, 68, 0.1); color: var(--danger-color);">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex gap-2 align-items-center">
                                                <span class="badge badge-<?php echo $anomaly['severity']; ?>">
                                                    <?php echo strtoupper($anomaly['severity']); ?>
                                                </span>
                                                <span class="badge badge-warning">
                                                    <?php echo str_replace('_', ' ', strtoupper($anomaly['anomaly_type'])); ?>
                                                </span>
                                            </div>
                                            <div class="text-muted" style="font-size: 0.875rem; margin-top: 0.25rem;">
                                                Detected <?php echo formatDateTime($anomaly['detected_at']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-success" onclick="resolveAnomaly(<?php echo $anomaly['id']; ?>)">
                                    <i class="fas fa-check"></i> Resolve
                                </button>
                            </div>
                            
                            <div style="padding-left: 3.5rem;">
                                <h4 style="margin-bottom: 0.5rem; font-size: 1.125rem;">
                                    <?php echo htmlspecialchars($anomaly['description']); ?>
                                </h4>
                                
                                <?php if ($anomaly['transaction_id']): ?>
                                    <div style="background: var(--dark-bg); padding: 0.75rem; border-radius: 0.5rem; margin: 1rem 0;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div style="font-weight: 600;">
                                                    <?php echo htmlspecialchars($anomaly['category']); ?>
                                                </div>
                                                <div class="text-muted" style="font-size: 0.875rem;">
                                                    <?php echo date('d M Y', strtotime($anomaly['transaction_date'])); ?>
                                                </div>
                                            </div>
                                            <div style="font-size: 1.25rem; font-weight: 700; color: var(--danger-color);">
                                                <?php echo format_currency($anomaly['amount']); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div style="background: rgba(79, 70, 229, 0.1); padding: 1rem; border-radius: 0.5rem; border-left: 3px solid var(--primary-color);">
                                    <div style="font-weight: 600; margin-bottom: 0.5rem; color: var(--primary-color);">
                                        <i class="fas fa-lightbulb"></i> Suggested Action
                                    </div>
                                    <p style="margin: 0; color: var(--text-light);">
                                        <?php echo htmlspecialchars($anomaly['suggested_action']); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Resolved Anomalies -->
            <div id="resolved-tab" class="tab-content">
                <?php if (empty($resolved_anomalies)): ?>
                    <div class="card text-center p-5">
                        <i class="fas fa-inbox text-muted" style="font-size: 4rem; margin-bottom: 1rem;"></i>
                        <h3>No Resolved Alerts</h3>
                        <p class="text-muted">Resolved anomalies will appear here.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($resolved_anomalies as $anomaly): ?>
                        <div class="anomaly-card resolved">
                            <div class="anomaly-header">
                                <div class="flex-1">
                                    <div class="anomaly-type">
                                        <div class="anomaly-type-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success-color);">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex gap-2 align-items-center">
                                                <span class="badge badge-success">RESOLVED</span>
                                                <span class="badge badge-primary">
                                                    <?php echo str_replace('_', ' ', strtoupper($anomaly['anomaly_type'])); ?>
                                                </span>
                                            </div>
                                            <div class="text-muted" style="font-size: 0.875rem;">
                                                Resolved by <?php echo htmlspecialchars($anomaly['resolved_by_name']); ?> 
                                                on <?php echo formatDateTime($anomaly['resolved_at']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div style="padding-left: 3.5rem;">
                                <p style="margin: 0;"><?php echo htmlspecialchars($anomaly['description']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <script src="assets/js/main.js"></script>
    <script>
        function switchTab(tabName) {
            // Update tab buttons
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            event.target.closest('.tab').classList.add('active');
            
            // Update tab content
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            document.getElementById(tabName + '-tab').classList.add('active');
        }
        
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
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(result.message, 'danger');
                }
            } catch (error) {
                showAlert('Failed to resolve alert', 'danger');
            }
        }
    </script>
</body>
</html>
