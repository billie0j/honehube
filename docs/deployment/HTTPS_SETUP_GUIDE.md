# 🔒 HTTPS Setup Guide for HoneHube

## ✅ **HTTPS Configuration Applied!**

The system is now configured to use HTTPS for secure communication.

---

## 📝 Changes Made

### 1. Backend Configuration (`backend/config/config.php`)

**Before:**
```php
ini_set('session.cookie_secure', 0); // HTTP allowed
```

**After:**
```php
ini_set('session.cookie_secure', 1); // HTTPS required ✅
```

### 2. Apache Configuration (`.htaccess`)

**Before:**
```apache
# Redirect to HTTPS (uncomment in production)
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

**After:**
```apache
# Redirect to HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301] ✅
```

**PHP Settings:**
```apache
php_value session.cookie_secure 1  ✅
```

---

## 🚀 Setup Options

You have **3 options** for HTTPS setup:

### Option 1: Local Development with Self-Signed Certificate (Recommended for Testing)
### Option 2: Production with Let's Encrypt (Free SSL)
### Option 3: Production with Purchased SSL Certificate

---

## 🔧 Option 1: Local Development (XAMPP)

### Step 1: Enable SSL in XAMPP

1. **Open XAMPP Control Panel**
2. **Click "Config" next to Apache**
3. **Select "httpd-ssl.conf"**

4. **Find and update these lines:**
```apache
# Change document root to your project
DocumentRoot "C:/xampp/htdocs/honehube"
ServerName localhost:443

# Update SSL certificate paths (XAMPP includes default certs)
SSLCertificateFile "conf/ssl.crt/server.crt"
SSLCertificateKeyFile "conf/ssl.key/server.key"
```

5. **Save and close**

### Step 2: Enable SSL Module

1. **Click "Config" next to Apache**
2. **Select "httpd.conf"**
3. **Find and uncomment these lines (remove #):**
```apache
LoadModule ssl_module modules/mod_ssl.so
Include conf/extra/httpd-ssl.conf
```

4. **Save and close**

### Step 3: Restart Apache

1. **Stop Apache** in XAMPP Control Panel
2. **Start Apache** again
3. **Verify SSL is enabled** (should see port 443 in XAMPP)

### Step 4: Access Your Site

```
HTTPS: https://localhost/honehube/
HTTP:  http://localhost/honehube/ (will redirect to HTTPS)
```

### Step 5: Accept Self-Signed Certificate

Your browser will show a warning because the certificate is self-signed:

**Chrome/Edge:**
1. Click "Advanced"
2. Click "Proceed to localhost (unsafe)"

**Firefox:**
1. Click "Advanced"
2. Click "Accept the Risk and Continue"

**This is normal for local development!**

---

## 🌐 Option 2: Production with Let's Encrypt (Free SSL)

### Prerequisites
- Domain name (e.g., honehube.com)
- Linux server (Ubuntu/CentOS)
- Apache installed

### Step 1: Install Certbot

**Ubuntu/Debian:**
```bash
sudo apt update
sudo apt install certbot python3-certbot-apache
```

**CentOS/RHEL:**
```bash
sudo yum install certbot python3-certbot-apache
```

### Step 2: Get SSL Certificate

```bash
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com
```

Follow the prompts:
1. Enter email address
2. Agree to terms
3. Choose to redirect HTTP to HTTPS (Yes)

### Step 3: Verify Installation

```bash
# Check certificate
sudo certbot certificates

