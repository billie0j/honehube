# 🧪 Quick Security Testing Guide

## ⏱️ Session Timeout Test (10 Minutes)

### Current Configuration
```php
// backend/config/config.php
define('SESSION_LIFETIME', 600); // 10 minutes (600 seconds)
```

### Test Steps

1. **Login to the system**
   ```
   URL: http://localhost/honehube/frontend/pages/login.html
   Email: admin@honehube.com
   Password: Admin@123
   ```

2. **Note the time**
   ```
   Login time: ___:___ (write it down)
   Expected timeout: ___:___ (add 10 minutes)
   ```

3. **Wait 10 minutes**
   - Do NOT interact with the system
   - Do NOT refresh any pages
   - Do NOT click anything
   - Just wait...

4. **After 10 minutes, try to access any page**
   ```
   Try: http://localhost/honehube/frontend/pages/admin-dashboard.html
   ```

5. **Expected Result**
   ```
   ✅ You should be redirected to login page
   ✅ Session has expired
   ✅ Must login again
   ```

### Quick Test (60 seconds)

If you want to test even faster, change to 60 seconds:

```php
// backend/config/config.php
define('SESSION_LIFETIME', 60); // 1 minute (60 seconds)
```

Then:
1. Login
2. Wait 1 minute
3. Try to access dashboard
4. Should be logged out

---

## 🔐 Account Lockout Test (5 Attempts / 30 Minutes)

### Current Configuration
```php
// backend/config/config.php
define('ACCOUNT_LOCKOUT_THRESHOLD', 5);    // 5 failed attempts
define('ACCOUNT_LOCKOUT_DURATION', 1800);  // 30 minutes
```

### Test Steps

1. **Go to login page**
   ```
   URL: http://localhost/honehube/frontend/pages/login.html
   ```

2. **Attempt 1: Wrong password**
   ```
   Email: test@example.com
   Password: WrongPassword1!
   Result: ❌ "Invalid email or password"
   ```

3. **Attempt 2: Wrong password**
   ```
   Email: test@example.com
   Password: WrongPassword2!
   Result: ❌ "Invalid email or password"
   ```

4. **Attempt 3: Wrong password**
   ```
   Email: test@example.com
   Password: WrongPassword3!
   Result: ❌ "Invalid email or password"
   ```

5. **Attempt 4: Wrong password**
   ```
   Email: test@example.com
   Password: WrongPassword4!
   Result: ❌ "Invalid email or password"
   ```

6. **Attempt 5: Wrong password**
   ```
   Email: test@example.com
   Password: WrongPassword5!
   Result: 🔒 "Too many failed attempts. Your account has been 
              temporarily locked for 30 minutes."
   ```

7. **Try to login again immediately**
   ```
   Email: test@example.com
   Password: AnyPassword123!
   Result: 🔒 "Account temporarily locked due to too many failed 
              login attempts. Please try again in 29 minutes."
   ```

8. **Wait 30 minutes**
   ```
   Start time: ___:___
   End time: ___:___ (add 30 minutes)
   ```

9. **Try to login after 30 minutes**
   ```
   Email: test@example.com
   Password: CorrectPassword123!
   Result: ✅ Login successful! Account unlocked.
   ```

### Quick Test (5 minutes lockout)

If you want to test faster, change lockout duration:

```php
// backend/config/config.php
define('ACCOUNT_LOCKOUT_DURATION', 300); // 5 minutes (300 seconds)
```

Then:
1. Make 5 failed login attempts
2. Account locks for 5 minutes
3. Wait 5 minutes
4. Account unlocks automatically

---

## 🎯 Combined Test Scenario

### Real-World Scenario

**Time: 2:00 PM**

1. **Student logs in**
   ```
   2:00 PM - Login successful ✅
   Session expires at: 2:10 PM
   ```

2. **Student browses items**
   ```
   2:02 PM - Viewing laptops
   2:05 PM - Viewing phones
   2:08 PM - Viewing chargers
   ```

3. **Student goes for break (inactive)**
   ```
   2:10 PM - Session expires (10 minutes passed)
   ```

