# Cash Management System 💰

A comprehensive web-based cash management system designed for organizations and communities with AI-powered anomaly detection, multi-tenant architecture, and enterprise-grade security.

## 🌟 Features

### Core Features
- **Multi-Tenant Architecture**: Separate data for each organization
- **User Authentication & Authorization**: Secure login with role-based access control
- **Account Management**: Support for multiple cash sources (Bank, E-Wallet, Cash, etc.)
- **Transaction Management**: Easy income and expense tracking
- **Automatic Balance Consolidation**: All balances combined into one view
- **Transaction History**: View all transactions from different accounts
- **AI-Based Anomaly Detection**: Intelligent alerts for suspicious activities

### AI Anomaly Detection
The system uses rule-based algorithms to detect:
- **High Expense Alerts**: Transactions significantly above average
- **Rapid Transaction Detection**: Multiple transactions in short time
- **Negative Balance Warnings**: Account balance drops below zero
- **Duplicate Transaction Detection**: Suspicious repeated transactions
- **Pattern Analysis**: Statistical analysis of spending patterns

### Security Features
✅ **Password Security**
- Argon2id/Bcrypt hashing
- Minimum complexity requirements
- Password strength validation

✅ **Brute Force Protection**
- Failed login attempt tracking
- Account lockout after 5 failed attempts
- 15-minute lockout period

✅ **Session Management**
- Secure session handling with HTTPOnly cookies
- Session expiration after inactivity
- IP address and user agent tracking

✅ **CSRF Protection**
- Token-based CSRF protection on all forms
- Auto-generated secure tokens

✅ **SQL Injection Prevention**
- Prepared statements with PDO
- Input sanitization and validation

✅ **XSS Protection**
- HTML entity encoding
- Content Security Policy headers

✅ **Activity Logging**
- Complete audit trail
- User action tracking
- IP address logging

## 🛠 Tech Stack

### Backend
- PHP 7.4+ (Native PHP)
- MySQL 5.7+ / MariaDB 10.3+
- PDO for database access

### Frontend
- HTML5
- CSS3 (Custom framework with modern design)
- JavaScript (Vanilla JS)
- Font Awesome icons
- Google Fonts (Inter)

### Security
- Password hashing (Argon2id/Bcrypt)
- CSRF token protection
- Prepared statements
- Session management
- Input validation & sanitization

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache/Nginx web server
- mod_rewrite enabled (for Apache)
- PDO MySQL extension
- JSON extension
- mbstring extension

## 🚀 Installation

### 1. Download and Extract
```bash
# Extract the files to your web server directory
# For XAMPP: C:\xampp\htdocs\cash-management-system
# For WAMP: C:\wamp64\www\cash-management-system
```

### 2. Configure Database

**Option A: Using phpMyAdmin**
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `cash_management_system`
3. Click on the database
4. Go to "Import" tab
5. Select the `database.sql` file
6. Click "Go" to import

**Option B: Using Command Line**
```bash
mysql -u root -p
# Enter your MySQL password

# Create database
CREATE DATABASE cash_management_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Import the database
USE cash_management_system;
SOURCE /path/to/database.sql;
```

### 3. Configure Application

Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'cash_management_system');
define('DB_USER', 'root');        // Change in production
define('DB_PASS', '');            // Change in production
```

Edit `config/config.php`:
```php
// Change BASE_URL to your local URL
define('BASE_URL', 'http://localhost/cash-management-system');
```

### 4. Set Permissions

For Linux/Mac:
```bash
chmod 755 /path/to/cash-management-system
chmod 777 /path/to/cash-management-system/uploads
chmod 777 /path/to/cash-management-system/logs
```

For Windows (XAMPP/WAMP):
- No special permissions needed
- Ensure the web server has write access to `uploads` and `logs` folders

### 5. Access the Application

1. Open your browser
2. Navigate to: `http://localhost/cash-management-system`
3. Click "Register" to create your first organization
4. Fill in the registration form
5. Login with your credentials

## 📱 Usage Guide

### First Time Setup

