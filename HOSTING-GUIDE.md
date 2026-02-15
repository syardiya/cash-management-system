# 🌐 HOSTING GUIDE - Cash Management System

## Quick Links
- **Local URL**: http://localhost/cash-management-system
- **phpMyAdmin**: http://localhost/phpmyadmin
- **Database Name**: cash_management_system

---

## 📍 LOCAL DEVELOPMENT (Already Setup!)

### Your Current Setup ✅
- ✅ XAMPP is running (Apache + MySQL)
- ✅ Files location: `c:\xampp\htdocs\cash-management-system`
- ✅ Application URL: http://localhost/cash-management-system
- ✅ Database: Ready to be created in phpMyAdmin

### Next Steps to Use Locally

1. **Create Database** (if not done):
   - Open: http://localhost/phpmyadmin
   - Click "New" → Database name: `cash_management_system`
   - Collation: `utf8mb4_unicode_ci` → Create
   - Click database → Import → Choose `database.sql` → Go

2. **Open Application**:
   - Go to: http://localhost/cash-management-system
   - Register your organization
   - Login and start using!

3. **Access from Phone/Tablet** (same WiFi):
   - Run in PowerShell: `ipconfig`
   - Find IPv4 (e.g., 192.168.1.100)
   - On phone: `http://192.168.1.100/cash-management-system`

---

## 🌍 PRODUCTION HOSTING OPTIONS

### Option 1: Shared Hosting (EASIEST - Recommended) ⭐

**Best for**: Small to medium organizations, easy management

**Top Indonesian Providers**:

| Provider | Starting Price | Features |
|----------|---------------|----------|
| **Niagahoster** | Rp 15,000/month | cPanel, Free SSL, 24/7 Support |
| **Hostinger** | Rp 20,000/month | Fast, Free Domain (yearly), Easy setup |
| **Rumahweb** | Rp 25,000/month | Local support, Free SSL |
| **Qwords** | Rp 18,000/month | Indonesian servers, Good support |

**International Providers**:
- Hostgator: ~$2.75/month
- Bluehost: ~$2.95/month
- SiteGround: ~$3.99/month

**Setup Steps**:

1. **Purchase Hosting**
   - Choose "Shared Hosting" or "Business Hosting"
   - Register domain (e.g., `yourcompany.com`)
   - Get cPanel login credentials

2. **Upload Files**
   
   Method A - File Manager:
   - Login to cPanel
   - Open "File Manager"
   - Go to `public_html` folder
   - Upload all files (can zip first for faster upload)
   - Extract if zipped
   
   Method B - FTP (FileZilla):
   - Download FileZilla
   - Connect with FTP credentials from hosting
   - Upload all files to `public_html`

3. **Create Database**
   - In cPanel → MySQL Databases
   - Create database: `youruser_cashsystem`
   - Create user with strong password
   - Add user to database (ALL PRIVILEGES)
   - **Important**: Note database name, username, password

4. **Import Database**
   - In cPanel → phpMyAdmin
   - Select your database
   - Import tab → Choose `database.sql` → Go

5. **Setup Otomatis (Auto-Installer)** 🚀
   - Buka website Anda (misal: `http://yoursite.infinityfreeapp.com`)
   - Anda akan diarahkan ke halaman **System Setup**.
   - Masukkan detail database dari Step 3:
     - **Database Host**: (biasanya `sqlXXX.infinityfree.com`)
     - **Database Name**: (misal `if0_35..._cash_system`)
     - **Database User**: (misal `if0_35...`)
     - **Database Password**: (Password akun hosting Anda)
   - Klik **Connect & Save**.
   - Selesai! 🎉

6. **Mulai Penggunaan**
   - **Register**: Buat akun organisasi baru sebagai Admin.
   - **Simpan Kode Akses**: Salin "Member Access Code" di dashboard.
   - **Invite Member**: Berikan link website + Kode Akses ke anggota tim.
   - **Member Login**: Anggota login lewat tab "Member Access" menggunakan Kode Akses tersebut.

