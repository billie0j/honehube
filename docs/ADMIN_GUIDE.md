# HoneHube Admin Guide

## 🎯 Overview
This guide explains all the admin features available in the HoneHube marketplace system. As an admin, you have complete control over listings, purchase requests, and user accounts.

---

## 🔐 Admin Login

**Default Admin Credentials:**
- **Email:** admin@honehube.com
- **Password:** Admin@123

**Access URL:**
- Local: `http://localhost/honehube/login.html`
- After login, you'll be redirected to the Admin Dashboard

---

## 📊 Admin Dashboard Features

### 1. **Statistics Overview**
The dashboard displays real-time statistics:
- **Total Users** - Number of registered students
- **Active Listings** - Items currently available for sale
- **Total Value** - Combined value of all listings
- **Purchase Requests** - Pending requests from students

---

## 📦 Item Management

### ✅ Add New Item for Sale

1. Click the **"➕ Create Listing"** button (top of dashboard or quick actions)
2. Fill in the form:
   - **Title*** (required) - Item name (e.g., "Dell Latitude E7450")
   - **Description** - Detailed information about the item
   - **Category*** (required) - Select from:
     - Laptops
     - Chargers
     - Phones
     - Bags
     - Earphones
     - Books
     - RAM
     - Storage
     - Other
   - **Price (K)*** (required) - Original price in Kwacha
   - **Condition*** (required) - New or Used
   - **Image URL** (optional) - Link to item image
3. Click **"Create Listing"**
4. Item appears immediately in the listings table

**Example:**
```
Title: HP Laptop Charger 65W
Description: Compatible with HP laptops, original charger
Category: Chargers
Price: 25.00
Condition: New
```

---

### ✏️ Edit Existing Item

1. Go to **"Manage Listings"** section
2. Find the item you want to edit
3. Click **"Edit"** button
4. Update any fields:
   - Title, Description, Category, Price, Condition
   - **Status** - Change to:
     - **Active** - Available for purchase
     - **Sold** - Item has been sold
     - **Inactive** - Temporarily unavailable
5. Click **"Update Listing"**

**Note:** Changing status to "Sold" will:
- Remove item from student browse page
- Mark accepted purchase request as "Completed"
- Cancel all other pending requests

---

### 🗑️ Delete Item

1. Find the item in the listings table
2. Click **"Delete"** button
3. Confirm deletion
4. Item is permanently removed
5. All related purchase requests are cancelled

**Warning:** Deletion cannot be undone!

---

### 👁️ View Item Details

1. Click **"View"** button on any listing
2. Opens the item detail page
3. Shows full description, price, condition, and status

---

## 📧 Purchase Request Management

### 📋 View All Purchase Requests

The **"Purchase Requests"** section shows:
- Request ID
- Student name
- Item title
- Original price
- Student's offered price
- Status (Pending, Negotiating, Accepted, Denied, Cancelled, Completed)
- Date submitted
- Action buttons

**Status Meanings:**
- 🟡 **Pending** - New request, awaiting your response
- 🔵 **Negotiating** - Price negotiation in progress
- 🟢 **Accepted** - You accepted the offer
- 🔴 **Denied** - You rejected the offer
- ⚫ **Cancelled** - Student cancelled the request
- ⚫ **Completed** - Item sold and transaction complete

---

### 👁️ View Request Details

1. Click **"View"** button on any request
2. Modal shows:
   - **Student Information** - Name, email, student ID
   - **Item Information** - Title, category, original price
   - **Request Details** - Offered price, message, status
   - **Negotiation History** - All offers and counter-offers with timestamps

---

### ✅ Accept Purchase Request

**Option 1: Quick Accept**
1. Click **"Accept"** button in the requests table
2. Confirm acceptance
3. Request status changes to "Accepted"
4. Student receives notification

**Option 2: From Details Modal**
1. Click **"View"** to open request details
2. Review all information
3. Click **"Accept"** button
4. Confirm acceptance