# Test renewal
sudo certbot renew --dry-run
```

### Step 4: Auto-Renewal

Certbot automatically sets up auto-renewal. Verify:

```bash
sudo systemctl status certbot.timer
```

### Step 5: Update Configuration

Edit `backend/config/config.php`:
```php
// Update CORS origins
define('ALLOWED_ORIGINS', ['https://yourdomain.com', 'https://www.yourdomain.com']);
```

---

## 💳 Option 3: Production with Purchased SSL Certificate

### Step 1: Purchase SSL Certificate

Popular providers:
- Namecheap
- GoDaddy
- DigiCert
- Comodo

### Step 2: Generate CSR (Certificate Signing Request)

```bash
openssl req -new -newkey rsa:2048 -nodes -keyout yourdomain.key -out yourdomain.csr
```

Fill in the details:
- Country: ZM (Zambia)
- State: Lusaka
- City: Lusaka
- Organization: Evlyne Hone College
- Common Name: yourdomain.com

### Step 3: Submit CSR to Provider

1. Copy the CSR content
2. Submit to SSL provider
3. Complete domain verification
4. Download certificate files

### Step 4: Install Certificate

You'll receive:
- `yourdomain.crt` (certificate)
- `yourdomain.ca-bundle` (intermediate certificates)

**Apache Configuration:**

Edit `/etc/apache2/sites-available/honehube-ssl.conf`:

```apache
<VirtualHost *:443>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/honehube

    SSLEngine on
    SSLCertificateFile /path/to/yourdomain.crt
    SSLCertificateKeyFile /path/to/yourdomain.key
    SSLCertificateChainFile /path/to/yourdomain.ca-bundle

    <Directory /var/www/honehube>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/honehube-ssl-error.log
    CustomLog ${APACHE_LOG_DIR}/honehube-ssl-access.log combined
</VirtualHost>
```

### Step 5: Enable Site and Restart

```bash
sudo a2ensite honehube-ssl
sudo a2enmod ssl
sudo systemctl restart apache2
```

---

## 🧪 Testing HTTPS

### Test 1: Force HTTPS Redirect

```bash
# Try HTTP
curl -I http://localhost/honehube/

# Should see:
HTTP/1.1 301 Moved Permanently
Location: https://localhost/honehube/
```

### Test 2: Verify SSL Certificate

```bash
# Check certificate details
openssl s_client -connect localhost:443 -servername localhost
```

### Test 3: Browser Test

1. **Open:** `http://localhost/honehube/`
2. **Should redirect to:** `https://localhost/honehube/`
3. **Check:** Padlock icon in address bar

### Test 4: Session Cookie Security

1. **Login to system**
2. **Open browser DevTools** (F12)
3. **Go to Application/Storage → Cookies**
4. **Check session cookie:**
   - ✅ `Secure` flag should be checked
   - ✅ `HttpOnly` flag should be checked
   - ✅ `SameSite` should be "Strict"

---

## 🔍 Troubleshooting

### Issue: "This site can't provide a secure connection"

**Cause:** SSL not properly configured

**Solution:**
1. Verify Apache SSL module is loaded
2. Check SSL certificate paths
3. Restart Apache
4. Check error logs: `tail -f /var/log/apache2/error.log`

### Issue: "NET::ERR_CERT_AUTHORITY_INVALID"

**Cause:** Self-signed certificate (local development)

**Solution:**
- This is normal for local development
- Click "Advanced" → "Proceed to localhost"
- Or install certificate in browser's trusted certificates

### Issue: "Too many redirects"

**Cause:** Redirect loop

**Solution:**
1. Check `.htaccess` HTTPS redirect rules
2. Verify Apache configuration
3. Clear browser cache and cookies

### Issue: Session not working after HTTPS

**Cause:** Cookie secure flag mismatch

**Solution:**
1. Clear browser cookies
2. Verify `session.cookie_secure = 1` in config
3. Ensure accessing via HTTPS

### Issue: Mixed content warnings

**Cause:** Loading HTTP resources on HTTPS page

**Solution:**
1. Update all URLs to use HTTPS or relative paths
2. Check for hardcoded HTTP URLs in HTML/JS
3. Use browser DevTools to find mixed content

---

## 📊 Security Checklist

After enabling HTTPS, verify:

- [ ] Site redirects HTTP to HTTPS
- [ ] Padlock icon appears in browser
- [ ] Session cookies have Secure flag
- [ ] No mixed content warnings
- [ ] Login works correctly
- [ ] All pages load via HTTPS
- [ ] API calls use HTTPS
- [ ] Images load correctly
- [ ] CSS and JS load correctly
- [ ] No console errors