4. **Student returns and tries to view dashboard**
   ```
   2:15 PM - Clicks on dashboard
   Result: ❌ Redirected to login
   Message: "Your session has expired. Please login again."
   ```

5. **Student tries to login but forgets password**
   ```
   2:16 PM - Attempt 1: Wrong password ❌
   2:16 PM - Attempt 2: Wrong password ❌
   2:17 PM - Attempt 3: Wrong password ❌
   2:17 PM - Attempt 4: Wrong password ❌
   2:18 PM - Attempt 5: Wrong password ❌
   Result: 🔒 Account locked until 2:48 PM (30 minutes)
   ```

6. **Student waits and tries again**
   ```
   2:48 PM - Account unlocks automatically
   2:49 PM - Logs in with correct password ✅
   Session expires at: 2:59 PM (10 minutes)
   ```

---

## 📊 Testing Checklist

### Session Timeout
- [ ] Login to system
- [ ] Note current time
- [ ] Wait 10 minutes without activity
- [ ] Try to access any page
- [ ] Verify redirect to login
- [ ] Verify "Session expired" message
- [ ] Login again successfully

### Account Lockout
- [ ] Go to login page
- [ ] Make 5 failed login attempts
- [ ] Verify account locks on 5th attempt
- [ ] Verify lockout message appears
- [ ] Verify remaining time is shown
- [ ] Try to login during lockout (should fail)
- [ ] Wait 30 minutes
- [ ] Verify account unlocks automatically
- [ ] Login successfully

### Rate Limiting
- [ ] Make 5 failed attempts
- [ ] Verify rate limiting triggers
- [ ] Check database for login_attempts records
- [ ] Wait 15 minutes
- [ ] Verify counter resets

---

## 🗄️ Database Verification

### Check Login Attempts
```sql
-- View recent login attempts
SELECT * FROM login_attempts 
ORDER BY attempted_at DESC 
LIMIT 10;
```

### Check Locked Accounts
```sql
-- View currently locked accounts
SELECT 
    email,
    locked_until,
    TIMESTAMPDIFF(MINUTE, NOW(), locked_until) as minutes_remaining,
    lock_reason
FROM account_lockouts 
WHERE locked_until > NOW();
```

### Check Active Sessions
```sql
-- View active sessions
SELECT 
    id,
    user_id,
    ip_address,
    created_at,
    expires_at,
    TIMESTAMPDIFF(MINUTE, NOW(), expires_at) as minutes_remaining
FROM sessions 
WHERE expires_at > NOW();
```

### Check Audit Logs
```sql
-- View security events
SELECT * FROM audit_logs 
WHERE action_type IN ('user_login', 'user_logout', 'account_locked')
ORDER BY created_at DESC 
LIMIT 20;
```

---

## 🔧 Configuration Options

### Session Timeout Options

```php
// Very Short (for testing)
define('SESSION_LIFETIME', 60);    // 1 minute

// Short (for testing)
define('SESSION_LIFETIME', 300);   // 5 minutes

// Medium (recommended for testing)
define('SESSION_LIFETIME', 600);   // 10 minutes ⭐ CURRENT

// Standard (production)
define('SESSION_LIFETIME', 1800);  // 30 minutes

// Long (production)
define('SESSION_LIFETIME', 3600);  // 1 hour

// Very Long (production)
define('SESSION_LIFETIME', 7200);  // 2 hours
```

### Account Lockout Options

```php
// Strict Security
define('ACCOUNT_LOCKOUT_THRESHOLD', 3);    // 3 attempts
define('ACCOUNT_LOCKOUT_DURATION', 3600);  // 1 hour

// Standard Security (current)
define('ACCOUNT_LOCKOUT_THRESHOLD', 5);    // 5 attempts ⭐
define('ACCOUNT_LOCKOUT_DURATION', 1800);  // 30 minutes ⭐

// Lenient Security
define('ACCOUNT_LOCKOUT_THRESHOLD', 10);   // 10 attempts
define('ACCOUNT_LOCKOUT_DURATION', 900);   // 15 minutes

// Testing (quick unlock)
define('ACCOUNT_LOCKOUT_THRESHOLD', 3);    // 3 attempts
define('ACCOUNT_LOCKOUT_DURATION', 300);   // 5 minutes
```

