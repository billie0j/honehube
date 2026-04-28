# Clear Database Instructions

## Method 1: Use Clear Database Page (Easiest)

1. **Open this URL:**
   ```
   http://localhost:8080/honehube/clear-database.html
   ```

2. **Choose an option:**
   - **Clear All Data** - Removes everything (users, accessories, requests, complaints)
   - **Clear Users Only** - Removes all users, recreates admin account
   - **Logout Only** - Just logs out current user, keeps all data

3. **Confirm** - Click OK on the confirmation dialog

4. **Done!** - The page will show what was cleared

---

## Method 2: Use Browser Console (Quick)

### Option A: Clear Everything
1. Press **F12** to open Developer Tools
2. Go to **Console** tab
3. Paste this code and press Enter:
```javascript
Store.clearAll();
sessionStorage.clear();
console.log('✅ All data cleared!');
location.reload();
```

### Option B: Clear Users Only (Keep Accessories)
```javascript
localStorage.removeItem('honehub_users');
localStorage.removeItem('honehub_initialized');
sessionStorage.removeItem('honehub_current_user');
localStorage.removeItem('honehub_remember');
Store.initialize();
console.log('✅ Users cleared! Admin recreated.');
console.log('Admin: admin@honehube.com / Admin@123');
```

### Option C: Logout Only
```javascript
Store.logout();
console.log('✅ Logged out!');
location.reload();
```

### Option D: Clear Specific User
```javascript
// First, see all users
console.log('Current users:', Store.getUsers());

// Then delete by ID (replace X with actual user ID)
Store.deleteUser(X);
console.log('✅ User deleted!');
```

---

## Method 3: Manual localStorage Clear

1. Press **F12** → Go to **Application** tab (Chrome) or **Storage** tab (Firefox)
2. Click **Local Storage** → your site URL
3. **Right-click** on any key → **Clear**
4. **Refresh** the page

---

## What Gets Cleared

### Clear All Data:
- ✅ All users (including admin)
- ✅ All accessories/products
- ✅ All purchase requests
- ✅ All complaints
- ✅ Current login session
- ✅ Remember me data
- ✅ Initialization flag

**Result:** Fresh start, admin account will be recreated on next page load

### Clear Users Only:
- ✅ All users
- ✅ Current login session
- ✅ Remember me data
- ❌ Accessories/products (preserved)
- ❌ Purchase requests (preserved)
- ❌ Complaints (preserved)

**Result:** Admin account recreated, all products remain

### Logout Only:
- ✅ Current login session
- ✅ Remember me data
- ❌ Users (preserved)
- ❌ All other data (preserved)

**Result:** Just logged out, can login again

---

## Default Admin Account

After clearing users, the system automatically creates:
- **Email:** `admin@honehube.com`
- **Password:** `Admin@123`
- **Role:** admin

---

## Verify Data Was Cleared

### Check in Console:
```javascript
console.log('Users:', Store.getUsers().length);
console.log('Current User:', Store.getCurrentUser());
console.log('Accessories:', Store.getAccessories().length);
```

### Check in localStorage:
1. Press **F12** → **Application** tab
2. Click **Local Storage** → your site
3. Look for these keys:
   - `honehub_users` - Should be empty or have only admin
   - `honehub_current_user` - Should not exist (if logged out)
   - `honehub_accessories` - Check if preserved or cleared
   - `honehub_initialized` - Should be "true" after reload

---

## Quick Reference

| Action | Command | What's Cleared |
|--------|---------|----------------|
| Clear Everything | `Store.clearAll(); location.reload();` | All data |
| Clear Users | `localStorage.removeItem('honehub_users'); Store.initialize();` | Users only |
| Logout | `Store.logout(); location.reload();` | Session only |
| Delete User | `Store.deleteUser(ID);` | One user |
| View Users | `console.log(Store.getUsers());` | (view only) |

---

## Troubleshooting

### Issue: Data Not Clearing
**Solution:** Try hard refresh after clearing
- Windows: `Ctrl + Shift + R`
- Mac: `Cmd + Shift + R`

### Issue: Admin Not Recreated
**Solution:** Force initialization
```javascript
localStorage.removeItem('honehub_initialized');
Store.initialize();
console.log('Admin:', Store.findUserByEmail('admin@honehube.com'));
```

### Issue: Still Logged In After Logout
**Solution:** Clear both session and local storage
```javascript
sessionStorage.clear();
localStorage.removeItem('honehub_remember');
location.reload();
```

---

## Recommended Approach

**For Testing:**
1. Use **Clear Users Only** to reset accounts while keeping products
2. This preserves your accessories/products setup
3. Admin account is automatically recreated

**For Fresh Start:**
1. Use **Clear All Data** to completely reset
2. System will reinitialize with default admin and products
3. Good for starting over completely

**For Logout:**
1. Use **Logout Only** to just sign out
2. All data remains intact
3. Can login again immediately

---

## Quick Start

👉 **Fastest way:** Open `http://localhost:8080/honehube/clear-database.html` and click a button!

👉 **Console way:** Press F12, paste `Store.clearAll(); location.reload();` and press Enter!