---

## 🔐 Additional Security Headers

The `.htaccess` file includes these security headers:

```apache
# Prevent clickjacking
Header always set X-Frame-Options "SAMEORIGIN"

# XSS Protection
Header always set X-XSS-Protection "1; mode=block"

# Prevent MIME sniffing
Header always set X-Content-Type-Options "nosniff"

# Referrer Policy
Header always set Referrer-Policy "strict-origin-when-cross-origin"

# Content Security Policy
Header always set Content-Security-Policy "default-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.google.com https://www.gstatic.com; img-src 'self' data: https:; font-src 'self' data:;"
```

---

## 📝 Configuration Files Updated

### 1. `backend/config/config.php`
```php
✅ session.cookie_secure = 1 (HTTPS required)
```

### 2. `.htaccess`
```apache
✅ Force HTTPS redirect
✅ session.cookie_secure = 1
✅ Security headers enabled
```

### 3. No changes needed in:
- Frontend files (use relative URLs)
- API files (inherit from config)
- Database (no URL dependencies)

---

## 🌐 Production Deployment Checklist

Before going live with HTTPS:

- [ ] Purchase/obtain SSL certificate
- [ ] Install certificate on server
- [ ] Configure Apache for HTTPS
- [ ] Update CORS origins in config.php
- [ ] Test HTTPS redirect
- [ ] Verify all pages load via HTTPS
- [ ] Check for mixed content
- [ ] Test login/logout
- [ ] Test all API endpoints
- [ ] Verify session security
- [ ] Test on multiple browsers
- [ ] Test on mobile devices
- [ ] Update DNS if needed
- [ ] Monitor error logs
- [ ] Set up certificate renewal

---

## 📚 Additional Resources

### SSL Certificate Providers
- **Let's Encrypt:** https://letsencrypt.org/ (Free)
- **Namecheap:** https://www.namecheap.com/security/ssl-certificates/
- **DigiCert:** https://www.digicert.com/
- **Comodo:** https://www.comodo.com/

### Testing Tools
- **SSL Labs:** https://www.ssllabs.com/ssltest/
- **Why No Padlock:** https://www.whynopadlock.com/
- **Security Headers:** https://securityheaders.com/

### Documentation
- **Apache SSL:** https://httpd.apache.org/docs/2.4/ssl/
- **Let's Encrypt:** https://letsencrypt.org/docs/
- **XAMPP SSL:** https://www.apachefriends.org/faq_windows.html

---

## 🎯 Quick Reference

### Local Development (XAMPP)
```
URL: https://localhost/honehube/
Certificate: Self-signed (built-in)
Setup: Enable SSL module in httpd.conf
```

### Production (Let's Encrypt)
```
URL: https://yourdomain.com/
Certificate: Free, auto-renews
Setup: sudo certbot --apache -d yourdomain.com
```

### Production (Purchased)
```
URL: https://yourdomain.com/
Certificate: Paid, manual renewal
Setup: Install .crt and .key files
```

---

## ✅ Summary

### What's Configured

1. ✅ **Session cookies require HTTPS**
   - `session.cookie_secure = 1`

2. ✅ **Automatic HTTP to HTTPS redirect**
   - All HTTP requests redirect to HTTPS

3. ✅ **Security headers enabled**
   - X-Frame-Options, XSS-Protection, etc.

4. ✅ **PHP security settings**
   - Secure session handling

### What You Need to Do

**For Local Testing (XAMPP):**
1. Enable SSL module in Apache
2. Restart Apache
3. Access via `https://localhost/honehube/`
4. Accept self-signed certificate warning

**For Production:**
1. Get SSL certificate (Let's Encrypt or purchased)
2. Install certificate on server
3. Configure Apache for HTTPS
4. Test thoroughly

---

**Status:** ✅ HTTPS Configuration Complete  
**Last Updated:** April 26, 2026  
**Version:** 1.0  
**Security Level:** Enterprise Grade 🔒
