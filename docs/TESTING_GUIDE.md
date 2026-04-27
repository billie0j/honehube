# Testing Guide - Complaints System

## Quick Start Testing Guide

This guide will help you test the newly implemented complaints system in HoneHube.

---

## Prerequisites

1. **Database Setup:**
   ```bash
   # Import the updated schema
   mysql -u root -p honehube < backend/database/schema.sql
   ```

2. **Server Running:**
   - XAMPP Apache and MySQL running
   - Access HoneHube at: `http://localhost/honehube/`

3. **Test Accounts:**
   - **Admin:** admin@honehube.com / Admin@123
   - **Student:** Create a new student account via registration

---

## Test Scenario 1: Student Submits Complaint

### Steps:

1. **Login as Student**
   - Go to `http://localhost/honehube/frontend/pages/login.html`
   - Register a new student account or use existing credentials

2. **Browse Items**
   - Navigate to home page
   - Browse available items
   - Click on any item (e.g., "iPhone 15" or "Wireless Charger")

3. **Submit Complaint**
   - Scroll down to "Report an Issue" section
   - Click "📋 Report Issue with This Item" button
   - Fill out the complaint form:
     - **Issue Type:** Select "Malfunction"
     - **Subject:** "Device not charging properly"
     - **Description:** "The iPhone 15 I purchased is not charging. The charging port seems damaged and the device only charges intermittently. I've tried multiple cables and chargers."
   - Click "Submit Complaint"
   - ✅ **Expected:** Success message appears, modal closes after 3 seconds

4. **View Complaint in Dashboard**
   - Navigate to student dashboard
   - Scroll to "📋 My Complaints" section
   - ✅ **Expected:** Your complaint appears in the table with "pending" status
   - Click "View" button
   - ✅ **Expected:** Modal shows full complaint details

5. **Test Filtering**
   - Click different status tabs (All, Pending, Investigating, Resolved, Rejected)
   - ✅ **Expected:** Complaints filter correctly

---

## Test Scenario 2: Admin Manages Complaints

### Steps:

1. **Login as Admin**
   - Logout from student account
   - Login with: admin@honehube.com / Admin@123

2. **View Complaints Dashboard**
   - Navigate to admin dashboard
   - Scroll to "📋 Item Complaints" section
   - ✅ **Expected:** All complaints from all students are visible
   - ✅ **Expected:** Statistics card shows total complaints count

3. **View Complaint Details**
   - Click "View" on any complaint
   - ✅ **Expected:** Modal shows:
     - Student information (name, email)
     - Item information (name, price, image)
     - Full complaint details
     - Current status

4. **Respond to Complaint**
   - Click "Respond" button
   - Change status to "Investigating"
   - Enter response: "Thank you for reporting this issue. We are investigating the problem. Please bring the device to our office for inspection."
   - Click "Submit Response"
   - ✅ **Expected:** Success message, complaint status updated

5. **Test Status Updates**
   - View the same complaint again
   - Click "Respond"
   - Change status to "Resolved"
   - Enter response: "We have replaced the charging port. The device is now working properly. Please test it and let us know if you have any further issues."
   - Click "Submit Response"
   - ✅ **Expected:** Status changes to "Resolved", resolved_at timestamp set

6. **Test Filtering**
   - Click filter buttons: All, Pending, Investigating
   - ✅ **Expected:** Complaints filter correctly

7. **Test Delete**
   - Click "Delete" on a complaint
   - Confirm deletion
   - ✅ **Expected:** Complaint removed from list

---

## Test Scenario 3: Student Views Admin Response

### Steps:

1. **Login as Student**
   - Login with the student account that submitted the complaint

2. **Check Dashboard**
   - Navigate to dashboard
   - Go to "My Complaints" section
   - ✅ **Expected:** Complaint status updated to "Investigating" or "Resolved"

3. **View Response**
   - Click "View" on the complaint
   - ✅ **Expected:** Modal shows:
     - Original complaint details
     - Admin response in colored box (blue for investigating, green for resolved)
     - Response timestamp

---

## Test Scenario 4: Multiple Complaint Types

### Test each complaint type:

1. **Malfunction**
   - Subject: "Device not working"
   - Description: "The laptop won't turn on after charging overnight."

2. **Defect**
   - Subject: "Screen has dead pixels"
   - Description: "The phone screen has several dead pixels in the center."

3. **Not as Described**
   - Subject: "Different specifications"
   - Description: "The laptop has 4GB RAM instead of the advertised 8GB."

4. **Damaged**
   - Subject: "Item arrived damaged"
   - Description: "The charger cable is frayed and the connector is bent."

5. **Other**
   - Subject: "Missing accessories"
   - Description: "The phone came without the original box and earphones."

✅ **Expected:** All complaint types submit successfully and display correctly

---

## Test Scenario 5: Validation Testing

### Test form validation:

1. **Empty Issue Type**
   - Leave issue type unselected
   - Try to submit
   - ✅ **Expected:** Error message "Please select an issue type"

2. **Short Subject**
   - Enter subject: "Bad"
   - Try to submit
   - ✅ **Expected:** Error message "Subject must be at least 5 characters"

3. **Short Description**
   - Enter description: "Broken"
   - Try to submit
   - ✅ **Expected:** Error message "Description must be at least 10 characters"

4. **Valid Submission**
   - Fill all fields correctly
   - ✅ **Expected:** Complaint submits successfully

---

## Test Scenario 6: Security Testing

### Test access control:

1. **Unauthenticated Access**
   - Logout from all accounts
   - Try to access: `http://localhost/honehube/backend/api/complaints.php?action=list`
   - ✅ **Expected:** 401 Unauthorized error

