# 🔧 TROUBLESHOOTING - Local Development

## Problem: Cannot Access http://localhost/cash-management-system

### Quick Fixes

#### Solution 1: Check XAMPP Services ✅

1. **Open XAMPP Control Panel**
   ```powershell
   C:\xampp\xampp-control.exe
   ```

2. **Verify Services are Running**
   - Apache should show **green "Running"** status
   - MySQL should show **green "Running"** status

3. **If Not Running**:
   - Click **"Start"** button next to Apache
   - Click **"Start"** button next to MySQL
   - Wait for green status

#### Solution 2: Check Port Conflicts

**Check if Apache is running:**
```powershell
netstat -ano | findstr :80
```
- Should show LISTENING status
- If "Address already in use": Another program is using port 80

**Programs that often use port 80:**
- Skype
- IIS (Windows Web Server)
- Other web servers

**To fix:**
- Close Skype or other programs
- Or change Apache port in XAMPP config

**Check if MySQL is running:**
```powershell
netstat -ano | findstr :3306
```
- Should show LISTENING status

#### Solution 3: Try Alternative URLs

Try these URLs in your browser:

1. **Primary URL:**
   ```
   http://localhost/cash-management-system
   ```

2. **Alternative 1:**
   ```
   http://127.0.0.1/cash-management-system
   ```

3. **Alternative 2 (if folder is different):**
   ```
   http://localhost/cash-management-system/login.php
   ```

#### Solution 4: Check Files Location

**Verify files are in correct location:**
```powershell
# Check if folder exists
Test-Path "c:\xampp\htdocs\cash-management-system"

# List contents
Get-ChildItem "c:\xampp\htdocs\cash-management-system"
```

**Files should be at:**
```
c:\xampp\htdocs\cash-management-system\
    ├── index.php
    ├── login.php
    ├── dashboard.php
    ├── config\
    ├── assets\
    └── ... (other files)
```

**NOT at:**
```
❌ c:\xampp\htdocs\cash-management-system\cash-management-system\
❌ C:\Desktop\cash-management-system\
```

#### Solution 5: Restart Apache

If page still doesn't load:

1. **Open XAMPP Control Panel**
2. **Stop Apache** (click Stop button)
3. **Wait 5 seconds**
4. **Start Apache** (click Start button)
5. **Try accessing again**

#### Solution 6: Check Windows Firewall

**Windows Firewall might be blocking Apache:**

1. Open **Windows Defender Firewall**
2. Click **"Allow an app through firewall"**
3. Find **"Apache HTTP Server"** or **"httpd.exe"**
4. Check both **Private** and **Public** boxes
5. Click **OK**
6. Restart Apache

#### Solution 7: Check Browser Issues

**Clear browser cache:**
1. Press `Ctrl + Shift + Delete`
2. Select **"Cached images and files"**
3. Click **"Clear data"**
4. Try again

**Try different browser:**
- If using Chrome, try Firefox or Edge
- If using Firefox, try Chrome

**Disable browser extensions:**
- Ad blockers might interfere
- Try in Incognito/Private mode: `Ctrl + Shift + N`

---

## Problem: Blank White Page

### Causes & Solutions

#### 1. PHP Errors

**Enable error display:**

Edit `c:\xampp\htdocs\cash-management-system\config\config.php`

Change line 19 from:
```php
ini_set('display_errors', 0);
```

To:
```php
ini_set('display_errors', 1);
```

**Refresh page to see errors**

#### 2. Check Error Logs

**Location:**
```
c:\xampp\htdocs\cash-management-system\logs\error.log
```

**View errors:**
```powershell
Get-Content "c:\xampp\htdocs\cash-management-system\logs\error.log" -Tail 50
```

#### 3. Database Connection Issue

**Check if database exists:**
1. Open: http://localhost/phpmyadmin
2. Look for database: `cash_management_system`
3. If not exists: Create it (see below)

**Check database credentials:**

Edit: `c:\xampp\htdocs\cash-management-system\config\database.php`

Should be:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'cash_management_system');
define('DB_USER', 'root');
define('DB_PASS', ''); // Empty for XAMPP
```

---

## Problem: Database Connection Error

### Solutions

#### Step 1: Check MySQL is Running

```powershell
netstat -ano | findstr :3306
```

If not running:
1. Open XAMPP Control Panel
2. Click **Start** next to MySQL

#### Step 2: Create Database

1. **Open phpMyAdmin:** http://localhost/phpmyadmin
2. Click **"New"** on left sidebar
3. Database name: `cash_management_system`
4. Collation: `utf8mb4_unicode_ci`
5. Click **"Create"**

#### Step 3: Import Database

1. Click on **`cash_management_system`** database
2. Go to **"Import"** tab
3. Click **"Choose File"**
4. Select: `c:\xampp\htdocs\cash-management-system\database.sql`
5. Scroll down, click **"Go"**
6. Wait for success message

#### Step 4: Verify Tables Created

In phpMyAdmin:
- Select your database
- Check if tables exist: `organizations`, `users`, `transactions`, etc.
- Should have 10+ tables

---

## Problem: CSS/JavaScript Not Loading (Page Looks Broken)

### Solutions

#### 1. Check BASE_URL Configuration

Edit: `c:\xampp\htdocs\cash-management-system\config\config.php`

Line 26 should be:
```php
define('BASE_URL', 'http://localhost/cash-management-system');
```

**Important:** No trailing slash!

#### 2. Check Assets Folder Exists

```powershell
Test-Path "c:\xampp\htdocs\cash-management-system\assets"
Test-Path "c:\xampp\htdocs\cash-management-system\assets\css"
Test-Path "c:\xampp\htdocs\cash-management-system\assets\js"
```

All should return `True`

#### 3. Clear Browser Cache

1. Press `Ctrl + Shift + Delete`
2. Clear cached files
3. Refresh page: `Ctrl + F5`

#### 4. Check Browser Console

1. Press `F12` to open Developer Tools
2. Go to **Console** tab
3. Look for 404 errors (file not found)
4. Check **Network** tab for failed requests

---

## Problem: "403 Forbidden" Error

### Solution

**Check .htaccess file:**

Location: `c:\xampp\htdocs\cash-management-system\.htaccess`

Should contain:
```apache
RewriteEngine On
RewriteBase /cash-management-system/