**What Happens:**
- Request marked as "Accepted"
- Student can proceed with payment/pickup
- Item remains "Active" until you mark it as "Sold"

---

### ❌ Deny Purchase Request

1. Click **"Deny"** button
2. Enter reason for denial (optional but recommended)
3. Confirm denial
4. Request status changes to "Denied"
5. Student receives notification with reason

**Example Reasons:**
- "Price too low"
- "Item reserved for another buyer"
- "Item no longer available"

---

### 💬 Make Counter-offer

When a student offers a price that's too low, you can negotiate:

1. Click **"View"** on the request
2. Click **"Counter-offer"** button
3. Enter your counter-offer price
   - Must be higher than student's offer
   - Must be lower than original price
4. Add optional message (e.g., "This is my best price")
5. Click **"Send Counter-offer"**

**What Happens:**
- Request status changes to "Negotiating"
- Student receives notification
- Student can:
  - Accept your counter-offer
  - Make a new offer
  - Cancel the request

**Negotiation Example:**
```
Original Price: K450.00
Student Offer: K350.00
Your Counter-offer: K400.00
Student's New Offer: K380.00
Your Final Counter-offer: K390.00
Student Accepts: K390.00 ✅
```

**Tip:** Maximum 5 negotiation rounds per request to keep things moving.

---

### 🏷️ Mark Item as Sold

After accepting a request and completing the transaction:

**Option 1: From Listings Table**
1. Find the item in "Manage Listings"
2. Click **"Edit"**
3. Change **Status** to "Sold"
4. Click **"Update Listing"**

**Option 2: Quick Action**
1. After accepting a purchase request
2. Go to the listing
3. Mark as sold

**What Happens:**
- Item removed from student browse page
- Accepted request marked as "Completed"
- All other pending requests automatically denied
- Item still visible in admin dashboard with "Sold" badge
- Statistics updated

---

## 👥 Student Account Management

### 📋 View All Students

The **"Recent Users"** section shows:
- User ID
- Name
- Email
- Student ID
- Role (User/Admin)
- Join date
- Status (Active/Inactive)
- Action buttons

---

### 👁️ View Student Details

1. Click **"View"** button on any student
2. Modal shows:
   - Full profile information
   - Account statistics:
     - Total purchase requests
     - Accepted requests
     - Denied requests
   - Registration date
   - Last login date

---

### ✏️ Edit Student Account

1. Click **"Edit"** button
2. Update fields:
   - Name
   - Email
   - Student ID
   - Role (User/Admin)
   - Status (Active/Inactive)
3. Click **"Update User"**

**Note:** Cannot edit passwords. Students must reset their own passwords.

---

### 🚫 Deactivate Student Account

Use this for problematic accounts:

1. Click **"Edit"** button
2. Change **Status** to "Inactive"
3. Click **"Update User"**

**What Happens:**
- Student cannot login
- Active sessions terminated
- All pending purchase requests cancelled
- Account can be reactivated later

---

### 🗑️ Delete Student Account

**Warning:** Use with caution! This is permanent.

1. Click **"Delete"** button
2. Confirm deletion (cannot be undone)
3. Account permanently removed
4. Purchase history preserved for records

**Cannot Delete:**
- Your own admin account
- Other admin accounts

---

## 🔄 Workflow Examples

### Example 1: Simple Sale (No Negotiation)

1. **Admin:** Create listing - "HP Charger, K25"
2. **Student:** Sends purchase request (no offer)
3. **Admin:** Reviews request, clicks "Accept"
4. **Student:** Pays and picks up item
5. **Admin:** Marks item as "Sold"
6. **System:** Request marked as "Completed"

---

### Example 2: Price Negotiation

1. **Admin:** Create listing - "Dell Laptop, K450"
2. **Student:** Sends request with offer: K350
3. **Admin:** Makes counter-offer: K400
4. **Student:** Makes new offer: K380
5. **Admin:** Makes final counter-offer: K390
6. **Student:** Accepts K390
7. **Admin:** Clicks "Accept" on request
8. **Student:** Pays K390 and picks up laptop
9. **Admin:** Marks item as "Sold"
10. **System:** Request marked as "Completed"