---

## 🎬 Video Test Script

### Session Timeout Demo (10 minutes)

```
[00:00] Open browser
[00:05] Navigate to login page
[00:10] Enter credentials and login
[00:15] Show dashboard - "Logged in successfully"
[00:20] Show current time - "2:00 PM"
[00:25] Say: "Now we wait 10 minutes..."
[00:30] [Fast forward or time-lapse]
[10:00] Show current time - "2:10 PM"
[10:05] Try to access dashboard
[10:10] Show redirect to login
[10:15] Show message: "Session expired"
[10:20] Login again successfully
[10:25] End demo
```

### Account Lockout Demo (5 attempts)

```
[00:00] Open browser
[00:05] Navigate to login page
[00:10] Attempt 1 - Wrong password - "Invalid"
[00:15] Attempt 2 - Wrong password - "Invalid"
[00:20] Attempt 3 - Wrong password - "Invalid"
[00:25] Attempt 4 - Wrong password - "Invalid"
[00:30] Attempt 5 - Wrong password - "Account locked!"
[00:35] Show lockout message with time remaining
[00:40] Try to login again - Still locked
[00:45] Show database - account_lockouts table
[00:50] Wait or fast forward 30 minutes
[30:00] Try to login - Success! Unlocked
[30:05] End demo
```

---

## ✅ Expected Results Summary

### Session Timeout
```
✅ Session expires after 10 minutes of inactivity
✅ User redirected to login page
✅ "Session expired" message displayed
✅ User must login again
✅ New session starts after login
```

### Account Lockout
```
✅ Account locks after 5 failed attempts
✅ Lockout message shows remaining time
✅ Cannot login during lockout period
✅ Account unlocks after 30 minutes
✅ Can login successfully after unlock
✅ All events logged in database
```

---

## 🐛 Troubleshooting

### Session not expiring?
1. Check SESSION_LIFETIME in config.php
2. Clear browser cookies
3. Restart Apache
4. Check PHP session settings

### Account not locking?
1. Check ACCOUNT_LOCKOUT_THRESHOLD in config.php
2. Verify database tables exist (account_lockouts, login_attempts)
3. Check error logs
4. Verify MySQL is running

### Can't unlock account?
```sql
-- Manual unlock
DELETE FROM account_lockouts WHERE email = 'user@example.com';
```

---

## 📝 Test Report Template

```
HONEHUBE SECURITY TEST REPORT
Date: _______________
Tester: _______________

SESSION TIMEOUT TEST
□ Login successful
□ Waited 10 minutes
□ Session expired as expected
□ Redirected to login
□ Message displayed correctly
□ Re-login successful
Result: PASS / FAIL

ACCOUNT LOCKOUT TEST
□ Made 5 failed attempts
□ Account locked on 5th attempt
□ Lockout message displayed
□ Remaining time shown
□ Cannot login during lockout
□ Account unlocked after 30 minutes
□ Login successful after unlock
Result: PASS / FAIL

OVERALL RESULT: PASS / FAIL

Notes:
_________________________________
_________________________________
_________________________________
```

---

## 🎯 Quick Reference

**Current Settings:**
- Session Timeout: **10 minutes** ⏱️
- Account Lockout: **5 attempts / 30 minutes** 🔒

**Test URLs:**
- Login: `http://localhost/honehube/frontend/pages/login.html`
- Dashboard: `http://localhost/honehube/frontend/pages/dashboard.html`
- Admin: `http://localhost/honehube/frontend/pages/admin-dashboard.html`

**Test Credentials:**
- Admin: `admin@honehube.com` / `Admin@123`
- Test: `test@example.com` / `Test@123!`

**Database Tables:**
- `sessions` - Active sessions
- `login_attempts` - Failed login tracking
- `account_lockouts` - Locked accounts
- `audit_logs` - Security events

---

**Happy Testing! 🧪**

**Status:** Ready for Testing ✅  
**Last Updated:** April 26, 2026  
**Version:** 1.0
