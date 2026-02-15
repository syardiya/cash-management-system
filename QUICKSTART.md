# ⚡ Quick Start Guide

## 🎯 Get Started in 5 Minutes!

### Prerequisites
- ✅ XAMPP installed (includes Apache + MySQL + PHP)
- ✅ Web browser (Chrome recommended)

### Step 1: Start Services (30 seconds)
1. Open **XAMPP Control Panel**
2. Click **Start** for Apache (wait for green)
3. Click **Start** for MySQL (wait for green)

### Step 2: Setup Database (2 minutes)
1. Open browser: `http://localhost/phpmyadmin`
2. Click **"New"** on left sidebar
3. Database name: `cash_management_system`
4. Collation: `utf8mb4_unicode_ci`
5. Click **"Create"**
6. Click your database name
7. Click **"Import"** tab
8. Choose file: `database.sql` (from project folder)
9. Click **"Go"** at bottom
10. ✅ Success! Database created

### Step 3: Configure App (1 minute)
1. Open `config/database.php` in text editor
2. Verify these settings:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'cash_management_system');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Empty for XAMPP
   ```

3. Open `config/config.php`
4. Verify BASE_URL:
   ```php
   define('BASE_URL', 'http://localhost/cash-management-system');
   ```

### Step 4: Launch App (30 seconds)
1. Open browser
2. Go to: `http://localhost/cash-management-system`
3. You should see the login page! 🎉

### Step 5: Create Account (1 minute)
1. Click **"Register here"**
2. Fill in organization details:
   - Organization Name: Your Company
   - Email: company@example.com
   - Phone: (optional)

3. Create admin account:
   - Full Name: Your Name
   - Username: admin
   - Email: admin@company.com
   - Password: `Admin123!` (or your own)
   - Confirm password
   - Check "I agree"
   - Click **"Create Account"**

4. Click **"Login here"**
5. Enter your credentials
6. ✅ Welcome to your dashboard!

## 🚀 Your First Transaction

### Add a Bank Account
1. On dashboard, click **"Manage Accounts"** or **"Add Account"**
2. Fill in:
   - Account Name: "My Bank"
   - Type: Bank
   - Initial Balance: 10000000 (10 million IDR)
   - Click **"Save Account"**

### Add Income
1. Go back to Dashboard
2. Click **"Add Income"**
3. Fill in:
   - Account: My Bank
   - Category: Salary
   - Amount: 5000000
   - Date: Today
   - Description: Monthly salary
   - Click **"Save Transaction"**

### Add Expense
1. Click **"Add Expense"**
2. Fill in:
   - Account: My Bank
   - Category: Food & Dining
   - Amount: 150000
   - Description: Groceries
   - Click **"Save Transaction"**

## 🤖 Test AI Detection

### Trigger High Expense Alert
1. Click **"Add Expense"**
2. Enter amount: **20000000** (way above normal)
3. Category: Shopping
4. Save transaction
5. Go to **"AI Alerts"** page
6. ✅ You should see an anomaly alert!

### Trigger Rapid Transaction Alert
1. Add 6 transactions quickly (within 5 minutes)
2. Check **"AI Alerts"**
3. ✅ You'll see a rapid transaction warning

## 📊 Explore Features

### Check Dashboard
- View total balance (combines all accounts)
- See this month's income/expense
- Review recent transactions
- Monitor AI alerts

### Manage Accounts
- Add multiple accounts (bank, e-wallet, cash)
- View individual balances
- Edit or deactivate accounts

### View AI Alerts
- See active alerts
- Read AI explanations
- Review suggested actions
- Mark alerts as resolved

## 🎓 Pro Tips

### Password Requirements
- Minimum 8 characters
- Must have: uppercase, lowercase, number, special char
- Example good password: `SecurePass123!`

### Adding Multiple Accounts
Add all your real accounts:
- Bank accounts
- E-wallets (GoPay, OVO, Dana, ShopeePay)
- Cash on hand
- Investment accounts

### Categories
The system comes with default categories:
- **Income**: Salary, Investment Returns, Donations
- **Expense**: Food, Transport, Utilities, Shopping, etc.

### Best Practices
1. ✅ Add transactions daily
2. ✅ Use descriptive names for accounts
3. ✅ Check AI alerts regularly
4. ✅ Review dashboard weekly
5. ✅ Keep accurate initial balances

## ⚠️ Troubleshooting Quick Fixes

### Can't Access `localhost/cash-management-system`
- ✅ Check Apache is running (green in XAMPP)
- ✅ Check folder is in `C:\xampp\htdocs\`
- ✅ Try: `http://127.0.0.1/cash-management-system`

### "Database connection error"
- ✅ Check MySQL is running (green in XAMPP)
- ✅ Verify database was created in phpMyAdmin
- ✅ Check database name is exactly: `cash_management_system`

### Blank white page
- ✅ Check `logs/error.log` for errors
- ✅ Verify all files were copied correctly
- ✅ Check PHP version: `http://localhost/dashboard` in XAMPP

### CSS not loading / looks broken
- ✅ Clear browser cache (Ctrl + Shift + Delete)
- ✅ Check `assets/css/style.css` exists
- ✅ Verify BASE_URL in config is correct

## 📱 Access from Phone (Same Network)

1. Find your computer's IP:
   ```
   cmd → ipconfig → look for IPv4
   Example: 192.168.1.100
   ```

2. On phone browser:
   ```
   http://192.168.1.100/cash-management-system
   ```

3. Make sure phone and PC are on same WiFi

## 🔐 Security Reminders

For testing/development:
- ✅ Use only on localhost
- ✅ Don't expose to internet
- ✅ Use strong passwords

**Never use this for production without:**
- 🔒 HTTPS enabled
- 🔒 Changed database password
- 🔒 Proper server security
- 🔒 Regular backups

## 🎉 You're All Set!

You now have a fully functional cash management system with:
- ✅ Multi-account support
- ✅ Automatic balance consolidation
- ✅ AI anomaly detection
- ✅ Transaction history
- ✅ Beautiful dashboard
- ✅ Secure authentication

## 📚 Next Steps

1. Read the full [README.md](README.md)
2. Check [INSTALLATION.md](INSTALLATION.md) for details
3. Review [PROJECT_STRUCTURE.md](PROJECT_STRUCTURE.md)
4. Start adding your real financial data!

---

**Need help?** Check the documentation files or review error logs.

**Enjoying the system?** Share it with your organization!

🚀 Happy managing your finances!
