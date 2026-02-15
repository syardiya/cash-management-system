# Cash Management System - Project Structure

```
cash-management-system/
│
├── 📁 api/                          # API endpoints
│   ├── accounts.php                 # Account management API
│   ├── anomalies.php                # Anomaly alerts API
│   ├── login.php                    # Login endpoint
│   ├── register.php                 # Registration endpoint
│   └── transactions.php             # Transaction management API
│
├── 📁 assets/                       # Frontend assets
│   ├── 📁 css/
│   │   └── style.css                # Main stylesheet
│   └── 📁 js/
│       └── main.js                  # Main JavaScript file
│
├── 📁 classes/                      # PHP Classes (Backend Logic)
│   ├── AccountSource.php            # Account management class
│   ├── Organization.php             # Organization management
│   ├── Transaction.php              # Transaction + AI detection
│   └── User.php                     # User authentication & authorization
│
├── 📁 config/                       # Configuration files
│   ├── config.php                   # Application config & helpers
│   └── database.php                 # Database connection (PDO)
│
├── 📁 logs/                         # Error logs
│   └── index.php                    # Security placeholder
│
├── 📁 uploads/                      # File uploads
│   └── index.php                    # Security placeholder
│
├── 📄 .htaccess                     # Apache security configuration
├── 📄 accounts.php                  # Accounts management page
├── 📄 anomalies.php                 # AI alerts page
├── 📄 dashboard.php                 # Main dashboard
├── 📄 database.sql                  # Database schema
├── 📄 index.php                     # Entry point (redirects)
├── 📄 INSTALLATION.md               # Installation guide
├── 📄 login.php                     # Login page
├── 📄 logout.php                    # Logout handler
├── 📄 PROJECT_STRUCTURE.md          # This file
├── 📄 README.md                     # Main documentation
└── 📄 register.php                  # Registration page
```

## 📋 File Descriptions

### API Endpoints (`api/`)
- **accounts.php**: REST API for account CRUD operations
- **anomalies.php**: API for retrieving and resolving anomalies
- **login.php**: Handles user authentication
- **register.php**: Handles organization and user registration
- **transactions.php**: REST API for transaction management

### Frontend Pages
- **index.php**: Entry point, redirects based on auth status
- **login.php**: User login interface
- **register.php**: Organization and user registration
- **dashboard.php**: Main dashboard with stats and quick actions
- **accounts.php**: Account management interface
- **anomalies.php**: AI anomaly alerts with resolution

### Backend Classes (`classes/`)
- **User.php**: Authentication, session management, security
- **Organization.php**: Multi-tenant organization management
- **AccountSource.php**: Manage different cash sources
- **Transaction.php**: Transaction handling + AI anomaly detection

### Configuration (`config/`)
- **config.php**: App constants, session config, helper functions
- **database.php**: PDO database connection singleton

### Assets (`assets/`)
- **css/style.css**: Custom CSS framework with dark theme
- **js/main.js**: JavaScript utilities, AJAX, modals, validation

### Database
- **database.sql**: Complete database schema with tables and indexes

### Documentation
- **README.md**: Main project documentation
- **INSTALLATION.md**: Step-by-step installation guide
- **PROJECT_STRUCTURE.md**: This file

## 🔐 Security Features

### Authentication & Authorization
- Password hashing (Argon2id/Bcrypt)
- Brute force protection
- Session management with expiration
- Role-based access control (Admin, Manager, User)

### Data Protection
- Prepared statements (SQL injection prevention)
- CSRF token protection
- XSS prevention (input sanitization)
- Secure session cookies (HTTPOnly, SameSite)

### Audit Trail
- Activity logging
- IP address tracking
- User action history

## 🤖 AI Anomaly Detection

Located in: `classes/Transaction.php` → `detectAnomalies()`

### Detection Algorithms
1. **High Expense Detection**
   - Statistical analysis (mean + 2σ)
   - Threshold: 2x average expense
   
2. **Rapid Transaction Detection**
   - 5+ transactions in 5 minutes
   - Account-specific monitoring

3. **Negative Balance Detection**
   - Real-time balance checking
   - Immediate alerts

4. **Duplicate Detection**
   - Same amount, same account
   - Within 1-hour window

## 📊 Database Schema

### Core Tables
- **organizations**: Multi-tenant organization data
- **users**: User accounts with authentication
- **account_sources**: Cash sources (bank, e-wallet, etc.)
- **transactions**: All financial transactions
- **categories**: Transaction categories
- **anomaly_logs**: AI detection results
- **activity_logs**: Security audit trail
- **sessions**: Session management

### Relationships
- Organizations → Users (1:N)
- Organizations → Accounts (1:N)
- Organizations → Transactions (1:N)
- Accounts → Transactions (1:N)
- Users → Transactions (1:N, created_by)
- Transactions → Anomalies (1:1, optional)

## 🎨 Design System

### Color Palette
- Primary: #4f46e5 (Indigo)
- Success: #10b981 (Green)
- Danger: #ef4444 (Red)
- Warning: #f59e0b (Amber)
- Dark BG: #0f172a
- Dark Card: #1e293b

### Typography
- Font: Inter (Google Fonts)
- Headings: 700 weight
- Body: 400 weight
- UI: 500-600 weight

### Effects
- Glassmorphism cards
- Smooth transitions (0.3s)
- Hover elevations
- Gradient accents

## 🔄 Data Flow

### Registration Flow
```
register.php → api/register.php → Organization::create() + User::register()
```

### Login Flow
```
login.php → api/login.php → User::login() → Session creation
```

### Transaction Flow
```
dashboard.php → api/transactions.php → Transaction::create() 
→ Update balance → detectAnomalies() → Create anomaly log
```

## 🚀 Future Extensions

To add new features, follow these patterns:

### New Page
1. Create PHP file in root
2. Include sidebar navigation
3. Require authentication
4. Follow existing UI patterns

### New API Endpoint
1. Create file in `api/`
2. Add CSRF protection
3. Verify authentication
4. Return JSON responses

### New Feature Class
1. Create in `classes/`
2. Use Database singleton
3. Implement security best practices
4. Add error logging

## 📝 Code Standards

### PHP
- Use prepared statements
- Sanitize all inputs
- Log errors, don't display
- Follow PSR standards

### JavaScript
- Use async/await
- Handle errors gracefully
- Validate on client and server
- Keep functions small

### CSS
- Use CSS custom properties
- Mobile-first responsive
- Consistent naming
- Reusable classes

## 🛠 Development Tips

### Debugging
1. Check `logs/error.log`
2. Enable `display_errors` in config
3. Use browser console (F12)
4. Check network tab for API calls

### Testing
1. Test all CRUD operations
2. Verify anomaly detection
3. Test authentication flows
4. Check responsive design

### Performance
1. Use database indexes
2. Limit query results
3. Optimize images
4. Minimize HTTP requests

---

**Built with ❤️ for better financial management**

Version 1.0.0 | Last Updated: February 2026
