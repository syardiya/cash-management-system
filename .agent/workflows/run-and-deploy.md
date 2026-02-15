---
description: How to run locally and deploy the Cash Management System
---

# 🚀 Running Locally & Deployment Guide

## 📍 PART 1: RUN LOCALLY (Development)

### Prerequisites

✅ XAMPP installed (includes Apache, MySQL, PHP)
✅ Web browser (Chrome recommended)

### Step 1: Start XAMPP Services

1. Open **XAMPP Control Panel**
2. Click **Start** for Apache (wait for green status)
3. Click **Start** for MySQL (wait for green status)

### Step 2: Setup Database

// turbo
```powershell
# Open phpMyAdmin in browser
Start-Process "http://localhost/phpmyadmin"
```

**Manual Steps in phpMyAdmin:**
1. Click **"New"** on left sidebar
2. Database name: `cash_management_system`
3. Collation: `utf8mb4_unicode_ci`
4. Click **"Create"**
5. Click your database name in the left sidebar
6. Click **"Import"** tab
7. Click **"Choose File"** and select: `c:\xampp\htdocs\cash-management-system\database.sql`
8. Scroll down and click **"Go"**
9. ✅ Wait for success message

### Step 3: Verify Configuration

The configuration files should already be set correctly:
- `config/database.php` → Database: `cash_management_system`, User: `root`, Password: empty
- `config/config.php` → BASE_URL: `http://localhost/cash-management-system`

### Step 4: Launch Application

// turbo
```powershell
# Open the application in browser
Start-Process "http://localhost/cash-management-system"
```

You should see the login page! 🎉

### Step 5: Create Your Account

1. Click **"Register here"**
2. Fill in organization details:
   - Organization Name: Your Company Name
   - Email: company@example.com
   - Phone: (optional)
3. Create admin account:
   - Full Name: Your Name
   - Username: admin
   - Email: admin@company.com
   - Password: Must meet requirements (e.g., `Admin123!`)
     - Min 8 characters
     - Uppercase, lowercase, number, special character
   - Confirm password
   - Check "I agree"
   - Click **"Create Account"**
4. Click **"Login here"**
5. Enter credentials and login
6. ✅ Welcome to your dashboard!

### Step 6: Add Your First Account & Transaction

1. Click **"Manage Accounts"** or **"Add Account"**
2. Fill in:
   - Account Name: "My Bank"
   - Type: Bank
   - Initial Balance: 10000000
   - Click **"Save Account"**
3. Go to Dashboard → Click **"Add Income"**
4. Fill details and save
5. ✅ You're ready to manage finances!

---

## 🌐 PART 2: HOSTING (Production Deployment)

### Option A: Shared Hosting (Easiest - Recommended for Small Organizations)

**Best Providers:**
- Hostinger (Indonesia) - from Rp 20,000/month
- Niagahoster (Indonesia) - from Rp 15,000/month
- Rumahweb (Indonesia) - from Rp 25,000/month
- Hostgator (International)
- Bluehost (International)

**Steps:**

1. **Purchase Hosting Package**
   - Choose package with: PHP 7.4+, MySQL, cPanel
   - Register domain (e.g., `yourcompany.com`)

2. **Upload Files**
   - Login to cPanel
   - Go to **File Manager**
   - Navigate to `public_html` folder
   - Upload all files from `c:\xampp\htdocs\cash-management-system`
   - Or use FileZilla FTP client

3. **Create Database**
   - In cPanel, go to **MySQL Databases**
   - Create database: `youruser_cashmanagement`
   - Create user with strong password
   - Add user to database with ALL PRIVILEGES
   - Note: username and password

4. **Import Database**
   - In cPanel, go to **phpMyAdmin**
   - Select your database
   - Click **Import** tab
   - Upload `database.sql`
   - Click **Go**

5. **Update Configuration**
   
   Edit `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'youruser_cashmanagement'); // Your database name
   define('DB_USER', 'youruser_dbuser');         // Your database user
   define('DB_PASS', 'your_strong_password');    // Your database password
   ```
   
   Edit `config/config.php`:
   ```php
   define('BASE_URL', 'https://yourcompany.com'); // Your domain
   ini_set('session.cookie_secure', 1); // Enable HTTPS (line 10)
   ```