# Allow access
<FilesMatch "\.(php|html|css|js|png|jpg|jpeg|gif|ico|woff|woff2|ttf)$">
    Order Allow,Deny
    Allow from all
</FilesMatch>
```

**Or temporarily rename .htaccess:**
```powershell
Rename-Item "c:\xampp\htdocs\cash-management-system\.htaccess" ".htaccess.backup"
```

Try accessing again.

---

## Problem: Session Errors

### Solutions

#### 1. Check Session Directory

```powershell
# Create temp directory if doesn't exist
New-Item -ItemType Directory -Force -Path "c:\xampp\tmp"
```

#### 2. Clear Browser Cookies

1. Press `Ctrl + Shift + Delete`
2. Select **"Cookies and other site data"**
3. Click **"Clear data"**

#### 3. Restart Apache

Stop and start Apache in XAMPP Control Panel

---

## Quick Commands Reference

### Open Application
```powershell
# Method 1
Start-Process "http://localhost/cash-management-system"

# Method 2
explorer "http://localhost/cash-management-system"
```

### Open phpMyAdmin
```powershell
explorer "http://localhost/phpmyadmin"
```

### Check Services
```powershell
# Check Apache (port 80)
netstat -ano | findstr :80

# Check MySQL (port 3306)
netstat -ano | findstr :3306
```

### View Error Logs
```powershell
# View last 50 lines
Get-Content "c:\xampp\htdocs\cash-management-system\logs\error.log" -Tail 50

# View all errors
notepad "c:\xampp\htdocs\cash-management-system\logs\error.log"
```

### Check PHP Version
```powershell
# Via command line
c:\xampp\php\php.exe -v

# Via web
explorer "http://localhost/dashboard/"
```

---

## Still Having Issues?

### Diagnostic Script

Run this to get full diagnostic information:

```powershell
Write-Host "=== XAMPP Diagnostic ===" -ForegroundColor Cyan

# Check if XAMPP is installed
Write-Host "`nXAMPP Location:"
Test-Path "c:\xampp"

# Check if files exist
Write-Host "`nCash Management System Files:"
Test-Path "c:\xampp\htdocs\cash-management-system"

# Check Apache
Write-Host "`nApache Status (port 80):"
netstat -ano | findstr :80

# Check MySQL
Write-Host "`nMySQL Status (port 3306):"
netstat -ano | findstr :3306

# Check PHP version
Write-Host "`nPHP Version:"
c:\xampp\php\php.exe -v

# List files
Write-Host "`nFiles in project:"
Get-ChildItem "c:\xampp\htdocs\cash-management-system" | Select-Object Name

Write-Host "`n=== End Diagnostic ===" -ForegroundColor Cyan
```

### Manual Browser Access

1. **Open any browser manually**
2. **Type in address bar:**
   ```
   http://localhost/cash-management-system
   ```
3. **Press Enter**

### Test Simple PHP

Create test file to verify PHP is working:

```powershell
# Create test.php
@"
<?php
phpinfo();
?>
"@ | Out-File "c:\xampp\htdocs\test.php" -Encoding utf8
```

Then open: http://localhost/test.php

If this works, PHP is running correctly!

---

## Emergency: Complete Reset

If nothing works, try complete reset:

### 1. Stop All Services
```powershell
# Open XAMPP Control Panel
c:\xampp\xampp-control.exe
```
Stop Apache and MySQL

### 2. Restart XAMPP Services
1. Start MySQL first
2. Wait for green status
3. Start Apache
4. Wait for green status

### 3. Test Simple URL
Open: http://localhost/

You should see XAMPP dashboard.

### 4. Try Application Again
Open: http://localhost/cash-management-system

---

## Contact & Resources

### Error Log Location
```
c:\xampp\htdocs\cash-management-system\logs\error.log
```

### Configuration Files
```
c:\xampp\htdocs\cash-management-system\config\config.php
c:\xampp\htdocs\cash-management-system\config\database.php
```

### XAMPP Documentation
- XAMPP FAQ: https://www.apachefriends.org/faq_windows.html
- Apache Docs: https://httpd.apache.org/docs/

---

## Summary Checklist

Before asking for help, verify:

- [ ] XAMPP Control Panel shows Apache as **green/running**
- [ ] XAMPP Control Panel shows MySQL as **green/running**
- [ ] Files are at: `c:\xampp\htdocs\cash-management-system\`
- [ ] Database `cash_management_system` exists in phpMyAdmin
- [ ] Database tables are imported (from database.sql)
- [ ] Config files have correct BASE_URL and database credentials
- [ ] Tried both `localhost` and `127.0.0.1`
- [ ] Cleared browser cache
- [ ] Checked error.log for PHP errors
- [ ] Windows Firewall allows Apache

---

**If you've checked everything and it still doesn't work, provide:**
1. Screenshot of XAMPP Control Panel
2. Contents of error.log
3. Exact error message you see
4. Browser console errors (F12 → Console)

This will help diagnose the specific issue!
