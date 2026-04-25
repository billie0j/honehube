// Honehub - Local Storage Manager
const Store = {
  // Keys
  KEYS: {
    USERS: 'honehub_users',
    CURRENT_USER: 'honehub_current_user',
    LISTINGS: 'honehub_listings',
    REMEMBER_ME: 'honehub_remember'
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
    users.push(user);
    this.saveUsers(users);
  },

  // Find user by email
  findUserByEmail(email) {
    const users = this.getUsers();
    return users.find(u => u.email.toLowerCase() === email.toLowerCase());
  },

  // Find user by student ID
  findUserByStudentId(studentId) {
    const users = this.getUsers();
    return users.find(u => u.studentId && u.studentId.toLowerCase() === studentId.toLowerCase());
  },

  // Authenticate user
  authenticate(identifier, password) {
    // Try email first
    let user = this.findUserByEmail(identifier);
    
    // Try student ID if not found by email
    if (!user) {
      user = this.findUserByStudentId(identifier);
    }
    
    if (user && user.password === password) {
      return user;
    }
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
  }
};

// Make Store available globally
window.Store = Store;