**Cost Estimate**:
- Hosting: Rp 15,000-50,000/month
- Domain: Rp 150,000/year (often free first year)
- SSL: FREE (Let's Encrypt)
- **Total**: ~Rp 20,000-60,000/month

---

### Option 2: VPS Hosting (More Control)

**Best for**: Growing organizations, need customization

**Providers**:

| Provider | Price | Specs |
|----------|-------|-------|
| **DigitalOcean** | $5/month | 1GB RAM, 25GB SSD |
| **Vultr** | $2.50/month | 512MB RAM, 10GB SSD |
| **IDCloudHost** | Rp 50,000/month | 1GB RAM, 20GB SSD |
| **Contabo** | €4.99/month | 4GB RAM, 300GB |
| **AWS Lightsail** | $3.50/month | 512MB RAM, 20GB SSD |

**Quick Setup** (Ubuntu 22.04):

```bash
# 1. Connect to VPS
ssh root@your-server-ip

# 2. Update system
apt update && apt upgrade -y

# 3. Install LAMP
apt install apache2 mysql-server php php-mysql php-mbstring php-json -y

# 4. Secure MySQL
mysql_secure_installation

# 5. Create database
mysql -u root -p
CREATE DATABASE cash_management_system;
CREATE USER 'cashuser'@'localhost' IDENTIFIED BY 'StrongPass123!';
GRANT ALL PRIVILEGES ON cash_management_system.* TO 'cashuser'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# 6. Upload files to /var/www/html
# Use SFTP or Git

# 7. Import database
mysql -u cashuser -p cash_management_system < database.sql

# 8. Set permissions
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html
chmod -R 777 /var/www/html/uploads
chmod -R 777 /var/www/html/logs

# 9. Install SSL (free)
apt install certbot python3-certbot-apache -y
certbot --apache -d yourcompany.com

# 10. Enable mod_rewrite
a2enmod rewrite
systemctl restart apache2
```

**Pros**:
- Full control over server
- Better performance
- Can install custom software
- Root access

**Cons**:
- Requires technical knowledge
- You manage security updates
- More maintenance needed

**Cost**: $3-10/month or Rp 50,000-200,000/month

---

### Option 3: Cloud Hosting (Enterprise)

**Best for**: Large organizations, high traffic

**Options**:
- **AWS (Amazon)**: Elastic, scalable, worldwide
- **Google Cloud**: Fast, reliable, AI integration
- **Microsoft Azure**: Enterprise features, Windows integration
- **Alibaba Cloud**: Good for Asia region

**Cost**: Variable, starts $10-50/month, can scale to thousands

**Note**: Requires DevOps knowledge or hire cloud specialist

---

### Option 4: Free Hosting (Testing ONLY)

⚠️ **WARNING**: NOT for production or real financial data!

**Providers**:
- InfinityFree: Free, with ads
- 000webhost: 300MB storage, 3GB bandwidth
- FreeHosting.com: Basic features

**Limitations**:
- ❌ Limited bandwidth/storage
- ❌ Slow performance
- ❌ Ads displayed
- ❌ No guaranteed uptime
- ❌ Security concerns
- ❌ Can be terminated anytime

**Only use for**: Demo, testing, learning purposes

---

## 🔒 SECURITY CHECKLIST (Production)

Before going live, complete:

### Critical Security Steps

- [ ] **Change Database Password**
  ```php
  // Use strong password: Uppercase, lowercase, numbers, symbols
  define('DB_PASS', 'MyStr0ng!P@ssw0rd#2024');
  ```

- [ ] **Enable HTTPS/SSL**
  - Free via Let's Encrypt
  - Green padlock must show in browser
  - Update `session.cookie_secure` to 1

- [ ] **Update BASE_URL**
  ```php
  define('BASE_URL', 'https://yourcompany.com');
  ```

- [ ] **Disable Error Display**
  ```php
  ini_set('display_errors', 0); // Already set
  ```

- [ ] **Set File Permissions**
  - Folders: 755
  - Files: 644
  - uploads/: 777
  - logs/: 777

- [ ] **Create Strong Admin Password**
  - Minimum 8 characters
  - Uppercase + lowercase + numbers + symbols
  - Example: `Admin@2024!Secure`

- [ ] **Setup Regular Backups**
  - Database: Daily
  - Files: Weekly
  - Test restore procedure

- [ ] **Secure .htaccess File**
  - Prevent directory listing
  - Block sensitive files

- [ ] **Monitor Error Logs**
  - Check `logs/error.log` regularly
  - Set up alerts for critical errors

- [ ] **Update Software**
  - Keep PHP updated (7.4+)
  - Update MySQL regularly
  - Apply security patches

### Additional Security (Recommended)

- [ ] Use different database prefix
- [ ] Enable firewall (VPS only)
- [ ] Install security plugins/scanners
- [ ] Use strong session encryption
- [ ] Implement IP whitelisting (admin area)
- [ ] Regular security audits
- [ ] Keep offline backups

---

## 💾 BACKUP STRATEGIES

### Automatic Backups (Recommended)

**For cPanel Hosting**:
1. cPanel → Backup Wizard
2. Select "Full Backup"
3. Schedule: Daily
4. Destination: Download to computer + email

**For VPS** (cron job):
```bash
# Create backup script
nano /root/backup-cash-system.sh
```

Add:
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups"
DB_USER="cashuser"
DB_PASS="your_password"
DB_NAME="cash_management_system"

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_$DATE.sql

# Backup files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/html

# Delete backups older than 7 days
find $BACKUP_DIR -name "db_*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "files_*.tar.gz" -mtime +7 -delete
```

```bash
# Make executable
chmod +x /root/backup-cash-system.sh

# Schedule daily at 2 AM
crontab -e
# Add: 0 2 * * * /root/backup-cash-system.sh
```

### Manual Backup

**Database**:
1. phpMyAdmin → Select database
2. Export → Quick → SQL format
3. Go → Download
4. Store securely

**Files**:
1. Download entire `cash-management-system` folder
2. Include `uploads` folder
3. Store on external drive + cloud

### Backup Frequency
- Database: **Daily** (critical financial data!)
- Files: Weekly or after major changes
- Config files: After each modification

### Backup Storage
- Local computer: Primary
- Cloud storage: Secondary (Google Drive, Dropbox)
- External drive: Tertiary
- **Rule**: Always 3 copies in different locations!

---

## 📊 HOSTING COMPARISON TABLE

| Feature | Shared Hosting | VPS | Cloud | Free |
|---------|---------------|-----|-------|------|
| **Price/month** | Rp 15,000-50,000 | Rp 50,000-200,000 | Rp 200,000+ | Rp 0 |
| **Setup Difficulty** | ⭐ Easy | ⭐⭐⭐ Hard | ⭐⭐⭐⭐ Very Hard | ⭐ Easy |
| **Performance** | ⭐⭐⭐ Good | ⭐⭐⭐⭐ Excellent | ⭐⭐⭐⭐⭐ Best | ⭐ Poor |
| **Control** | Limited | Full | Full | Very Limited |
| **Security** | Good | Excellent | Excellent | Poor |
| **Support** | 24/7 | Self/Paid | Enterprise | Limited |
| **Scalability** | Limited | Good | Unlimited | None |
| **Best For** | Small-Medium Org | Growing Business | Enterprise | Testing Only |

**Recommendation**: Start with **Shared Hosting** (Niagahoster/Hostinger), upgrade to VPS if you grow to 50+ users.

---

## 🚀 PERFORMANCE OPTIMIZATION

### For Production Sites

#### 1. Enable PHP OpCache
In `php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
```

#### 2. Database Optimization
```sql
-- Add indexes for better performance
CREATE INDEX idx_org_id ON transactions(organization_id);
CREATE INDEX idx_account_id ON transactions(account_source_id);
CREATE INDEX idx_date ON transactions(transaction_date);

-- Optimize tables monthly
OPTIMIZE TABLE transactions;
OPTIMIZE TABLE account_sources;
OPTIMIZE TABLE anomaly_logs;
```

#### 3. Enable Gzip Compression
Add to `.htaccess`:
```apache
<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>
```

#### 4. Browser Caching
Add to `.htaccess`:
```apache
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresByType image/jpg "access plus 1 year"
  ExpiresByType image/jpeg "access plus 1 year"
  ExpiresByType image/gif "access plus 1 year"
  ExpiresByType image/png "access plus 1 year"
  ExpiresByType text/css "access plus 1 month"
  ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

#### 5. Use CDN (Optional)
- Sign up for CloudFlare (free)
- Point your domain to CloudFlare
- Enable caching and optimization
- Speeds up asset delivery worldwide

---

## 🎯 RECOMMENDED MIGRATION PATH

### Phase 1: Development (Now)
- Use: Local XAMPP
- Cost: Free
- Users: Just you
- **Current Status**: ✅ You are here

### Phase 2: Testing
- Use: Free hosting or shared hosting
- Cost: Rp 0-20,000/month
- Users: 1-5 test users
- Duration: 1-2 weeks

### Phase 3: Production (Small)
- Use: Shared hosting (Niagahoster/Hostinger)
- Cost: Rp 20,000-50,000/month
- Users: 1-50 users
- **Recommended**: This is perfect for most organizations

### Phase 4: Production (Growing)
- Use: VPS (DigitalOcean/IDCloudHost)
- Cost: Rp 100,000-200,000/month  
- Users: 50-500 users
- Upgrade when: Site gets slow or 100k+ monthly visitors

### Phase 5: Enterprise
- Use: Cloud hosting (AWS/Google Cloud)
- Cost: Variable, starts Rp 500,000/month
- Users: 500+ users
- Upgrade when: Need high availability, multiple regions

---

## 📞 SUPPORT & RESOURCES

### Hosting Support
- **Niagahoster**: 0804-1-808-888 (24/7), Live chat
- **Hostinger**: Live chat 24/7, Email support
- **Rumahweb**: (021) 2963-1888, Email support

### Documentation
- [cPanel Guide](https://docs.cpanel.net/)
- [FileZilla Tutorial](https://filezilla-project.org/documentation.php)
- [Let's Encrypt](https://letsencrypt.org/)

### Common Questions

**Q: Can I change hosting later?**
A: Yes! Just backup and restore to new hosting.

**Q: Do I need a domain?**
A: Recommended for professional use. Can use subdomain initially.

**Q: How much bandwidth needed?**
A: Small org (20 users): ~5GB/month. Included in most plans.

**Q: Can I host on my own computer?**
A: Possible but not recommended (security, uptime, speed issues).

---

## ✅ GO-LIVE CHECKLIST

Final steps before launching:

### Technical
- [ ] Database imported successfully
- [ ] All config files updated
- [ ] SSL certificate installed (HTTPS working)
- [ ] Test all features: login, transactions, accounts
- [ ] Error logs are empty
- [ ] Backup system configured
- [ ] Email notifications working (if configured)

### Security
- [ ] Strong passwords set
- [ ] Firewall enabled (VPS)
- [ ] File permissions correct
- [ ] Error display disabled
- [ ] Security headers configured

### Content
- [ ] Test organization registered
- [ ] Sample transactions added
- [ ] All features tested
- [ ] Mobile responsiveness checked

### Documentation
- [ ] Admin credentials documented (stored securely)
- [ ] Database credentials saved
- [ ] Hosting details noted
- [ ] Backup procedure documented

### Training
- [ ] Admin trained on system
- [ ] Users know how to register
- [ ] Support process established

---

## 🎉 SUCCESS!

Once everything is checked:

1. **Announce to users**
   - Share URL: https://yourcompany.com
   - Send registration instructions
   - Provide admin contact

2. **Monitor for 1 week**
   - Check error logs daily
   - Watch for user issues
   - Monitor performance

3. **Collect feedback**
   - What works well?
   - What needs improvement?
   - Any bugs found?

4. **Regular maintenance**
   - Weekly: Check backups, review logs
   - Monthly: Update software, optimize database
   - Quarterly: Security audit, performance review

---

**Congratulations! Your Cash Management System is now accessible to the world! 🌍**

For questions or issues, check the error logs first: `logs/error.log`

**Version**: 1.0.0  
**Last Updated**: February 2026
