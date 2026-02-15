# ⚡ QUICK START - Cash Management System
# This file provides quick commands to run your application

## 🎯 QUICK COMMANDS

### Open Application
Start-Process "http://localhost/cash-management-system"

### Open phpMyAdmin (to manage database)
Start-Process "http://localhost/phpmyadmin"

### Check if Apache is running (port 80)
netstat -ano | findstr :80

### Check if MySQL is running (port 3306)
netstat -ano | findstr :3306

### Find your IP address (for mobile access)
ipconfig

### Open XAMPP Control Panel
Start-Process "C:\xampp\xampp-control.exe"

---

## 📋 SETUP STEPS (First Time)

1. **Start XAMPP Services**
   - Open XAMPP Control Panel
   - Start Apache
   - Start MySQL

2. **Create Database**
   - Open: http://localhost/phpmyadmin
   - Click "New"
   - Database name: cash_management_system
   - Collation: utf8mb4_unicode_ci
   - Click "Create"
   - Select database → Import → Choose database.sql → Go

3. **Open Application**
   - Go to: http://localhost/cash-management-system
   - Click "Register" to create your organization
   - Fill in all details
   - Login and start using!

---

## 🌐 ACCESS FROM OTHER DEVICES

### From Phone/Tablet (Same WiFi)
1. Run: ipconfig
2. Find IPv4 Address (e.g., 192.168.1.100)
3. On phone browser: http://192.168.1.100/cash-management-system

---

## 🔧 TROUBLESHOOTING

### Apache won't start
- Check if port 80 is in use: netstat -ano | findstr :80
- Stop Skype or other apps using port 80
- Change port in XAMPP config

### MySQL won't start
- Check if port 3306 is in use: netstat -ano | findstr :3306
- Stop other MySQL instances

### Can't access localhost
- Try: http://127.0.0.1/cash-management-system
- Check Windows Firewall
- Restart XAMPP services

### Database connection error
- Verify MySQL is running
- Check database name is: cash_management_system
- Verify credentials in config/database.php

---

## 📁 IMPORTANT FILES

- Main config: config/config.php
- Database config: config/database.php
- Database file: database.sql
- Error logs: logs/error.log
- Uploads folder: uploads/

---

## 📚 DOCUMENTATION

- Full Guide: HOSTING-GUIDE.md
- Workflow: .agent/workflows/run-and-deploy.md
- Quick Start: QUICKSTART.md
- Installation: INSTALLATION.md
- README: README.md

---

## 🎉 NEXT STEPS

After everything works:
1. Register your organization
2. Create admin account
3. Add bank accounts
4. Start recording transactions
5. Monitor AI alerts

---

## 🌍 HOSTING (Production)

For production deployment, see: HOSTING-GUIDE.md

**Recommended Hosting**:
- Shared Hosting: Niagahoster/Hostinger (Rp 15,000-50,000/month)
- VPS: DigitalOcean ($5/month) or IDCloudHost (Rp 50,000/month)

**Complete hosting guide with step-by-step instructions available in HOSTING-GUIDE.md**

---

## 💡 TIPS

- Always backup database before making changes
- Use strong passwords (min 8 chars, uppercase, lowercase, number, symbol)
- Check AI alerts regularly for unusual transactions
- Keep XAMPP updated for security

---

**Your application is ready to use! Open http://localhost/cash-management-system to get started!** 🚀