1. **Register Organization**
   - Go to registration page
   - Enter organization details
   - Create admin account
   - Accept terms and conditions

2. **Create Accounts**
   - Login to dashboard
   - Click "Manage Accounts"
   - Add your bank accounts, e-wallets, or cash accounts
   - Set initial balances

3. **Add Transactions**
   - Use "Quick Actions" on dashboard
   - Select "Add Income" or "Add Expense"
   - Fill in transaction details
   - Submit

4. **Monitor Finances**
   - View dashboard for overview
   - Check AI alerts for anomalies
   - Review transaction history
   - Generate reports

### User Roles

- **Admin**: Full access to all features
- **Manager**: Can view and manage transactions
- **User**: Can add transactions and view reports

## 🔒 Security Best Practices

### For Production Deployment

1. **Change Database Credentials**
   ```php
   define('DB_USER', 'your_secure_username');
   define('DB_PASS', 'your_strong_password');
   ```

2. **Enable HTTPS**
   ```php
   // In config/config.php
   ini_set('session.cookie_secure', 1); // Change to 1
   define('BASE_URL', 'https://yourdomain.com');
   ```

3. **Disable Error Display**
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 0); // Already set
   ini_set('log_errors', 1);
   ```

4. **Secure File Uploads**
   - Move `uploads` folder outside web root
   - Validate file types
   - Scan for malware

5. **Regular Backups**
   - Backup database daily
   - Store backups securely
   - Test restore procedures

6. **Update Regularly**
   - Keep PHP updated
   - Update dependencies
   - Monitor security advisories

## 🐛 Troubleshooting

### Common Issues

**Database Connection Error**
```
Solution:
1. Check database credentials in config/database.php
2. Ensure MySQL service is running
3. Verify database exists
4. Check user permissions
```

**Session Not Working**
```
Solution:
1. Ensure session.save_path is writable
2. Check PHP session settings
3. Clear browser cookies
4. Restart web server
```

**CSS/JS Not Loading**
```
Solution:
1. Check BASE_URL in config/config.php
2. Verify file paths are correct
3. Clear browser cache
4. Check file permissions
```

**Password Requirements Not Met**
```
Password must contain:
- Minimum 8 characters
- At least one uppercase letter
- At least one lowercase letter
- At least one number
- At least one special character (@$!%*?&#)
```

## 📊 Database Schema

### Main Tables
- `organizations` - Organization data
- `users` - User accounts
- `account_sources` - Cash sources (bank, e-wallet, etc.)
- `transactions` - All financial transactions
- `categories` - Transaction categories
- `anomaly_logs` - AI detection results
- `activity_logs` - Security audit trail
- `sessions` - Session management

## 🎨 Design Features

- **Dark Theme**: Modern dark UI with gradient accents
- **Glassmorphism**: Frosted glass effects
- **Smooth Animations**: Micro-interactions for better UX
- **Responsive Design**: Works on desktop, tablet, and mobile
- **Premium Aesthetics**: Professional and polished design

## 📈 Future Enhancements

- [ ] Export to Excel/PDF
- [ ] Budget planning and tracking
- [ ] Recurring transactions
- [ ] Multi-currency support
- [ ] Email notifications
- [ ] Mobile app integration
- [ ] Advanced reporting and analytics
- [ ] Integration with banking APIs
- [ ] WhatsApp notifications
- [ ] Two-factor authentication

## 🤝 Support

For issues or questions:
1. Check the troubleshooting section
2. Review error logs in `logs/error.log`
3. Check database structure matches schema
4. Ensure all requirements are met

## 📄 License

This project is developed for organizational use. All rights reserved.

## ⚠️ Important Notes

1. **Never commit sensitive data** to version control
2. **Change default passwords** before production use
3. **Enable HTTPS** in production
4. **Regular backups** are essential
5. **Monitor logs** for suspicious activity
6. **Keep software updated** for security patches

## 🎯 Demo Credentials

After installation, register your own organization. The system does not include default credentials for security reasons.

---

**Built with ❤️ for better financial management**

Version 1.0.0 | Last Updated: February 2026
