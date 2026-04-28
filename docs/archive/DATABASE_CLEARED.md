# Database Clear Tool - Ready to Use

## ✅ What I Created

I've created tools to clear your database login details and all stored data.

### Files Created:
1. **`clear-database.html`** - Interactive page with buttons to clear data
2. **`CLEAR_DATABASE_INSTRUCTIONS.md`** - Detailed instructions for all methods

---

## 🚀 How to Clear Database (Choose One Method)

### Method 1: Use the Clear Page (Easiest - 30 seconds)

1. **Open this URL in your browser:**
   ```
   http://localhost:8080/honehube/clear-database.html
   ```

2. **You'll see 3 buttons:**
   - **Clear All Data** → Removes everything (users, products, requests, complaints)
   - **Clear Users Only** → Removes all users, keeps products
   - **Logout Only** → Just logs out, keeps everything

3. **Click the button you want** → Confirm → Done!

---

### Method 2: Browser Console (Fastest - 10 seconds)

1. **Press F12** (opens Developer Tools)
2. **Click "Console" tab**
3. **Paste this code and press Enter:**

```javascript
Store.clearAll();
sessionStorage.clear();
console.log('✅ All data cleared!');
location.reload();
```

**Done!** Everything is cleared and page reloads.

---

### Method 3: Clear Specific Items

**Clear Users Only (Keep Products):**
```javascript
localStorage.removeItem('honehub_users');
localStorage.removeItem('honehub_initialized');
Store.logout();
Store.initialize();
console.log('✅ Users cleared! Admin recreated.');
```

**Logout Only:**
```javascript
Store.logout();
location.reload();
```

**View Current Users:**
```javascript
console.log('Users:', Store.getUsers());
```

**Delete Specific User:**
```javascript
Store.deleteUser(2); // Replace 2 with user ID
```

---

## 📊 What Gets Cleared

### Option 1: Clear All Data
- ✅ All users (including admin)
- ✅ All accessories/products  
- ✅ All purchase requests
- ✅ All complaints
- ✅ Login sessions
- ✅ Remember me data

**Result:** Complete fresh start. Admin account recreated on reload.

### Option 2: Clear Users Only
- ✅ All users
- ✅ Login sessions
- ❌ Products/accessories (kept)
- ❌ Purchase requests (kept)
- ❌ Complaints (kept)

**Result:** Users reset, products preserved. Admin account recreated.

### Option 3: Logout Only
- ✅ Current login session
- ❌ Everything else (kept)

**Result:** Just logged out. Can login again immediately.

---

## 🔑 Default Admin Account

After clearing users, the system automatically creates:

```
Email: admin@honehube.com
Password: Admin@123
Role: admin
```

You can login with these credentials immediately after clearing.

---

## ✅ Verify Data Was Cleared

### In Browser Console (F12 → Console):
```javascript
console.log('Users:', Store.getUsers().length);
console.log('Current User:', Store.getCurrentUser());
console.log('Products:', Store.getAccessories().length);
```

### In localStorage (F12 → Application → Local Storage):
- Check `honehub_users` → Should be empty or only have admin
- Check `honehub_current_user` → Should not exist if logged out
- Check `honehub_accessories` → Check if cleared or preserved

---

## 🎯 Recommended Actions

**For Your Current Issue (Registration Error):**
1. Open: `http://localhost:8080/honehube/clear-database.html`
2. Click: **"Clear Users Only"**
3. This will:
   - Remove all registered users
   - Clear any duplicate emails causing registration issues
   - Recreate admin account
   - Keep all your products/accessories

**Then Try Registering Again:**
1. Go to registration page
2. Use a fresh email (not admin@honehube.com)
3. Should work now!

---

## 📁 Quick Links

- **Clear Database Page:** `http://localhost:8080/honehube/clear-database.html`
- **Registration Page:** `http://localhost:8080/honehube/frontend/pages/register.html`
- **Test Page:** `http://localhost:8080/honehube/test-registration.html`
- **Login Page:** `http://localhost:8080/honehube/frontend/pages/login.html`

---

## 🔧 Troubleshooting

### Data Not Clearing?
Try hard refresh:
- **Windows:** `Ctrl + Shift + R`
- **Mac:** `Cmd + Shift + R`

### Admin Not Recreated?
```javascript
localStorage.removeItem('honehub_initialized');
Store.initialize();
```

### Still Logged In?
```javascript
sessionStorage.clear();
localStorage.removeItem('honehub_remember');
location.reload();
```

---

## 📝 Summary

✅ **Created:** Interactive clear database page
✅ **Created:** Detailed instructions document
✅ **Ready to use:** Open `clear-database.html` and click a button
✅ **Safe:** Asks for confirmation before clearing
✅ **Flexible:** Choose what to clear (all, users only, or logout only)

---

## 🎬 Next Steps

1. **Open:** `http://localhost:8080/honehube/clear-database.html`
2. **Click:** "Clear Users Only" (recommended)
3. **Try:** Register a new account
4. **Check:** Browser console (F12) for detailed logs

The registration debugging tools from earlier will show you exactly what's happening!