6. **Set File Permissions**
   - In File Manager, right-click folders:
   - `uploads` → Change permissions to 755
   - `logs` → Change permissions to 755

7. **Enable SSL (HTTPS)**
   - In cPanel, go to **SSL/TLS Status**
   - Click **Run AutoSSL** (free)
   - Or install Let's Encrypt certificate
   - ✅ Your site is now secure!

8. **Test Your Site**
   - Visit `https://yourcompany.com`
   - Register your organization
   - Test all features
   - ✅ You're live!

---

### Option B: VPS Hosting (More Control - For Tech-Savvy Users)

**Best Providers:**
- DigitalOcean - from $5/month
- AWS Lightsail - from $3.50/month
- Vultr - from $2.50/month
- IDCloudHost (Indonesia) - from Rp 50,000/month

**Quick Setup (Ubuntu 22.04):**

1. **Create VPS Instance**
   - OS: Ubuntu 22.04 LTS
   - RAM: minimum 1GB
   - Storage: 25GB SSD

2. **Connect via SSH**
   ```bash
   ssh root@your-server-ip
   ```

3. **Install LAMP Stack**
   ```bash
   # Update system
   sudo apt update && sudo apt upgrade -y
   
   # Install Apache
   sudo apt install apache2 -y
   
   # Install MySQL
   sudo apt install mysql-server -y
   
   # Install PHP and extensions
   sudo apt install php php-mysql php-mbstring php-json php-curl -y
   
   # Restart Apache
   sudo systemctl restart apache2
   ```

4. **Configure MySQL**
   ```bash
   sudo mysql
   CREATE DATABASE cash_management_system;
   CREATE USER 'cashuser'@'localhost' IDENTIFIED BY 'strong_password_here';
   GRANT ALL PRIVILEGES ON cash_management_system.* TO 'cashuser'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   ```

5. **Upload Application**
   ```bash
   cd /var/www/html
   sudo rm index.html
   # Upload files via SFTP or Git
   # Or use: sudo wget your-files-url
   ```

6. **Import Database**
   ```bash
   mysql -u cashuser -p cash_management_system < database.sql
   ```

7. **Set Permissions**
   ```bash
   sudo chown -R www-data:www-data /var/www/html
   sudo chmod -R 755 /var/www/html
   sudo chmod -R 777 /var/www/html/uploads
   sudo chmod -R 777 /var/www/html/logs
   ```

8. **Configure Apache**
   ```bash
   sudo nano /etc/apache2/sites-available/000-default.conf
   ```
   
   Update DocumentRoot and add:
   ```apache
   <Directory /var/www/html>
       AllowOverride All
   </Directory>
   ```
   
   ```bash
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

9. **Install SSL Certificate**
   ```bash
   sudo apt install certbot python3-certbot-apache -y
   sudo certbot --apache -d yourcompany.com
   ```

10. **Update Configuration Files**
    - Edit `config/database.php` with your MySQL credentials
    - Edit `config/config.php` with your domain URL
    - Set `session.cookie_secure` to 1

---

### Option C: Free Hosting (For Testing Only)

**Warning:** Not recommended for production/real financial data!

**Providers:**
- InfinityFree
- 000webhost
- FreeHosting.com

**Limitations:**
- Limited bandwidth
- Slower performance
- Ads on free plans
- No guaranteed uptime
- Security concerns

**Steps:** Similar to Option A (Shared Hosting)

---

## 🔒 SECURITY CHECKLIST FOR PRODUCTION

Before going live, ensure:

- [ ] Changed database password to strong password
- [ ] HTTPS/SSL enabled (green padlock in browser)
- [ ] Updated `BASE_URL` to your domain
- [ ] Set `session.cookie_secure` to 1
- [ ] Set `display_errors` to 0 in config
- [ ] File permissions properly set (755 for folders, 644 for files)
- [ ] Regular backups scheduled (daily recommended)
- [ ] Strong admin password created
- [ ] Firewall configured (if VPS)
- [ ] Updated all software (PHP, MySQL, Apache)
- [ ] Tested all features in production environment
- [ ] Error logging enabled and monitored
- [ ] Database backed up before deployment

---

## 📱 Access from Mobile/Other Devices

### Local Network Access (Development)

1. Find your computer's IP address:
   ```powershell
   ipconfig
   ```
   Look for IPv4 Address (e.g., 192.168.1.100)

2. On mobile/tablet (same WiFi):
   - Open browser
   - Go to: `http://192.168.1.100/cash-management-system`
   - ✅ Access from anywhere on your network!

