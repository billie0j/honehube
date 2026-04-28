# Registration Error Debugging Guide

## Issue
User getting "Unable to create account. Please try again." error when trying to register.

## Changes Made

### 1. Enhanced Error Logging
Added detailed console logging throughout the registration flow to identify the exact failure point:

**In `frontend/pages/register.html`:**
- Added `console.log('Registration result:', result)` to see the full response
- Added `console.error('Registration failed:', result.message)` to log specific error messages
- Enhanced error message to show if email/student ID is already registered

**In `frontend/assets/js/hybrid-store.js`:**
- Added logging for registration attempts with user data
- Added logging for missing required fields
- Added logging for duplicate email detection
- Added logging for duplicate student ID detection
- Added logging for successful user creation

**In `frontend/assets/js/store.js`:**
- Added logging when user is successfully added to localStorage

### 2. Improved Error Messages
Changed the generic error message to provide more specific feedback:
- If email or student ID already exists: "This email or student ID is already registered. Please use a different one or login."
- For other errors: "Unable to create account. Please try again."

## How to Debug

### Step 1: Open Browser Console
1. Open the registration page: `http://localhost:8080/honehube/frontend/pages/register.html`
2. Press **F12** to open Developer Tools
3. Click on the **Console** tab

### Step 2: Try to Register
1. Fill in the registration form
2. Click "Create Account"
3. Watch the console for log messages

### Step 3: Check Console Output
You should see messages like:

**If registration is working:**
```
Registration attempt: {name: "John Doe", email: "john@evelynhone.ac.zw", student_id: "B123456"}
User added successfully: {id: 2, email: "john@evelynhone.ac.zw", name: "John Doe"}
User created successfully: 2
Registration result: {success: true, message: "Registration successful!", user: {...}}
```

**If email already exists:**
```
Registration attempt: {name: "John Doe", email: "admin@honehube.com", student_id: null}
Email already exists: admin@honehube.com
Registration failed: Email already registered
Registration result: {success: false, message: "Email already registered"}
```

**If student ID already exists:**
```
Registration attempt: {name: "John Doe", email: "john@evelynhone.ac.zw", student_id: "B123456"}
Student ID already exists: B123456
Registration failed: Student ID already registered
Registration result: {success: false, message: "Student ID already registered"}
```

### Step 4: Check localStorage
1. In Developer Tools, click on the **Application** tab (Chrome) or **Storage** tab (Firefox)
2. Expand **Local Storage** in the left sidebar
3. Click on your site URL
4. Look for the key `honehub_users`
5. Click on it to see all registered users

### Step 5: Clear Data if Needed
If you need to start fresh:

**Option A: Clear specific user data**
```javascript
// In browser console, run:
localStorage.removeItem('honehub_users');
localStorage.removeItem('honehub_initialized');
location.reload();
```

**Option B: Clear all Honehube data**
```javascript
// In browser console, run:
Store.clearAll();
location.reload();
```

## Common Issues and Solutions

### Issue 1: Email Already Registered
**Symptom:** Error message says email is already registered
**Solution:** 
- Use a different email address
- OR clear localStorage and try again
- OR login with the existing account

### Issue 2: Student ID Already Registered
**Symptom:** Error message says student ID is already registered
**Solution:**
- Use a different student ID
- OR clear localStorage and try again

### Issue 3: CSRF Token Validation Failing
**Symptom:** Console shows CSRF validation errors
**Solution:**
- Refresh the page to generate a new token
- Make sure you're not using browser back button

### Issue 4: reCAPTCHA Not Completed
**Symptom:** Error says "Please complete the verification"
**Solution:**
- Click the reCAPTCHA checkbox before submitting
- Note: Currently using test reCAPTCHA key

## Testing Steps

### Test 1: Register New User with College Email
1. Go to registration page
2. Select "College Email" tab
3. Enter:
   - Name: Test User
   - Email: test@evelynhone.ac.zw
   - Password: Test@123
4. Complete reCAPTCHA
5. Click "Create Account"
6. Check console for logs
7. Should redirect to index.html

### Test 2: Register with Student ID
1. Go to registration page
2. Select "Student ID" tab
3. Enter:
   - Name: Test User 2
   - Student ID: B999999
   - Password: Test@123
4. Complete reCAPTCHA
5. Click "Create Account"
6. Check console for logs
7. Should redirect to index.html

### Test 3: Register with Gmail
1. Go to registration page
2. Select "Gmail" tab
3. Enter:
   - Name: Test User 3
   - Email: testuser@gmail.com
   - Password: Test@123
4. Complete reCAPTCHA
5. Click "Create Account"
6. Check console for logs
7. Should redirect to index.html

### Test 4: Try Duplicate Email
1. Try to register with admin@honehube.com
2. Should see error: "This email or student ID is already registered"
3. Console should show: "Email already exists: admin@honehube.com"

## Next Steps

1. **Try registering again** and check the browser console (F12)
2. **Copy the console output** and share it if you still see errors
3. **Check localStorage** to see if users are being saved
4. **Try clearing localStorage** if you suspect duplicate data

## Files Modified
- `frontend/pages/register.html` - Enhanced error messages and logging
- `frontend/assets/js/hybrid-store.js` - Added debug logging to registration flow
- `frontend/assets/js/store.js` - Added logging to user creation

## Production Notes
Before deploying to production:
1. Remove or comment out all `console.log()` and `console.error()` statements
2. Revert to generic error messages for security
3. Replace test reCAPTCHA key with production key
4. Implement proper password hashing
