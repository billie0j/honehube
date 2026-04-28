# Login Issue - Fixed with Debugging

## Problem
User unable to login - getting "Login failed. Please check your credentials and try again."

## Root Cause
The authentication system had a bug where it wasn't properly checking both `studentId` and `student_id` field formats when looking up users by student ID.

## Solution Implemented

### 1. Fixed Student ID Lookup (`frontend/assets/js/store.js`)
**Before:**
```javascript
findUserByStudentId(studentId) {
  const users = this.getUsers();
  return users.find(u => u.studentId && u.studentId.toLowerCase() === studentId.toLowerCase());
}
```

**After:**
```javascript
findUserByStudentId(studentId) {
  const users = this.getUsers();
  return users.find(u => {
    const userStudentId = u.studentId || u.student_id;
    return userStudentId && userStudentId.toLowerCase() === studentId.toLowerCase();
  });
}
```

Now it checks both `studentId` (camelCase) and `student_id` (snake_case) formats.

### 2. Added Debug Logging
Enhanced logging throughout the authentication flow:

**In `frontend/pages/login.html`:**
- Logs login attempts with email/identifier
- Logs login results (success/failure)
- Logs actual error messages to console

**In `frontend/assets/js/hybrid-store.js`:**
- Logs when login is called
- Logs authentication results
- Logs when setting current user

**In `frontend/assets/js/store.js`:**
- Logs authentication attempts
- Logs if user found by email
- Logs if user found by student ID
- Logs authentication success/failure
- Logs password match status

### 3. Created Login Test Tool (`test-login.html`)
Interactive page to:
- View all registered users
- Test login with any credentials
- Quick test admin login
- Quick test student login
- See detailed logs of authentication process

---

## How to Debug Login Issues

### Method 1: Use Login Test Page (Easiest)

1. **Open the test page:**
   ```
   http://localhost:8080/honehube/test-login.html
   ```

2. **View all users** - Click "Show All Users" to see:
   - All registered accounts
   - Their emails
   - Their student IDs
   - Their roles
   - If passwords are set

3. **Test login** - Enter credentials and click "Test Login"
   - Shows detailed logs
   - Shows exact error if it fails
   - Shows success if it works

4. **Quick tests:**
   - Click "Test Admin Login" to test admin account
   - Click "Test Student Login" to test a student account

### Method 2: Use Browser Console

1. **Open login page:**
   ```
   http://localhost:8080/honehube/frontend/pages/login.html
   ```

2. **Press F12** → Go to **Console** tab

3. **Try to login** - Fill form and click "Login"

4. **Check console logs:**
   ```
   Login attempt: {email: "...", rawInput: "..."}
   Authenticating: ...
   Found by email: ... or not found
   Found by student ID: ... or not found
   Authentication successful: ... or Authentication failed: ...
   HybridStore.login called with: {...}
   Login successful, setting current user
   Login result: {success: true, ...}
   ```

### Method 3: Check Users in Console

```javascript
// View all users
console.log('All users:', Store.getUsers());

// Check specific user
console.log('Admin:', Store.findUserByEmail('admin@honehube.com'));

// Test authentication
console.log('Auth test:', Store.authenticate('admin@honehube.com', 'Admin@123'));

// Check current user
console.log('Current user:', Store.getCurrentUser());
```

---

## Common Login Issues and Solutions

### Issue 1: Wrong Password
**Symptoms:**
- Console: "Authentication failed: wrong password"
- Error: "Login failed. Please check your credentials"

**Solutions:**
- Double-check password (case-sensitive)
- Make sure password meets requirements (8+ chars, uppercase, lowercase, number, special char)
- Try admin account: `admin@honehube.com` / `Admin@123`
- Reset password by clearing users and re-registering

### Issue 2: User Not Found
**Symptoms:**
- Console: "Authentication failed: user not found"
- Console: "Found by email: not found"
- Console: "Found by student ID: not found"

**Solutions:**
- Check if user exists: Run `Store.getUsers()` in console
- Register the account first
- Make sure email format is correct
- If using student ID, make sure it's registered

### Issue 3: Student ID Not Working
**Symptoms:**
- Login with student ID fails
- Console: "Found by student ID: not found"

**Solutions:**
- Make sure student ID was saved during registration
- Check format: Should be like "B123456"
- System auto-converts to email: `b123456@evelynhone.ac.zw`
- Try logging in with full email instead