### Internet Access (Production)

- Simply visit your domain: `https://yourcompany.com`
- Works from anywhere with internet connection

---

## 🔄 BACKUP & MAINTENANCE

### Database Backup (Critical!)

**Automated (Recommended):**

For cPanel hosting:
1. Go to **Backup Wizard**
2. Choose **Full Backup**
3. Set automatic schedule (daily)
4. Download to local storage

For VPS:
```bash
# Create backup script
sudo nano /root/backup.sh
```

Add:
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u cashuser -p'password' cash_management_system > /backups/db_$DATE.sql
find /backups/ -name "db_*.sql" -mtime +7 -delete
```

```bash
# Make executable
sudo chmod +x /root/backup.sh

# Schedule daily backup
sudo crontab -e
# Add line:
0 2 * * * /root/backup.sh
```

**Manual Backup:**
1. phpMyAdmin → Select database → Export → Quick → Go
2. Download SQL file
3. Store securely

### File Backup

- Backup `uploads` folder regularly
- Backup configuration files
- Store in multiple locations (cloud + local)

---

## 🎯 RECOMMENDED HOSTING PACKAGES

### For Small Organizations (1-20 users)
- **Shared Hosting**: Hostinger Business Plan or Niagahoster Bisnis
- Cost: Rp 20,000 - 50,000/month
- Storage: 10GB - 50GB
- Perfect for: Community organizations, small businesses

### For Medium Organizations (20-100 users)
- **VPS Hosting**: DigitalOcean Basic or IDCloudHost VPS
- Cost: $5-10/month or Rp 100,000-200,000/month
- Resources: 1-2GB RAM, 25-50GB SSD
- Perfect for: Growing businesses, multiple departments

### For Large Organizations (100+ users)
- **Cloud Hosting**: AWS, Google Cloud, or Azure
- Cost: Variable (pay as you go)
- Resources: Scalable
- Perfect for: Corporations, enterprises

---

## ⚡ PERFORMANCE OPTIMIZATION

### For Production

1. **Enable Caching**
   - Use browser caching
   - Enable OpCache in PHP

2. **Optimize Database**
   - Add indexes to frequently queried columns
   - Regular table optimization

3. **Use CDN** (optional)
   - CloudFlare (free tier available)
   - Speeds up asset delivery

4. **Compress Assets**
   - Enable Gzip compression in Apache
   - Minify CSS/JS files

---

## 🆘 TROUBLESHOOTING

### Local Development Issues

**Apache won't start:**
- Check if port 80 is in use
- Stop Skype or other apps using port 80
- Change Apache port in XAMPP config

**MySQL won't start:**
- Check if port 3306 is in use
- Stop other MySQL instances
- Repair MySQL database in XAMPP

**Can't access localhost:**
- Check Windows Firewall
- Try `http://127.0.0.1/cash-management-system`
- Restart XAMPP services

### Production Issues

**500 Internal Server Error:**
- Check error logs
- Verify .htaccess is correct
- Check file permissions

**Database Connection Failed:**
- Verify database credentials
- Check database server is running
- Ensure user has proper privileges

**Cannot upload files:**
- Check upload folder permissions (755 or 777)
- Increase PHP upload limits in php.ini
- Check available disk space

---

## 📞 SUPPORT RESOURCES

- Documentation in project folder
- Error logs: `logs/error.log`
- PHPMyAdmin for database management
- cPanel documentation (for shared hosting)
- Community forums for VPS providers

---

**Version:** 1.0.0  
**Last Updated:** February 2026

🎉 **Congratulations! Your Cash Management System is now running!**
