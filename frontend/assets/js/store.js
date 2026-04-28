// Honehub - Complete Client-Side Storage Manager
// Replaces PHP backend for GitHub Pages deployment
const Store = {
  // Storage Keys
  KEYS: {
    USERS: 'honehub_users',
    CURRENT_USER: 'honehub_current_user',
    LISTINGS: 'honehub_listings',
    ACCESSORIES: 'honehub_accessories',
    PURCHASE_REQUESTS: 'honehub_purchase_requests',
    COMPLAINTS: 'honehub_complaints',
    REMEMBER_ME: 'honehub_remember',
    INITIALIZED: 'honehub_initialized'
  },

  // Get users from localStorage
  getUsers() {
    const users = localStorage.getItem(this.KEYS.USERS);
    return users ? JSON.parse(users) : [];
  },

  // Save users to localStorage
  saveUsers(users) {
    localStorage.setItem(this.KEYS.USERS, JSON.stringify(users));
  },

  // Add new user
  addUser(user) {
    const users = this.getUsers();
    user.id = users.length > 0 ? Math.max(...users.map(u => u.id)) + 1 : 1;
    user.createdAt = user.createdAt || new Date().toISOString();
    user.created_at = user.created_at || new Date().toISOString();
    user.role = user.role || 'student';
    users.push(user);
    this.saveUsers(users);
    console.log('User added successfully:', { id: user.id, email: user.email, name: user.name }); // Debug log
    return user;
  },

  // Find user by email
  findUserByEmail(email) {
    const users = this.getUsers();
    return users.find(u => u.email.toLowerCase() === email.toLowerCase());
  },

  // Find user by student ID
  findUserByStudentId(studentId) {
    const users = this.getUsers();
    return users.find(u => {
      const userStudentId = u.studentId || u.student_id;
      return userStudentId && userStudentId.toLowerCase() === studentId.toLowerCase();
    });
  },

  // Authenticate user
  authenticate(identifier, password) {
    console.log('Authenticating:', identifier); // Debug log
    
    // Try email first
    let user = this.findUserByEmail(identifier);
    console.log('Found by email:', user ? user.email : 'not found'); // Debug log
    
    // Try student ID if not found by email
    if (!user) {
      user = this.findUserByStudentId(identifier);
      console.log('Found by student ID:', user ? user.email : 'not found'); // Debug log
    }
    
    if (user && user.password === password) {
      console.log('Authentication successful:', user.email); // Debug log
      return user;
    }
    
    console.error('Authentication failed:', user ? 'wrong password' : 'user not found'); // Debug log
    return null;
  },

  // Set current user
  setCurrentUser(user, remember = false) {
    if (remember) {
      localStorage.setItem(this.KEYS.REMEMBER_ME, JSON.stringify(user));
    } else {
      sessionStorage.setItem(this.KEYS.CURRENT_USER, JSON.stringify(user));
    }
  },

  // Get current user
  getCurrentUser() {
    let user = sessionStorage.getItem(this.KEYS.CURRENT_USER);
    if (!user) {
      user = localStorage.getItem(this.KEYS.REMEMBER_ME);
    }
    return user ? JSON.parse(user) : null;
  },

  // Logout
  logout() {
    sessionStorage.removeItem(this.KEYS.CURRENT_USER);
    localStorage.removeItem(this.KEYS.REMEMBER_ME);
  },

  // Get listings
  getListings() {
    const listings = localStorage.getItem(this.KEYS.LISTINGS);
    return listings ? JSON.parse(listings) : this.getDefaultListings();
  },

  // Default listings
  getDefaultListings() {
    return [
      {
        id: 1,
        title: 'Modern Apartment',
        description: 'Spacious 2-bedroom apartment in city center',
        price: 350,
        location: 'Harare',
        type: 'Apartment',
        bedrooms: 2,
        image: '🏠'
      },
      {
        id: 2,
        title: 'Cozy Studio',
        description: 'Perfect for students, near Evlyne Hone College',
        price: 150,
        location: 'Evlyne Area',
        type: 'Studio',
        bedrooms: 1,
        image: '🏢'
      },
      {
        id: 3,
        title: 'Family House',
        description: '3-bedroom house with garden',
        price: 500,
        location: 'Bulawayo',
        type: 'House',
        bedrooms: 3,
        image: '🏡'
      }
    ];
  },

  // Save listings
  saveListings(listings) {
    localStorage.setItem(this.KEYS.LISTINGS, JSON.stringify(listings));
  },

  // ==================== ACCESSORIES ====================
  
  getAccessories() {
    const accessories = localStorage.getItem(this.KEYS.ACCESSORIES);
    return accessories ? JSON.parse(accessories) : this.getDefaultAccessories();
  },

  saveAccessories(accessories) {
    localStorage.setItem(this.KEYS.ACCESSORIES, JSON.stringify(accessories));
  },

  getDefaultAccessories() {
    return [
      { id: 1, item_name: 'Dell Latitude E7450', category: 'Laptops', description: 'Intel Core i5, 8GB RAM, 256GB SSD, 14" display', original_price: 4500.00, image: '../assets/images/lap1.png', status: 'available', posted_by: 1 },
      { id: 2, item_name: 'Lenovo ThinkPad T480', category: 'Laptops', description: 'Intel Core i7, 16GB RAM, 512GB SSD, excellent condition', original_price: 6500.00, image: '../assets/images/lenove1.png', status: 'available', posted_by: 1 },
      { id: 3, item_name: 'HP EliteBook 840 G5', category: 'Laptops', description: 'Intel Core i5 8th Gen, 8GB DDR4 RAM, 256GB SSD, 14" FHD Display, Excellent condition', original_price: 5200.00, image: '../assets/images/lap1.png', status: 'available', posted_by: 1 },
      { id: 4, item_name: 'Dell Latitude 7490', category: 'Laptops', description: 'Intel Core i5 8th Gen, 8GB DDR4 RAM, 256GB SSD, 14" FHD Display, Business Class', original_price: 5400.00, image: '../assets/images/lap2.png', status: 'available', posted_by: 1 },
      { id: 5, item_name: 'Kingston 16GB DDR4 RAM', category: 'RAM', description: 'Brand new 16GB DDR4 2666MHz memory module', original_price: 750.00, image: '../assets/images/ram.png', status: 'available', posted_by: 1 },
      { id: 6, item_name: 'RAM Module', category: 'RAM', description: 'High-performance RAM module, DDR4, all sizes available (4GB-32GB), multiple speeds', original_price: 650.00, image: '../assets/images/ram.png', status: 'available', posted_by: 1 },
      { id: 7, item_name: 'Samsung 500GB SSD', category: 'Storage', description: 'High-speed SATA SSD, barely used', original_price: 600.00, image: '../assets/images/hard.png', status: 'available', posted_by: 1 },
      { id: 8, item_name: 'External Hard Drive', category: 'Storage', description: 'Portable external hard drive, USB 3.0, multiple capacities available', original_price: 850.00, image: '../assets/images/hard.png', status: 'available', posted_by: 1 },
      { id: 9, item_name: 'HP Laptop Charger 65W', category: 'Chargers', description: 'Compatible with HP laptops, original charger', original_price: 250.00, image: '../assets/images/adapter.png', status: 'available', posted_by: 1 },
      { id: 10, item_name: 'Wireless Charger', category: 'Chargers', description: 'Fast wireless charging pad, Qi-certified, 15W output', original_price: 350.00, image: '../assets/images/wireless.png', status: 'available', posted_by: 1 },
      { id: 11, item_name: 'iPhone 15', category: 'Phones', description: 'Brand new Apple iPhone 15, 128GB, A16 Bionic chip', original_price: 12500.00, image: '../assets/images/iphone 15.webp', status: 'available', posted_by: 1 },
      { id: 12, item_name: 'iPhone X', category: 'Phones', description: 'Apple iPhone X, 64GB, Face ID, dual cameras, excellent condition', original_price: 5800.00, image: '../assets/images/10.png', status: 'available', posted_by: 1 },
      { id: 13, item_name: 'Samsung Galaxy A07', category: 'Phones', description: 'Samsung Galaxy A07, 64GB storage, 4GB RAM', original_price: 1800.00, image: '../assets/images/samsung A07.jpg', status: 'available', posted_by: 1 },
      { id: 25, item_name: 'Samsung Galaxy S20', category: 'Phones', description: 'Samsung Galaxy S20, 128GB, Phantom Gray, Excellent Condition, Triple Camera System', original_price: 4500.00, image: '../assets/images/galaxy20.png', status: 'available', posted_by: 1 },
      { id: 14, item_name: 'Phone Stand', category: 'Accessories', description: 'Adjustable aluminum phone stand, compatible with all smartphones', original_price: 150.00, image: '../assets/images/stand.png', status: 'available', posted_by: 1 },
      { id: 15, item_name: 'Adjustable Laptop Stand', category: 'Accessories', description: 'Ergonomic aluminum laptop stand, adjustable height and angle', original_price: 180.00, image: '../assets/images/stand1.png', status: 'available', posted_by: 1 },
      { id: 16, item_name: 'Laptop Cooling Pad', category: 'Accessories', description: 'RGB laptop cooling pad with 6 quiet fans, adjustable height', original_price: 280.00, image: '../assets/images/coolinpad.png', status: 'available', posted_by: 1 },
      { id: 17, item_name: 'HD Laptop Webcam', category: 'Accessories', description: 'Full HD 1080p USB webcam with built-in microphone', original_price: 320.00, image: '../assets/images/came.jpg', status: 'available', posted_by: 1 },
      { id: 18, item_name: 'Wireless Mouse', category: 'Accessories', description: '2.4GHz wireless mouse with ergonomic design, 18-month battery life', original_price: 150.00, image: '../assets/images/muse.jpg', status: 'available', posted_by: 1 },
      { id: 19, item_name: 'USB Laptop Speakers', category: 'Accessories', description: 'Portable USB-powered stereo speakers, crystal clear sound', original_price: 280.00, image: '../assets/images/spesker.png', status: 'available', posted_by: 1 },
      { id: 20, item_name: 'Wired Earphones', category: 'Accessories', description: 'High-quality wired earphones with deep bass, noise isolation', original_price: 120.00, image: '../assets/images/ear.png', status: 'available', posted_by: 1 },
      { id: 21, item_name: 'Cabled Earbuds', category: 'Accessories', description: 'Premium cabled earbuds with superior sound quality, noise cancellation', original_price: 180.00, image: '../assets/images/bug1.png', status: 'available', posted_by: 1 },
      { id: 22, item_name: 'Power Cable', category: 'Cables', description: 'Universal power cable, 1.5 meters, heavy duty construction', original_price: 80.00, image: '../assets/images/power.png', status: 'available', posted_by: 1 },
      { id: 23, item_name: 'Multi Adapter', category: 'Adapters', description: 'Universal multi adapter with 4 USB ports and 3 AC outlets', original_price: 320.00, image: '../assets/images/adapter.png', status: 'available', posted_by: 1 },
      { id: 24, item_name: 'Triple Monitor Setup', category: 'Monitors', description: 'Professional triple monitor setup, 3x 24-inch Full HD displays', original_price: 8500.00, image: '../assets/images/tri%20monitor.png', status: 'available', posted_by: 1 }
    ];
  },

  addAccessory(accessory) {
    const accessories = this.getAccessories();
    accessory.id = accessories.length > 0 ? Math.max(...accessories.map(a => a.id)) + 1 : 1;
    accessory.posted_by = this.getCurrentUser()?.id || 1;
    accessory.status = 'available';
    accessories.push(accessory);
    this.saveAccessories(accessories);
    return accessory;
  },

  updateAccessory(id, updates) {
    const accessories = this.getAccessories();
    const index = accessories.findIndex(a => a.id === id);
    if (index !== -1) {
      accessories[index] = { ...accessories[index], ...updates };
      this.saveAccessories(accessories);
      return accessories[index];
    }
    return null;
  },

  deleteAccessory(id) {
    const accessories = this.getAccessories();
    const filtered = accessories.filter(a => a.id !== id);
    this.saveAccessories(filtered);
    return filtered.length < accessories.length;
  },

  // ==================== PURCHASE REQUESTS ====================
  
  getPurchaseRequests() {
    const requests = localStorage.getItem(this.KEYS.PURCHASE_REQUESTS);
    return requests ? JSON.parse(requests) : [];
  },

  savePurchaseRequests(requests) {
    localStorage.setItem(this.KEYS.PURCHASE_REQUESTS, JSON.stringify(requests));
  },

  addPurchaseRequest(request) {
    const requests = this.getPurchaseRequests();
    request.request_id = requests.length > 0 ? Math.max(...requests.map(r => r.request_id)) + 1 : 1;
    request.student_id = this.getCurrentUser()?.id || 0;
    request.status = 'pending';
    request.created_at = new Date().toISOString();
    requests.push(request);
    this.savePurchaseRequests(requests);
    return request;
  },

  updatePurchaseRequest(id, updates) {
    const requests = this.getPurchaseRequests();
    const index = requests.findIndex(r => r.request_id === id);
    if (index !== -1) {
      requests[index] = { ...requests[index], ...updates };
      this.savePurchaseRequests(requests);
      return requests[index];
    }
    return null;
  },

  getUserPurchaseRequests(userId) {
    const requests = this.getPurchaseRequests();
    return requests.filter(r => r.student_id === userId);
  },

  // ==================== COMPLAINTS ====================
  
  getComplaints() {
    const complaints = localStorage.getItem(this.KEYS.COMPLAINTS);
    return complaints ? JSON.parse(complaints) : [];
  },

  saveComplaints(complaints) {
    localStorage.setItem(this.KEYS.COMPLAINTS, JSON.stringify(complaints));
  },

  addComplaint(complaint) {
    const complaints = this.getComplaints();
    complaint.complaint_id = complaints.length > 0 ? Math.max(...complaints.map(c => c.complaint_id)) + 1 : 1;
    complaint.user_id = this.getCurrentUser()?.id || 0;
    complaint.status = 'pending';
    complaint.created_at = new Date().toISOString();
    complaint.updated_at = new Date().toISOString();
    complaints.push(complaint);
    this.saveComplaints(complaints);
    return complaint;
  },

  updateComplaint(id, updates) {
    const complaints = this.getComplaints();
    const index = complaints.findIndex(c => c.complaint_id === id);
    if (index !== -1) {
      complaints[index] = { ...complaints[index], ...updates, updated_at: new Date().toISOString() };
      this.saveComplaints(complaints);
      return complaints[index];
    }
    return null;
  },

  getUserComplaints(userId) {
    const complaints = this.getComplaints();
    return complaints.filter(c => c.user_id === userId);
  },

  // ==================== INITIALIZATION ====================
  
  initialize() {
    const initialized = localStorage.getItem(this.KEYS.INITIALIZED);
    if (!initialized) {
      // Create default admin user
      const users = this.getUsers();
      if (users.length === 0) {
        this.addUser({
          id: 1,
          name: 'Admin User',
          full_name: 'Admin User',
          email: 'admin@honehube.com',
          password: 'Admin@123',
          role: 'admin',
          createdAt: new Date().toISOString(),
          created_at: new Date().toISOString()
        });
      }
      
      // Initialize accessories
      this.getAccessories();
      
      // Mark as initialized
      localStorage.setItem(this.KEYS.INITIALIZED, 'true');
    }
  },

  // ==================== USER MANAGEMENT ====================
  
  updateUser(id, updates) {
    const users = this.getUsers();
    const index = users.findIndex(u => u.id === id);
    if (index !== -1) {
      users[index] = { ...users[index], ...updates };
      this.saveUsers(users);
      return users[index];
    }
    return null;
  },

  deleteUser(id) {
    const users = this.getUsers();
    const filtered = users.filter(u => u.id !== id);
    this.saveUsers(filtered);
    return filtered.length < users.length;
  },

  // ==================== UTILITY METHODS ====================
  
  clearAll() {
    Object.values(this.KEYS).forEach(key => {
      localStorage.removeItem(key);
    });
  },

  exportData() {
    return {
      users: this.getUsers(),
      accessories: this.getAccessories(),
      purchaseRequests: this.getPurchaseRequests(),
      complaints: this.getComplaints()
    };
  },

  importData(data) {
    if (data.users) this.saveUsers(data.users);
    if (data.accessories) this.saveAccessories(data.accessories);
    if (data.purchaseRequests) this.savePurchaseRequests(data.purchaseRequests);
    if (data.complaints) this.saveComplaints(data.complaints);
  }
};

// Initialize on load
Store.initialize();

// Make Store available globally
window.Store = Store;