# HoneHube Student/Buyer Guide

## 🎯 Overview
This guide explains how to use HoneHube as a student buyer. You can browse items, send purchase requests, negotiate prices, and track your requests.

---

## 🚀 Getting Started

### Step 1: Register an Account

1. Go to `http://localhost/honehube/register.html`
2. Fill in the registration form:
   - **Full Name** (required)
   - **Email** (required, must be valid)
   - **Password** (required, must meet security requirements)
   - **Student ID** (optional)
3. Complete the reCAPTCHA
4. Click **"Register"**
5. You'll be automatically logged in

**Password Requirements:**
- At least 8 characters
- One uppercase letter
- One lowercase letter
- One number
- One special character (@#$%^&+=!)

---

### Step 2: Login

1. Go to `http://localhost/honehube/login.html`
2. Enter your email and password
3. Complete the reCAPTCHA
4. Click **"Login"**
5. You'll be redirected to the home page

---

## 🛍️ Browsing Items

### View Available Items

1. Go to the home page: `http://localhost/honehube/index.html`
2. You'll see all available items for sale
3. Each item card shows:
   - Item image (or icon)
   - Title
   - Category
   - Price
   - Condition (New/Used)
   - Seller name

---

### Search for Items

**Method 1: Search Bar**
1. Type keywords in the search bar (e.g., "laptop", "charger", "HP")
2. Click **"Search"** or press **Enter**
3. Results will filter automatically

**Method 2: Category Filter**
1. Click on a category tab:
   - All
   - Laptops
   - Chargers
   - Phones
   - Bags
   - Earphones
   - Books
   - RAM
   - Storage
   - Other
2. Items will filter by category

**Method 3: Condition Filter**
1. Use the dropdown menu:
   - All Conditions
   - New
   - Used
2. Items will filter by condition

**Combine Filters:**
- You can use search + category + condition together
- Example: Search "Dell" + Category "Laptops" + Condition "Used"

---

## 📱 Viewing Item Details

1. Click on any item card
2. You'll see the item detail page with:
   - Large image
   - Full title
   - Price
   - Category
   - Condition badge
   - Status badge (if not active)
   - Complete description
   - Seller information
   - Purchase request form (if available)

---

## 💰 Sending a Purchase Request

### Option 1: Buy at Original Price

1. Go to the item detail page
2. Scroll to the **"Request to Buy"** section
3. Leave the **"Your Offer Price"** field empty
4. Optionally add a message
5. Check **"I agree to the terms and conditions"**
6. Click **"📧 Send Purchase Request"**

**What Happens:**
- Request sent to admin with status "Pending"
- Admin will review and respond
- You can track status in your dashboard

---

### Option 2: Negotiate a Lower Price

1. Go to the item detail page
2. Scroll to the **"Request to Buy"** section
3. Enter your offered price in **"Your Offer Price (K)"**
   - Must be less than original price
   - Must be greater than 0
4. See the **price comparison** automatically:
   - Original Price
   - Your Offer
   - You Save (amount and percentage)
5. Optionally add a message explaining your offer
6. Check **"I agree to the terms and conditions"**
7. Click **"📧 Send Purchase Request"**

**Example:**
```
Original Price: K450.00
Your Offer: K350.00
You Save: K100.00 (22.2% off)
Message: "Hi, I'm a student on a budget. Can you accept K350?"
```

**What Happens:**
- Request sent to admin with status "Negotiating"
- Admin can:
  - Accept your offer
  - Deny your offer
  - Make a counter-offer
- You'll see updates in your dashboard

---

## 📊 Tracking Your Requests

### Access Your Dashboard

1. Click **"📊 My Dashboard"** in the navigation bar
2. Or go to `http://localhost/honehube/dashboard.html`

### Dashboard Overview

**Statistics Cards:**
- **My Requests** - Total number of purchase requests
- **Pending** - Requests awaiting admin response
- **Accepted** - Requests approved by admin
- **Negotiating** - Active price negotiations

**Purchase Requests Table:**
- Shows all your requests
- Columns:
  - Item (title and category)
  - Original Price
  - My Offer
  - Status
  - Date
  - Actions (View, Cancel)

---

### Request Status Meanings

#### 🟡 Pending
- **Meaning:** Your request is waiting for admin review
- **What to do:** Wait for admin response
- **Actions available:** View, Cancel

#### 🔵 Negotiating
- **Meaning:** Price negotiation is in progress
- **What to do:** Check negotiation history, admin may have made a counter-offer
- **Actions available:** View, Cancel

#### 🟢 Accepted
- **Meaning:** Admin accepted your offer!
- **What to do:** Contact admin to arrange payment and pickup
- **Actions available:** View

#### 🔴 Denied
- **Meaning:** Admin rejected your offer
- **What to do:** View denial reason, try a different item or higher offer
- **Actions available:** View

#### ⚫ Cancelled
- **Meaning:** You cancelled the request
- **What to do:** Nothing, request is closed
- **Actions available:** View

#### ⚫ Completed
- **Meaning:** Transaction completed, item sold
- **What to do:** Nothing, request is closed
- **Actions available:** View

---

### View Request Details

1. Click **"View"** button on any request
2. Modal shows:
   - **Item Information** - Title, category, original price
   - **Your Request** - Status, your offer, your message, date
   - **Denial Reason** (if denied)
   - **Negotiation History** - All offers and counter-offers with timestamps
   - **Status Messages** - Guidance based on current status

**Negotiation History Example:**
```
You (buyer): K350.00
"Hi, I'm a student on a budget. Can you accept K350?"
2026-04-26 10:30 AM

Admin (seller): K400.00
"I can do K400, that's my best price."
2026-04-26 11:15 AM

You (buyer): K380.00
"How about K380? That's all I can afford."
2026-04-26 12:00 PM

Admin (seller): K390.00
"Final offer: K390."
2026-04-26 12:30 PM
```

---

### Cancel a Request

1. Find the request in your dashboard
2. Click **"Cancel"** button
3. Confirm cancellation
4. Request status changes to "Cancelled"

**Note:** You can only cancel requests with status:
- Pending
- Negotiating

**Cannot cancel:**
- Accepted requests
- Denied requests
- Completed requests

---

## 🤝 Negotiation Process

### How Negotiation Works

1. **You send request with offer** (e.g., K350)
   - Status: Negotiating

2. **Admin reviews and responds:**
   - **Option A:** Accept your offer → Status: Accepted ✅
   - **Option B:** Deny your offer → Status: Denied ❌
   - **Option C:** Make counter-offer (e.g., K400) → Status: Negotiating 💬

3. **If admin makes counter-offer, you can:**
   - Accept the counter-offer (contact admin)
   - Make a new offer (future feature)
   - Cancel the request

4. **Negotiation continues** until:
   - Admin accepts your offer
   - Admin denies your offer
   - You cancel the request
   - Maximum 5 rounds reached

---

### Negotiation Tips

**✅ DO:**
- Be polite and respectful
- Explain why you're offering a lower price
- Research similar items to make fair offers
- Respond promptly to counter-offers
- Be realistic with your budget

**❌ DON'T:**
- Offer extremely low prices (less than 70% of original)
- Send multiple requests for the same item
- Be rude or demanding
- Ignore admin's counter-offers
- Cancel requests unnecessarily

**Example Good Messages:**
```
"Hi, I'm a student and this is within my budget. Can you accept K350?"

"I've seen similar items for K400. Would you consider K380?"

"I really need this for my studies. K350 is all I can afford right now."
```

---

## 📧 After Request is Accepted

### What to Do Next

1. **Check your dashboard** - Status will show "Accepted"
2. **Contact the admin** - Arrange payment and pickup
3. **Make payment** - Follow admin's payment instructions
4. **Pick up item** - Arrange time and location with admin
5. **Admin marks as sold** - Request status becomes "Completed"

**Payment Methods** (arranged with admin):
- Cash
- Mobile money
- Bank transfer
- Other methods as agreed

**Pickup Location:**
- Usually on campus
- Arrange specific location with admin
- Bring student ID for verification

---

## 🔍 Frequently Asked Questions

### Can I send multiple requests for the same item?
No, you can only have one active request per item. If you want to make a new offer, cancel your current request first.

### How long does admin take to respond?
Usually within 24 hours. Check your dashboard regularly for updates.

### Can I edit my offer after sending?
No, but you can cancel and send a new request with a different offer.

### What if admin doesn't respond?
Wait 24-48 hours. If no response, try contacting admin directly or choose a different item.

### Can I negotiate after admin accepts?
No, once accepted, the price is final. Arrange payment and pickup.

### What if I change my mind after acceptance?
Contact admin immediately. Cancelling after acceptance is discouraged and may affect future requests.

### Can I buy multiple items?
Yes! Send separate purchase requests for each item you want.

### Do I need a student ID?
It's optional for registration but may be required for pickup verification.

### Is my information secure?
Yes, all data is encrypted and protected. Passwords are hashed with bcrypt.

### Can I see other students' offers?
No, all purchase requests and negotiations are private between you and the admin.

---

## 🛡️ Safety Tips

### Protect Your Account
- ✅ Use a strong, unique password
- ✅ Never share your password
- ✅ Logout after using shared computers
- ✅ Keep your email secure

### Safe Transactions
- ✅ Meet in public campus locations
- ✅ Inspect item before paying
- ✅ Get receipt or confirmation
- ✅ Report suspicious activity

### Avoid Scams
- ❌ Don't pay before seeing the item
- ❌ Don't share bank details publicly
- ❌ Don't accept deals outside the platform
- ❌ Don't trust "too good to be true" offers

---

## 📱 Mobile Access

HoneHube works on:
- 💻 Desktop computers
- 📱 Tablets
- 📱 Mobile phones

All features are fully responsive and mobile-friendly.

---

## 🆘 Troubleshooting

### Can't login
- Check email and password spelling
- Ensure caps lock is off
- Complete reCAPTCHA
- Try password reset (future feature)

### Items not loading
- Check internet connection
- Refresh the page
- Clear browser cache
- Try different browser

### Can't send purchase request
- Ensure you're logged in
- Check all required fields
- Verify offered price is valid
- Accept terms and conditions

### Request not showing in dashboard
- Refresh the page
- Check if you're logged in
- Verify request was sent successfully

---

## 📞 Need Help?

If you encounter issues:
1. Check this guide first
2. Review the `TROUBLESHOOTING_INSTALL.md` file
3. Contact the admin
4. Check browser console for errors

---

## 📝 Quick Reference

### Student Capabilities Checklist
- ✅ Register and login securely
- ✅ View available accessories
- ✅ Search and filter items
- ✅ View item details
- ✅ Request to buy an item
- ✅ Suggest a lower price
- ✅ Wait for admin approval
- ✅ View request status
- ✅ Track negotiations
- ✅ Cancel pending requests

### Request Status Flow
```
Send Request → Pending → Admin Reviews
                ↓
        ┌───────┴───────┐
        ↓               ↓
    Accepted        Negotiating → Counter-offer → Accept/Deny
        ↓               ↓
    Payment         Cancelled
        ↓
    Completed
```

### Keyboard Shortcuts
- `Ctrl + R` - Refresh page
- `Enter` - Submit search
- `Esc` - Close modal dialogs
- `Tab` - Navigate form fields

---

**Document Version:** 1.0  
**Last Updated:** April 26, 2026  
**System:** HoneHube E-commerce Platform
