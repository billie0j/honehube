# Requirements: Admin Item Management & Student Negotiation System

## Overview
HoneHube is a secure web-based marketplace system where the admin posts accessories such as laptops, chargers, phones, bags, earphones, books, and other student-related items. A student buyer logs in, views available items, and sends a request to buy. The student can also negotiate by reducing the price to a certain amount. The admin then reviews the request and either accepts or denies the negotiated price.

## User Roles

### Admin
- Can post items for sale
- Can manage all listings (create, edit, delete)
- Can view all purchase requests from students
- Can view negotiated prices
- Can accept or deny student offers
- Can mark items as sold
- Can manage student accounts

### Student (Buyer)
- Can browse available items
- Can view item details
- Can send purchase requests
- Can negotiate prices
- Can view their own purchase requests and negotiation history

---

## Functional Requirements

### FR1: Admin - Item Management

#### FR1.1: Create New Listing
**User Story:** As an admin, I want to add new items for sale so that students can purchase them.

**Acceptance Criteria:**
- Admin can access "Create Listing" form from admin dashboard
- Form includes fields:
  - Title (required, max 200 chars)
  - Description (optional, text area)
  - Category (required, dropdown: Laptops, Chargers, Phones, Bags, Earphones, Books, RAM, Storage, Other)
  - Original Price (required, decimal, min 0.01)
  - Condition (required, radio: New/Used)
  - Image (optional, file upload)
- Form validates all required fields
- Image upload supports: JPG, PNG, GIF (max 5MB)
- Image preview shown before upload
- Success message displayed after creation
- Admin redirected to listings view or stays on form (user choice)
- New listing appears immediately in admin dashboard

#### FR1.2: Edit Existing Listing
**User Story:** As an admin, I want to edit item details so that I can correct mistakes or update information.

**Acceptance Criteria:**
- Admin can click "Edit" button on any listing
- Edit form pre-populates with existing data
- Admin can modify any field except creation date
- Admin can change item status (Active/Inactive/Sold)
- Admin can replace or remove existing image
- Changes save immediately
- Success message displayed after update
- Updated listing reflects changes in all views

#### FR1.3: Delete Listing
**User Story:** As an admin, I want to delete listings so that I can remove items that are no longer available.

**Acceptance Criteria:**
- Admin can click "Delete" button on any listing
- Confirmation dialog appears before deletion
- Confirmation shows item title and warns about permanent deletion
- If listing has active purchase requests, admin is warned
- Deletion removes listing from database
- Associated purchase requests are marked as "cancelled"
- Success message displayed after deletion
- Listing removed from all views immediately

#### FR1.4: View All Listings
**User Story:** As an admin, I want to view all listings in one place so that I can manage inventory effectively.

**Acceptance Criteria:**
- Admin dashboard shows listings table with columns:
  - ID, Title, Category, Original Price, Condition, Status, Created Date, Actions
- Table supports sorting by any column
- Table supports filtering by:
  - Status (All/Active/Sold/Inactive)
  - Category
  - Condition
- Search functionality searches title and description
- Pagination (20 items per page)
- Export to CSV functionality
- Quick actions: View, Edit, Delete

---

### FR2: Admin - Purchase Request Management

#### FR2.1: View Purchase Requests
**User Story:** As an admin, I want to view all purchase requests so that I can process student orders.

**Acceptance Criteria:**
- Admin dashboard shows "Purchase Requests" section
- Table displays:
  - Request ID, Student Name, Student ID, Item Title, Original Price, Requested Price, Status, Date, Actions
- Requests sorted by date (newest first)
- Filter by status: All/Pending/Accepted/Denied/Completed
- Filter by student
- Search by student name or item title
- Badge colors indicate status:
  - Pending: Yellow
  - Negotiating: Blue
  - Accepted: Green
  - Denied: Red
  - Completed: Gray

#### FR2.2: View Negotiation Details
**User Story:** As an admin, I want to see the full negotiation history so that I can make informed decisions.

