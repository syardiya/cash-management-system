# 🚀 Installation Guide - Cash Management System

## Prerequisites Check

Before installation, ensure you have:
- ✅ PHP 7.4 or higher
- ✅ MySQL 5.7+ or MariaDB 10.3+
- ✅ Apache or Nginx web server
- ✅ Web browser (Chrome, Firefox, Edge)

## Step-by-Step Installation

### 1️⃣ Install XAMPP (Windows Users)

If you don't have a web server installed:

1. **Download XAMPP**
   - Visit: https://www.apachefriends.org/
   - Download XAMPP for Windows
   - Choose the latest version with PHP 7.4+

2. **Install XAMPP**
   - Run the installer
   - Install to `C:\xampp` (default location)
   - Select Apache and MySQL components

3. **Start Services**
   - Open XAMPP Control Panel
   - Click "Start" for Apache
   - Click "Start" for MySQL
   - Both should show green "Running" status

### 2️⃣ Copy Project Files

1. **Locate Your Web Directory**
   ```
   XAMPP: C:\xampp\htdocs\
   WAMP: C:\wamp64\www\
   ```

2. **Copy Files**
   - Copy the entire `cash-management-system` folder
   - Paste into your web directory
   - Final path should be: `C:\xampp\htdocs\cash-management-system\`

### 3️⃣ Create Database

**Option A: Using phpMyAdmin (Recommended for Beginners)**

1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click "New" on the left sidebar
3. Database name: `cash_management_system`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"
6. Click on your new database
7. Go to "Import" tab
8. Click "Choose File"
9. Select `database.sql` from your project folder
10. Scroll down and click "Go"
11. Wait for success message

**Option B: Using Command Line**

```bash
# Open Command Prompt
# Navigate to MySQL bin folder
cd C:\xampp\mysql\bin

# Login to MySQL
mysql -u root -p
# Press Enter (default password is empty)

# Create database
CREATE DATABASE cash_management_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Use the database
USE cash_management_system;

# Import the SQL file
SOURCE C:/xampp/htdocs/cash-management-system/database.sql;

# Verify tables were created
SHOW TABLES;

# Exit MySQL
EXIT;
```

### 4️⃣ Configure Application

1. **Edit Database Configuration**
   
   Open: `config/database.php`
   
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'cash_management_system');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Leave empty for XAMPP default
   ```

2. **Edit Application Configuration**
   
   Open: `config/config.php`
   
   ```php
   define('BASE_URL', 'http://localhost/cash-management-system');
   ```

### 5️⃣ Set File Permissions (Windows)

1. **Create Required Folders**
   - Right-click on project folder
   - New → Folder
   - Create these folders:
     - `uploads`
     - `logs`

2. **Set Permissions** (Usually automatic on Windows)
   - Right-click folder → Properties
   - Security tab → Edit
   - Ensure "Users" has full control

### 6️⃣ Test Installation

1. **Open Browser**
   - Navigate to: `http://localhost/cash-management-system`
   - You should see the login page

2. **Register Your Organization**
   - Click "Register here"
   - Fill in organization details
   - Create admin account
   - Submit registration

3. **Login**
   - Use your created credentials
   - You should see the dashboard

## ✅ Verification Checklist

After installation, verify:

- [ ] Can access login page
- [ ] Can register new organization
- [ ] Can login successfully
- [ ] Dashboard loads without errors
- [ ] Can add new account
- [ ] Can add transaction
- [ ] AI alerts are working
- [ ] No PHP errors displayed

## 🔧 Troubleshooting

### Problem: "Can't connect to database"

**Solution:**
1. Check MySQL is running in XAMPP
2. Verify database name is correct
3. Verify database exists in phpMyAdmin
4. Check database credentials in `config/database.php`

### Problem: "Page not found" or 404 error

**Solution:**
1. Check project is in correct folder (`htdocs`)
2. Verify Apache is running
3. Check BASE_URL in `config/config.php`
4. Clear browser cache

### Problem: Blank white page

**Solution:**
1. Enable error display temporarily
2. Edit `config/config.php`:
   ```php
   ini_set('display_errors', 1);
   ```
3. Refresh page to see errors
4. Check PHP error log in `logs/error.log`

### Problem: CSS/JavaScript not loading