2. **Student Accessing Admin Endpoint**
   - Login as student
   - Try to access admin complaints list via browser console:
     ```javascript
     API.getAllComplaints().then(console.log)
     ```
   - ✅ **Expected:** 403 Forbidden error

3. **Student Viewing Other's Complaint**
   - Create two student accounts
   - Submit complaint from Account A
   - Login as Account B
   - Try to access Account A's complaint via API
   - ✅ **Expected:** 403 Access denied

4. **CSRF Token Validation**
   - Try to submit complaint without CSRF token
   - ✅ **Expected:** 403 Invalid security token

---

## Test Scenario 7: Statistics Verification

### Verify statistics update correctly:

1. **Admin Dashboard Statistics**
   - Login as admin
   - Note the "Total Complaints" count
   - Submit a new complaint as student
   - Refresh admin dashboard
   - ✅ **Expected:** Total complaints count increases by 1
   - ✅ **Expected:** Pending complaints count increases by 1

2. **Student Dashboard Statistics**
   - Login as student
   - Note "My Complaints" count
   - Submit a new complaint
   - Refresh dashboard
   - ✅ **Expected:** My complaints count increases by 1

---

## Test Scenario 8: Wireless Charger Product

### Verify new product:

1. **Browse Products**
   - Go to home page
   - ✅ **Expected:** Wireless Charger appears in product list

2. **View Product Details**
   - Click on Wireless Charger
   - ✅ **Expected:** 
     - Title: "Wireless Charger"
     - Price: K350.00
     - Category: Chargers
     - Description: "Fast wireless charging pad, Qi-certified, 15W output..."
     - Image: wireless.png displays correctly
     - Status: Available

3. **Purchase Request**
   - Login as student
   - Submit purchase request for Wireless Charger
   - ✅ **Expected:** Request submits successfully

4. **Report Issue**
   - Click "Report Issue with This Item"
   - Submit complaint about Wireless Charger
   - ✅ **Expected:** Complaint submits successfully

---

## Common Issues & Solutions

### Issue 1: Database Connection Error
**Solution:** 
- Check XAMPP MySQL is running
- Verify database credentials in `backend/config/config.php`
- Ensure `honehube` database exists

### Issue 2: CSRF Token Error
**Solution:**
- Clear browser cache and cookies
- Logout and login again
- Check session is working properly

### Issue 3: Complaints Not Showing
**Solution:**
- Check browser console for JavaScript errors
- Verify API endpoints are accessible
- Check user is authenticated

### Issue 4: Images Not Loading
**Solution:**
- Verify image files exist in `frontend/assets/images/`
- Check file paths are correct
- Ensure Apache has read permissions

---

## Browser Console Testing

### Test API directly in browser console:

```javascript
// Initialize API
await HybridStore.init();

// Submit complaint (as student)
const complaint = await API.submitComplaint({
  item_id: 6,
  complaint_type: 'malfunction',
  subject: 'Test complaint',
  description: 'This is a test complaint for debugging purposes.'
});
console.log(complaint);

// Get my complaints (as student)
const myComplaints = await API.getMyComplaints();
console.log(myComplaints);

// Get all complaints (as admin)
const allComplaints = await API.getAllComplaints();
console.log(allComplaints);

// Update complaint status (as admin)
const update = await API.updateComplaintStatus(1, 'investigating', 'We are looking into this issue.');
console.log(update);
```

---

## Expected Results Summary

### ✅ All Tests Should Pass:
- [x] Students can submit complaints
- [x] Students can view their complaints
- [x] Students can see admin responses
- [x] Admins can view all complaints
- [x] Admins can respond to complaints
- [x] Admins can update complaint status
- [x] Admins can delete complaints
- [x] Form validation works correctly
- [x] Access control is enforced
- [x] CSRF protection works
- [x] Statistics update correctly
- [x] Filtering works properly
- [x] Wireless Charger product is visible
- [x] No JavaScript errors in console
- [x] No PHP errors in logs

---

## Performance Testing

### Load Testing:
1. Submit 50+ complaints
2. Check page load times
3. Verify filtering performance
4. Test pagination (if implemented)

### Expected Performance:
- Page load: < 2 seconds
- API response: < 500ms
- Filter/search: < 100ms

---

## Accessibility Testing

### Test with:
- Keyboard navigation (Tab, Enter, Escape)
- Screen reader compatibility
- Color contrast for status badges
- Form labels and ARIA attributes

---

## Mobile Testing

### Test on mobile devices:
- Responsive design
- Touch interactions
- Modal display
- Form usability
- Table scrolling

---

## Final Checklist

Before marking as complete, verify:

- [ ] All test scenarios pass
- [ ] No console errors
- [ ] No PHP errors in logs
- [ ] Database queries are optimized
- [ ] Security features work
- [ ] UI is responsive
- [ ] Documentation is complete
- [ ] Code is clean and commented
- [ ] Git commit is ready

---

## Reporting Issues

If you find any issues during testing:

1. **Document the issue:**
   - What you were doing
   - What you expected
   - What actually happened
   - Browser and version
   - Screenshots if applicable

2. **Check browser console** for JavaScript errors

3. **Check PHP error logs** in XAMPP

4. **Verify database** state using phpMyAdmin

---

## Success Criteria

The complaints system is working correctly when:

✅ Students can submit and track complaints  
✅ Admins can view and respond to all complaints  
✅ Status workflow functions properly  
✅ Security measures are enforced  
✅ UI is intuitive and responsive  
✅ No errors in console or logs  
✅ Statistics update in real-time  
✅ Wireless Charger product is available  

---

**Happy Testing! 🎉**