**Acceptance Criteria:**
- Clicking on a purchase request opens detail modal/page
- Detail view shows:
  - Student information (name, email, student ID)
  - Item details (title, description, image, original price)
  - Original purchase request message
  - Negotiation history (chronological):
    - Student's initial offer
    - Admin's counter-offer (if any)
    - Student's revised offer (if any)
    - Timestamps for each message
  - Current status
  - Action buttons (Accept/Deny/Counter-offer)
- All prices clearly displayed with currency (K)
- Percentage difference from original price shown

#### FR2.3: Accept Purchase Request
**User Story:** As an admin, I want to accept a purchase request so that the student can proceed with payment.

**Acceptance Criteria:**
- Admin clicks "Accept" button on purchase request
- Confirmation dialog shows final agreed price
- Upon confirmation:
  - Request status changes to "Accepted"
  - Student receives notification (email/in-app)
  - Item status remains "Active" until marked as sold
  - Timestamp recorded
- Success message displayed
- Request moves to "Accepted" filter

#### FR2.4: Deny Purchase Request
**User Story:** As an admin, I want to deny a purchase request so that I can reject unreasonable offers.

**Acceptance Criteria:**
- Admin clicks "Deny" button on purchase request
- Optional reason field appears
- Upon confirmation:
  - Request status changes to "Denied"
  - Student receives notification with reason (if provided)
  - Item remains available for other students
  - Timestamp recorded
- Success message displayed
- Request moves to "Denied" filter

#### FR2.5: Counter-offer
**User Story:** As an admin, I want to make a counter-offer so that I can negotiate with students.

**Acceptance Criteria:**
- Admin clicks "Counter-offer" button
- Modal appears with:
  - Current student offer
  - Input field for counter-offer price
  - Optional message field
  - Validation: counter-offer must be between student offer and original price
- Upon submission:
  - Counter-offer added to negotiation history
  - Request status changes to "Negotiating"
  - Student receives notification
  - Student can accept, deny, or make new offer
- Counter-offer appears in negotiation timeline

---

### FR3: Admin - Mark Item as Sold

#### FR3.1: Mark as Sold
**User Story:** As an admin, I want to mark an item as sold so that it's no longer available for purchase.

**Acceptance Criteria:**
- Admin can mark item as sold from:
  - Listings table (quick action)
  - Edit listing form
  - Purchase request acceptance flow
- Confirmation dialog appears
- Upon confirmation:
  - Item status changes to "Sold"
  - Item removed from public browse page
  - Item still visible in admin dashboard with "Sold" badge
  - Associated accepted purchase request marked as "Completed"
  - All other pending requests for this item automatically denied
  - Sold date recorded
- Success message displayed
- Statistics updated (sold count, revenue)

---

### FR4: Admin - Student Account Management

#### FR4.1: View All Students
**User Story:** As an admin, I want to view all registered students so that I can manage accounts.

**Acceptance Criteria:**
- Admin dashboard shows "Users" section
- Table displays:
  - ID, Name, Email, Student ID, Join Date, Last Login, Status, Actions
- Filter by:
  - Status (Active/Inactive)
  - Role (Student/Admin)
- Search by name, email, or student ID
- Sort by any column
- Export to CSV

#### FR4.2: View Student Details
**User Story:** As an admin, I want to view detailed student information so that I can verify accounts.

**Acceptance Criteria:**
- Clicking "View" opens student detail page/modal
- Shows:
  - Full profile information
  - Purchase history (all requests)
  - Active negotiations
  - Account statistics (total requests, accepted, denied)
  - Account status
  - Registration date
  - Last login date

#### FR4.3: Edit Student Account
**User Story:** As an admin, I want to edit student accounts so that I can correct information or update roles.

**Acceptance Criteria:**
- Admin clicks "Edit" on student account
- Form allows editing:
  - Name
  - Email
  - Student ID
  - Role (Student/Admin)
  - Status (Active/Inactive)
- Cannot edit password (student must reset)
- Validation ensures email uniqueness
- Changes save immediately
- Success message displayed
- Student receives notification of changes

#### FR4.4: Deactivate/Activate Student Account
**User Story:** As an admin, I want to deactivate problematic accounts so that they cannot access the system.