---

### Example 3: Denied Request

1. **Admin:** Create listing - "MacBook Pro, K1200"
2. **Student:** Sends request with offer: K600
3. **Admin:** Reviews offer (too low)
4. **Admin:** Clicks "Deny" with reason: "Price too low, minimum K1000"
5. **System:** Request marked as "Denied"
6. **Student:** Receives notification with reason
7. **Item:** Remains available for other students

---

## 📊 Reports & Export

### Export Users
1. Go to "Recent Users" section
2. Click **"📥 Export"** button
3. Downloads CSV file with all user data

### Export Listings
1. Go to "Recent Listings" section
2. Click **"📥 Export"** button
3. Downloads CSV file with all listing data

---

## 🔍 Search & Filter

### Filter Listings
- **By Status:** All / Active / Sold / Inactive
- **By Category:** All / Laptops / Chargers / etc.
- **Search:** Type in title or description

### Filter Purchase Requests
- **By Status:** All / Pending / Negotiating / Accepted / Denied / Completed
- **Search:** Type student name or item title

### Filter Users
- **By Role:** All / User / Admin
- **By Status:** All / Active / Inactive
- **Search:** Type name, email, or student ID

---

## 💡 Best Practices

### Pricing Strategy
- ✅ Set competitive prices based on item condition
- ✅ Be open to reasonable negotiations (10-15% off)
- ✅ Consider student budgets
- ❌ Don't accept offers below 70% of original price

### Communication
- ✅ Respond to requests within 24 hours
- ✅ Provide clear reasons when denying requests
- ✅ Be professional and courteous
- ✅ Use counter-offers to find middle ground

### Item Listings
- ✅ Write detailed descriptions
- ✅ Include item condition honestly
- ✅ Add images when possible
- ✅ Update status promptly when sold
- ❌ Don't leave sold items as "Active"

### Account Management
- ✅ Review user accounts regularly
- ✅ Deactivate spam accounts
- ✅ Keep admin credentials secure
- ❌ Don't delete accounts unless absolutely necessary

---

## 🛠️ Troubleshooting

### "API not available" message
- Check if XAMPP is running
- Verify MySQL is started
- Run `install.php` to set up database
- System will work in localStorage mode as fallback

### Can't create listings
- Verify you're logged in as admin
- Check all required fields are filled
- Ensure price is greater than 0
- Try refreshing the page

### Purchase requests not showing
- Click "🔄 Refresh" button
- Check if database is running
- Verify students have submitted requests

### Can't edit/delete items
- Verify admin access
- Check if item exists
- Try refreshing the page
- Check browser console for errors

---

## 🔒 Security Features

All admin actions are protected with:
- ✅ CSRF token validation
- ✅ Admin role verification
- ✅ SQL injection prevention
- ✅ Input sanitization
- ✅ Session management
- ✅ Rate limiting

---

## 📱 Mobile Access

The admin dashboard is fully responsive and works on:
- 💻 Desktop computers
- 📱 Tablets
- 📱 Mobile phones

---

## 🆘 Need Help?

If you encounter issues:
1. Check the `TROUBLESHOOTING_INSTALL.md` file
2. Verify XAMPP is running properly
3. Check browser console for errors
4. Review the `DATABASE_SETUP.md` for database issues

---

## 📝 Quick Reference

### Admin Capabilities Checklist
- ✅ Login securely
- ✅ Add accessories/items for sale
- ✅ Set original price
- ✅ Upload item image
- ✅ Edit or delete items
- ✅ View student purchase requests
- ✅ View negotiated prices
- ✅ Accept or deny student offers
- ✅ Mark item as sold
- ✅ Manage student accounts

### Keyboard Shortcuts
- `Ctrl + R` - Refresh page
- `Esc` - Close modal dialogs
- `Tab` - Navigate form fields

---

**Document Version:** 1.0  
**Last Updated:** April 26, 2026  
**System:** HoneHube E-commerce Platform