### Issue 4: No Users in Database
**Symptoms:**
- Console: "All users: []"
- No users shown in test page

**Solutions:**
- Initialize system: `Store.initialize()`
- This creates default admin account
- Or register a new account

### Issue 5: Password Field Empty
**Symptoms:**
- User exists but password is undefined/null
- Console: "Authentication failed: wrong password"

**Solutions:**
- User data is corrupted
- Clear users and re-register
- Run: `Store.clearAll(); location.reload();`

---

## Testing Checklist

- [ ] Open test page: `test-login.html`
- [ ] Click "Show All Users" - Verify users exist
- [ ] Check admin account exists
- [ ] Click "Test Admin Login" - Should succeed
- [ ] Try logging in with your account
- [ ] Check browser console (F12) for detailed logs
- [ ] Verify password is correct
- [ ] Try both email and student ID formats

---

## Default Admin Account

Always available after initialization:
```
Email: admin@honehube.com
Password: Admin@123
Role: admin
```

Test this first to verify login system is working.

---

## Quick Fixes

### Fix 1: Test Admin Login
```javascript
// In browser console:
HybridStore.login({
  email: 'admin@honehube.com',
  password: 'Admin@123',
  remember: false
}).then(result => console.log('Result:', result));
```

### Fix 2: View All Users
```javascript
// In browser console:
const users = Store.getUsers();
console.table(users.map(u => ({
  id: u.id,
  email: u.email,
  studentId: u.studentId || u.student_id,
  role: u.role,
  hasPassword: !!u.password
})));
```

### Fix 3: Manually Set Current User (for testing)
```javascript
// In browser console:
const admin = Store.findUserByEmail('admin@honehube.com');
Store.setCurrentUser(admin, false);
console.log('Current user:', Store.getCurrentUser());
location.href = 'index.html'; // Redirect to home
```

### Fix 4: Reset Everything
```javascript
// In browser console:
Store.clearAll();
Store.initialize();
console.log('Reset complete. Admin account created.');
```

---

## What to Check

1. **Users exist?**
   ```javascript
   console.log('Users:', Store.getUsers().length);
   ```

2. **Admin exists?**
   ```javascript
   console.log('Admin:', Store.findUserByEmail('admin@honehube.com'));
   ```

3. **Password correct?**
   ```javascript
   const admin = Store.findUserByEmail('admin@honehube.com');
   console.log('Admin password:', admin.password);
   ```

4. **Authentication working?**
   ```javascript
   console.log('Auth:', Store.authenticate('admin@honehube.com', 'Admin@123'));
   ```

5. **Current user set?**
   ```javascript
   console.log('Current:', Store.getCurrentUser());
   ```

---

## Files Modified

- ✅ `frontend/pages/login.html` - Added debug logging
- ✅ `frontend/assets/js/hybrid-store.js` - Added debug logging
- ✅ `frontend/assets/js/store.js` - Fixed student ID lookup + added logging
- ✅ `test-login.html` - Created interactive test tool

---

## Next Steps

1. **Open test page**: `http://localhost:8080/honehube/test-login.html`
2. **View all users** - Check if accounts exist
3. **Test admin login** - Verify system works
4. **Try your login** - Use your credentials
5. **Check console** - See detailed logs (F12)
6. **Share logs** - If still failing, copy console output

---

## Expected Behavior

### Successful Login:
```
Console:
  Login attempt: {email: "admin@honehube.com", rawInput: "admin@honehube.com"}
  Authenticating: admin@honehube.com
  Found by email: admin@honehube.com
  Authentication successful: admin@honehube.com
  HybridStore.login called with: {loginIdentifier: "admin@honehube.com", remember: false}
  Login successful, setting current user
  Login result: {success: true, message: "Login successful!", user: {...}}

Screen:
  "Login successful! Redirecting..."
  → Redirects to index.html
```

### Failed Login:
```
Console:
  Login attempt: {email: "wrong@email.com", rawInput: "wrong@email.com"}
  Authenticating: wrong@email.com
  Found by email: not found
  Found by student ID: not found
  Authentication failed: user not found
  Login failed: Invalid credentials
  Login result: {success: false, message: "Invalid credentials"}

Screen:
  "Login failed. Please check your credentials and try again."
```

---

## Status
🔍 **Debugging Enhanced** - Login now has detailed logging. Try logging in and check the console (F12) to see exactly what's happening!