**Acceptance Criteria:**
- Admin clicks "Deactivate" button
- Confirmation dialog with reason field
- Upon confirmation:
  - Account status changes to "Inactive"
  - Student cannot login
  - Active sessions terminated
  - All pending purchase requests cancelled
  - Student receives notification
- Admin can reactivate account later
- Reactivation restores access but not cancelled requests

#### FR4.5: Delete Student Account
**User Story:** As an admin, I want to delete student accounts so that I can remove spam or duplicate accounts.

**Acceptance Criteria:**
- Admin clicks "Delete" button
- Strong confirmation dialog (type "DELETE" to confirm)
- Warning about permanent deletion
- Upon confirmation:
  - Account permanently deleted
  - All purchase requests marked as "cancelled"
  - Purchase history preserved for admin records
  - Cannot be undone
- Success message displayed
- Account removed from all views

---

### FR5: Student - Browse and View Items

#### FR5.1: Browse Available Items
**User Story:** As a student, I want to browse available items so that I can find what I need.

**Acceptance Criteria:**
- Home page shows grid of active items
- Each item card displays:
  - Image (or placeholder icon)
  - Title
  - Category
  - Price
  - Condition badge
  - "View Details" button
- Filter by category
- Filter by condition (New/Used)
- Filter by price range (slider)
- Sort by: Price (low to high), Price (high to low), Newest, Oldest
- Search by title or description
- Pagination or infinite scroll
- Only "Active" items shown to students

#### FR5.2: View Item Details
**User Story:** As a student, I want to view detailed item information so that I can make informed decisions.

**Acceptance Criteria:**
- Clicking item opens detail page
- Shows:
  - Large image (or placeholder)
  - Title
  - Full description
  - Category
  - Original price
  - Condition
  - Posted date
  - "Send Purchase Request" button (if logged in)
  - "Login to Purchase" button (if not logged in)
- Breadcrumb navigation
- Back to browse button

---

### FR6: Student - Purchase Requests

#### FR6.1: Send Purchase Request
**User Story:** As a student, I want to send a purchase request so that I can buy an item.

**Acceptance Criteria:**
- Student clicks "Send Purchase Request" on item detail page
- Modal/form appears with:
  - Item summary (title, price)
  - Message field (optional, max 500 chars)
  - "Offer Price" field (optional, must be less than original price)
  - Checkbox: "I agree to the terms and conditions"
  - Submit button
- Validation:
  - Offered price must be positive number
  - Offered price must be less than original price
  - Terms must be accepted
- Upon submission:
  - Request created with status "Pending"
  - Admin receives notification
  - Student sees success message
  - Student redirected to "My Requests" page
- Student cannot send multiple requests for same item

#### FR6.2: View My Purchase Requests
**User Story:** As a student, I want to view my purchase requests so that I can track their status.

**Acceptance Criteria:**
- Student dashboard shows "My Purchase Requests" section
- Table displays:
  - Request ID, Item Title, Original Price, My Offer, Status, Date, Actions
- Filter by status: All/Pending/Negotiating/Accepted/Denied/Completed
- Sort by date
- Status badges with colors
- Actions: View Details, Cancel (if pending)

#### FR6.3: View Request Details
**User Story:** As a student, I want to view request details so that I can see negotiation history.

**Acceptance Criteria:**
- Clicking request opens detail page
- Shows:
  - Item information
  - Original request message
  - Negotiation timeline:
    - My initial offer
    - Admin's counter-offer (if any)
    - My revised offer (if any)
    - Timestamps
  - Current status
  - Action buttons based on status:
    - If "Negotiating": Accept Counter-offer, Make New Offer, Cancel
    - If "Accepted": View Payment Instructions
    - If "Denied": View Reason
    - If "Completed": Leave Review (future feature)

#### FR6.4: Negotiate Price
**User Story:** As a student, I want to negotiate the price so that I can get a better deal.

