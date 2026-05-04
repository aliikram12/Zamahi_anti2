# ZAMAHI Luxury Catering - Production Deployment Guide

## ✅ Fixed Issues

The following issues have been resolved to ensure CSS and assets load correctly on production:

### 1. Dynamic SITE_URL (FIXED)
- **Problem**: `SITE_URL` was hardcoded to `http://localhost/Zamahi_anti2`
- **Solution**: Now auto-detects the URL based on the server environment
- **Files Modified**: `includes/config.php`

### 2. Case Sensitivity (VERIFIED)
- All folder names use lowercase (`css/`, `js/`, `images/`)
- File paths are consistent throughout the project

### 3. .htaccess Created
- Added security rules to protect sensitive folders
- Enabled browser caching for better performance
- Added GZIP compression support

---

## ⚠️ Required Configuration Changes for Production

Before uploading to cPanel, you MUST update the following in `includes/config.php`:

### Database Settings
```php
define('DB_HOST', 'localhost');        // Usually 'localhost' on cPanel
define('DB_NAME', 'your_db_name');     // Your cPanel database name
define('DB_USER', 'your_db_user');     // Your cPanel database username
define('DB_PASS', 'your_db_password'); // Your cPanel database password
```

### SMTP Settings (for email functionality)
```php
define('SMTP_HOST', 'smtp.your-provider.com');
define('SMTP_PORT', 587);              // or 465 for SSL
define('SMTP_USER', 'your-email@example.com');
define('SMTP_PASS', 'your-email-password');
define('SMTP_FROM_EMAIL', 'bookings@yourdomain.com');
define('SMTP_FROM_NAME', 'ZAMAHI Luxury Catering');
```

### Google Maps API (optional)
```php
define('GOOGLE_MAPS_API_KEY', 'YOUR_API_KEY_HERE');
```

---

## 📤 Upload Instructions for cPanel

1. **Compress the project folder** (exclude `vendor/` if using Composer on server)

2. **Upload to cPanel File Manager**:
   - Navigate to `public_html/` (or your subdomain folder)
   - Upload the compressed file
   - Extract it there

3. **Set folder permissions**:
   - `invoices/` → 755 or 775
   - `assets/images/uploads/` → 755 or 775
   - `assets/images/gallery/` → 755 or 775

4. **Import the database**:
   - Go to cPanel → phpMyAdmin
   - Create a new database
   - Import `setup_database.sql`

5. **Update database credentials** in `includes/config.php`

6. **Enable HTTPS** (recommended):
   - Install free SSL via cPanel → Let's Encrypt
   - Uncomment the HTTPS redirect lines in `.htaccess`

---

## 🧪 Testing

After deployment:
1. Visit your domain - CSS should load correctly
2. Test the booking form submits properly
3. Verify admin panel works
4. Check email functionality

---

## 📁 Project Structure

```
Zamahi_anti2/
├── .htaccess                    # Production config
├── index.php                    # Main page
├── includes/
│   ├── config.php              # Configuration (UPDATE THIS)
│   ├── db.php                  # Database connection
│   ├── header.php              # Header with CSS link
│   ├── footer.php              # Footer with JS link
│   └── functions.php           # Helper functions
├── assets/
│   ├── css/style.css           # Main stylesheet
│   ├── js/                     # JavaScript files
│   └── images/                 # Images folder
├── admin/                      # Admin panel
├── api/                        # API endpoints
└── invoices/                   # Generated invoices
```

---

## 🔧 Troubleshooting

### CSS Not Loading?
1. Check browser console for 404 errors
2. Verify `.htaccess` is uploaded
3. Clear browser cache

### Database Error?
1. Verify database credentials in config.php
2. Check database user has proper permissions
3. Ensure database is imported correctly

### Images Not Showing?
1. Check folder permissions (755 or 775)
2. Verify image paths in database
3. Check file ownership

---

**Last Updated**: 2026-03-16