**Solution:**
1. Check BASE_URL is correct
2. Verify files exist in `assets/css/` and `assets/js/`
3. Clear browser cache (Ctrl + Shift + Delete)
4. Check browser console for errors (F12)

### Problem: Can't upload files

**Solution:**
1. Check `uploads` folder exists
2. Verify folder permissions
3. Check PHP upload limits in `php.ini`

### Problem: Session errors

**Solution:**
1. Check `session.save_path` in `php.ini`
2. Ensure temp folder is writable
3. Clear browser cookies
4. Restart Apache

## 🎯 First Time Setup Guide

### Step 1: Create Your Organization

1. Go to registration page
2. Fill in:
   - Organization Name: "Your Company Name"
   - Email: your@email.com
   - Phone: (optional)
   - Address: (optional)

3. Create admin account:
   - Full Name: "Your Name"
   - Username: choose a username
   - Email: admin@yourcompany.com
   - Password: Use a strong password!
     - Min 8 characters
     - Include uppercase, lowercase
     - Include numbers
     - Include special characters (@$!%*?&#)

### Step 2: Set Up Accounts

1. Login to dashboard
2. Click "Manage Accounts" or "Add Account"
3. Add your first account:
   - Name: "Main Bank Account"
   - Type: Bank
   - Initial Balance: Your current balance
   - Click Save

4. Add more accounts:
   - E-wallets (GoPay, OVO, Dana)
   - Cash accounts
   - Investment accounts

### Step 3: Add Your First Transaction

1. On dashboard, click "Add Income" or "Add Expense"
2. Select account
3. Choose category
4. Enter amount
5. Add description
6. Save

### Step 4: Monitor AI Alerts

The system will automatically:
- Detect unusually high expenses
- Alert on rapid transactions
- Warn about negative balances
- Identify suspicious patterns

Check the "AI Alerts" page regularly!

## 📊 Sample Data (Optional)

Want to see how it works with sample data?

1. Go to phpMyAdmin
2. Select your database
3. Click SQL tab
4. Copy and paste sample transactions
5. Click Go

```sql
-- Sample transactions (adjust IDs as needed)
INSERT INTO transactions (organization_id, account_source_id, transaction_type, category, amount, description, transaction_date, created_by)
VALUES 
(1, 1, 'income', 'Salary', 5000000, 'Monthly salary', CURDATE(), 1),
(1, 1, 'expense', 'Food & Dining', 150000, 'Groceries', CURDATE(), 1),
(1, 1, 'expense', 'Transportation', 200000, 'Fuel', CURDATE(), 1);
```

## 🔐 Security Recommendations

### For Development:
1. ✅ Use localhost only
2. ✅ Don't expose to internet
3. ✅ Use strong passwords

### For Production:
1. 🔒 Enable HTTPS
2. 🔒 Change database password
3. 🔒 Set strong session security
4. 🔒 Regular backups
5. 🔒 Update PHP regularly
6. 🔒 Monitor error logs

## 📱 Browser Compatibility

Tested and works on:
- ✅ Google Chrome (Recommended)
- ✅ Mozilla Firefox
- ✅ Microsoft Edge
- ✅ Safari
- ⚠️ Internet Explorer (Not recommended)

## 🆘 Getting Help

If you encounter issues:

1. **Check Error Logs**
   - Location: `logs/error.log`
   - Look for PHP errors and warnings

2. **Enable Debug Mode**
   - Edit `config/config.php`
   - Set `ini_set('display_errors', 1);`
   - Reload page and check errors

3. **Common Issues**
   - Database connection: Check credentials
   - File permissions: Ensure folders are writable
   - PHP version: Must be 7.4+
   - Extensions: Enable PDO, JSON, mbstring

## 🎉 Success!

If everything works:
1. ✅ You can login
2. ✅ Dashboard shows correctly
3. ✅ You can add transactions
4. ✅ AI detection is working

**Congratulations! Your Cash Management System is ready to use!**

## 📈 Next Steps

1. Customize categories for your needs
2. Add all your accounts
3. Import existing transaction data
4. Set up regular backups
5. Train your team on using the system
6. Monitor AI alerts regularly

---

**Need more help?** Check the README.md file for detailed documentation.

**Version:** 1.0.0  
**Last Updated:** February 2026