**Acceptance Criteria:**
- If admin makes counter-offer, student receives notification
- Student can:
  - Accept counter-offer (request becomes "Accepted")
  - Make new offer (must be between their last offer and admin's counter-offer)
  - Cancel request
- New offer form includes:
  - Current negotiation summary
  - New offer price field
  - Optional message
  - Validation: new offer must be reasonable
- Upon submission:
  - New offer added to timeline
  - Admin receives notification
  - Status remains "Negotiating"
- Maximum 5 negotiation rounds per request

#### FR6.5: Cancel Purchase Request
**User Story:** As a student, I want to cancel my purchase request so that I can change my mind.

**Acceptance Criteria:**
- Student clicks "Cancel" button
- Confirmation dialog appears
- Upon confirmation:
  - Request status changes to "Cancelled"
  - Admin receives notification
  - Item becomes available for other students
  - Cannot be undone
- Can only cancel if status is "Pending" or "Negotiating"
- Cannot cancel if status is "Accepted" or "Completed"

---

## Non-Functional Requirements

### NFR1: Security
- All forms protected with CSRF tokens
- Password requirements enforced (8+ chars, uppercase, lowercase, number, special char)
- SQL injection prevention (prepared statements)
- XSS prevention (input sanitization, output encoding)
- Rate limiting on login attempts (5 attempts per 15 minutes)
- Session timeout after 30 minutes of inactivity
- Secure password hashing (bcrypt)
- File upload validation (type, size, content)

### NFR2: Performance
- Page load time < 2 seconds
- Image optimization (max 5MB, auto-resize to 800x800)
- Database queries optimized with indexes
- Pagination for large datasets (20 items per page)
- Lazy loading for images
- Caching for frequently accessed data

### NFR3: Usability
- Responsive design (mobile, tablet, desktop)
- Intuitive navigation
- Clear error messages
- Success feedback for all actions
- Loading indicators for async operations
- Accessible (WCAG 2.1 Level AA)
- Consistent UI patterns

### NFR4: Reliability
- Hybrid mode: Works with or without database
- Graceful degradation if API unavailable
- Data validation on client and server
- Transaction rollback on errors
- Automatic session recovery
- Error logging for debugging

### NFR5: Maintainability
- Clean, documented code
- Modular architecture
- Consistent naming conventions
- Version control (Git)
- Database migrations for schema changes
- API versioning

---

## Data Requirements

### DR1: Purchase Requests Table
```sql
CREATE TABLE purchase_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    listing_id INT NOT NULL,
    buyer_id INT NOT NULL,
    original_price DECIMAL(10, 2) NOT NULL,
    offered_price DECIMAL(10, 2) NULL,
    message TEXT NULL,
    status ENUM('pending', 'negotiating', 'accepted', 'denied', 'cancelled', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### DR2: Negotiations Table
```sql
CREATE TABLE negotiations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    user_id INT NOT NULL,
    user_type ENUM('buyer', 'seller') NOT NULL,
    offered_price DECIMAL(10, 2) NOT NULL,
    message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES purchase_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### DR3: Notifications Table (Future)
```sql
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(500) NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

---

## User Interface Requirements

### UIR1: Admin Dashboard Enhancements
- Add "Create Listing" button (prominent, top-right)
- Add "Purchase Requests" tab/section
- Add negotiation counter badge (shows pending count)
- Add quick stats: Total Items, Active Items, Sold Items, Pending Requests, Total Revenue

### UIR2: Student Dashboard
- Add "My Purchase Requests" section
- Add "Browse Items" quick link
- Add stats: Active Requests, Accepted Requests, Total Spent

### UIR3: Item Detail Page
- Large image display
- Clear pricing information
- "Send Purchase Request" form
- Breadcrumb navigation

### UIR4: Negotiation Interface
- Timeline view of negotiation history
- Clear indication of who made each offer
- Visual price comparison (original vs offered vs counter-offer)
- Action buttons contextual to status

---

## Success Metrics

### SM1: Admin Efficiency
- Time to create listing < 2 minutes
- Time to process purchase request < 1 minute
- 90% of requests processed within 24 hours

### SM2: Student Satisfaction
- 80% of students find items they need
- 70% of negotiations result in accepted offers
- Average response time from admin < 12 hours

### SM3: System Performance
- 99% uptime
- < 2 second page load time
- Zero data loss incidents

---

## Future Enhancements (Out of Scope for v1)

- Real-time notifications (WebSocket)
- Email notifications
- Payment integration
- Review and rating system
- Wishlist functionality
- Advanced search with filters
- Item comparison feature
- Chat system between buyer and seller
- Mobile app (iOS/Android)
- Analytics dashboard
- Automated price suggestions
- Bulk upload for admin
- Image gallery (multiple images per item)

---

## Assumptions

1. Admin is trusted and does not need approval workflow
2. Payment is handled offline (cash, mobile money, etc.)
3. Item pickup/delivery is arranged separately
4. One admin manages all listings initially
5. Students have valid email addresses
6. Students have unique student IDs
7. Internet connection available for both admin and students
8. Modern web browser (Chrome, Firefox, Safari, Edge)
9. XAMPP or similar local server for development
10. MySQL database available

---

## Constraints

1. Must work on XAMPP (Apache + MySQL + PHP)
2. Must maintain hybrid mode (localStorage fallback)
3. Must use existing authentication system
4. Must follow existing UI/UX patterns
5. Must be compatible with existing codebase
6. No external payment gateway integration
7. No SMS notifications (email only)
8. Single currency (Kwacha - K)
9. English language only
10. No mobile app (web only)

---

## Dependencies

1. Existing authentication system (login/register)
2. Existing database schema (users, listings tables)
3. Existing API infrastructure (api/config.php, api/auth.php)
4. Existing frontend framework (vanilla JS, no React/Vue)
5. Existing CSS framework (custom styles)
6. Google reCAPTCHA (already integrated)
7. CSRF token system (already implemented)
8. Session management (already implemented)

---

## Risks and Mitigations

### Risk 1: Database Performance with Many Negotiations
**Mitigation:** Implement pagination, indexing, and archiving old negotiations

### Risk 2: Image Upload Security
**Mitigation:** Strict file validation, virus scanning, separate upload directory

### Risk 3: Price Manipulation
**Mitigation:** Server-side validation, audit logging, price history tracking

### Risk 4: Spam Purchase Requests
**Mitigation:** Rate limiting, CAPTCHA, account verification

### Risk 5: Concurrent Purchases
**Mitigation:** Database transactions, optimistic locking, status checks

---

## Acceptance Testing Scenarios

### Test Scenario 1: Admin Creates Listing
1. Admin logs in
2. Clicks "Create Listing"
3. Fills form with valid data
4. Uploads image
5. Submits form
6. Verifies listing appears in dashboard
7. Verifies listing visible to students

### Test Scenario 2: Student Sends Purchase Request
1. Student logs in
2. Browses items
3. Clicks on item
4. Clicks "Send Purchase Request"
5. Enters offered price (lower than original)
6. Submits request
7. Verifies request appears in "My Requests"
8. Verifies admin sees request in dashboard

### Test Scenario 3: Negotiation Flow
1. Student sends request with offer
2. Admin views request
3. Admin makes counter-offer
4. Student receives notification
5. Student accepts counter-offer
6. Admin marks item as sold
7. Request status becomes "Completed"
8. Item removed from browse page

### Test Scenario 4: Admin Denies Request
1. Admin views purchase request
2. Admin clicks "Deny"
3. Admin enters reason
4. Admin confirms denial
5. Student receives notification
6. Request status becomes "Denied"
7. Item remains available

### Test Scenario 5: Student Cancels Request
1. Student views their request
2. Student clicks "Cancel"
3. Student confirms cancellation
4. Request status becomes "Cancelled"
5. Admin receives notification
6. Item becomes available again

---

## Glossary

- **Listing:** An item posted for sale by the admin
- **Purchase Request:** A student's request to buy an item
- **Negotiation:** Back-and-forth price discussion between student and admin
- **Offer:** Price proposed by student
- **Counter-offer:** Price proposed by admin in response to student offer
- **Original Price:** The initial price set by admin when creating listing
- **Agreed Price:** Final price after negotiation (if accepted)
- **Active Item:** Item available for purchase
- **Sold Item:** Item that has been purchased and marked as sold
- **Inactive Item:** Item temporarily unavailable (hidden from students)

---

**Document Version:** 1.0  
**Last Updated:** April 26, 2026  
**Status:** Draft - Pending Approval
