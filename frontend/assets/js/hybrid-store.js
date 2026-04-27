/**
 * HoneHube Hybrid Store
 * Automatically uses backend API when available, falls back to localStorage for GitHub Pages
 */

const HybridStore = {
  useAPI: false,
  initialized: false,

  /**
   * Initialize - Check if backend API is available
   */
  async init() {
    if (this.initialized) return this.useAPI;
    
    try {
      // Try to reach the backend
      const response = await fetch('/backend/api/auth.php?action=csrf', {
        method: 'GET',
        credentials: 'include'
      });
      
      if (response.ok) {
        const data = await response.json();
        if (data.success) {
          this.useAPI = true;
          await API.init();
          console.log('✅ Backend API available - using PHP backend');
        }
      }
    } catch (error) {
      console.log('ℹ️ Backend not available - using localStorage mode');
      this.useAPI = false;
    }
    
    // Initialize Store regardless
    Store.initialize();
    this.initialized = true;
    return this.useAPI;
  },

  // ==================== AUTHENTICATION ====================

  async register(userData) {
    if (this.useAPI) {
      return await API.register(userData);
    }
    
    // Client-side registration
    const { name, email, password, studentId } = userData;
    
    // Validation
    if (!name || !email || !password) {
      return { success: false, message: 'All fields are required' };
    }
    
    // Check if user exists
    if (Store.findUserByEmail(email)) {
      return { success: false, message: 'Email already registered' };
    }
    
    if (studentId && Store.findUserByStudentId(studentId)) {
      return { success: false, message: 'Student ID already registered' };
    }
    
    // Create user
    const user = Store.addUser({
      name,
      full_name: name,
      email,
      password, // In production, this should be hashed
      studentId,
      student_id: studentId,
      role: 'student'
    });
    
    return { 
      success: true, 
      message: 'Registration successful!',
      user: { ...user, password: undefined }
    };
  },

  async login(credentials) {
    if (this.useAPI) {
      return await API.login(credentials);
    }
    
    // Client-side login
    const { identifier, password, remember } = credentials;
    
    const user = Store.authenticate(identifier, password);
    
    if (!user) {
      return { success: false, message: 'Invalid credentials' };
    }
    
    Store.setCurrentUser(user, remember);
    
    return { 
      success: true, 
      message: 'Login successful!',
      user: { ...user, password: undefined }
    };
  },

  async logout() {
    if (this.useAPI) {
      return await API.logout();
    }
    
    Store.logout();
    return { success: true, message: 'Logged out successfully' };
  },

  async getCurrentUser() {
    if (this.useAPI) {
      const result = await API.getCurrentUser();
      return result.success ? result.user : null;
    }
    
    return Store.getCurrentUser();
  },

  // ==================== ACCESSORIES/LISTINGS ====================

  async getAccessories(filters = {}) {
    if (this.useAPI) {
      return await API.getListings(filters);
    }
    
    let accessories = Store.getAccessories();
    
    // Apply filters
    if (filters.category) {
      accessories = accessories.filter(a => a.category === filters.category);
    }
    if (filters.status) {
      accessories = accessories.filter(a => a.status === filters.status);
    }
    if (filters.search) {
      const search = filters.search.toLowerCase();
      accessories = accessories.filter(a => 
        a.item_name.toLowerCase().includes(search) ||
        a.description.toLowerCase().includes(search)
      );
    }
    
    return { 
      success: true, 
      accessories,
      listings: accessories // Alias for compatibility
    };
  },

  async getAccessory(id) {
    if (this.useAPI) {
      return await API.getListing(id);
    }
    
    const accessories = Store.getAccessories();
    const accessory = accessories.find(a => a.id == id);
    
    if (!accessory) {
      return { success: false, message: 'Item not found' };
    }
    
    return { success: true, accessory, listing: accessory };
  },

  async createAccessory(data) {
    const user = await this.getCurrentUser();
    if (!user || user.role !== 'admin') {
      return { success: false, message: 'Unauthorized' };
    }
    
    if (this.useAPI) {
      return await API.createListing(data);
    }
    
    const accessory = Store.addAccessory(data);
    return { success: true, message: 'Item added successfully', accessory };
  },

  async updateAccessory(id, data) {
    const user = await this.getCurrentUser();
    if (!user || user.role !== 'admin') {
      return { success: false, message: 'Unauthorized' };
    }
    
    if (this.useAPI) {
      return await API.updateListing(id, data);
    }
    
    const accessory = Store.updateAccessory(id, data);
    if (!accessory) {
      return { success: false, message: 'Item not found' };
    }
    
    return { success: true, message: 'Item updated successfully', accessory };
  },

  async deleteAccessory(id) {
    const user = await this.getCurrentUser();
    if (!user || user.role !== 'admin') {
      return { success: false, message: 'Unauthorized' };
    }
    
    if (this.useAPI) {
      return await API.deleteListing(id);
    }
    
    const deleted = Store.deleteAccessory(id);
    if (!deleted) {
      return { success: false, message: 'Item not found' };
    }
    
    return { success: true, message: 'Item deleted successfully' };
  },

  // ==================== PURCHASE REQUESTS ====================

  async getPurchaseRequests(filters = {}) {
    const user = await this.getCurrentUser();
    if (!user) {
      return { success: false, message: 'Not authenticated' };
    }
    
    if (this.useAPI) {
      return await API.getRequests(filters);
    }
    
    let requests = Store.getPurchaseRequests();
    
    // Filter by user role
    if (user.role !== 'admin') {
      requests = requests.filter(r => r.student_id === user.id);
    }
    
    // Apply additional filters
    if (filters.status) {
      requests = requests.filter(r => r.status === filters.status);
    }
    
    return { success: true, requests };
  },

  async createPurchaseRequest(data) {
    const user = await this.getCurrentUser();
    if (!user) {
      return { success: false, message: 'Not authenticated' };
    }
    
    if (this.useAPI) {
      return await API.createRequest(data);
    }
    
    const request = Store.addPurchaseRequest(data);
    return { success: true, message: 'Purchase request submitted', request };
  },

  async updatePurchaseRequest(id, data) {
    const user = await this.getCurrentUser();
    if (!user) {
      return { success: false, message: 'Not authenticated' };
    }
    
    if (this.useAPI) {
      return await API.updateRequest(id, data);
    }
    
    const request = Store.updatePurchaseRequest(id, data);
    if (!request) {
      return { success: false, message: 'Request not found' };
    }
    
    return { success: true, message: 'Request updated', request };
  },

  // ==================== COMPLAINTS ====================

  async getComplaints(filters = {}) {
    const user = await this.getCurrentUser();
    if (!user) {
      return { success: false, message: 'Not authenticated' };
    }
    
    if (this.useAPI) {
      return await API.getComplaints(filters);
    }
    
    let complaints = Store.getComplaints();
    
    // Filter by user role
    if (user.role !== 'admin') {
      complaints = complaints.filter(c => c.user_id === user.id);
    }
    
    // Apply additional filters
    if (filters.status) {
      complaints = complaints.filter(c => c.status === filters.status);
    }
    
    return { success: true, complaints };
  },

  async createComplaint(data) {
    const user = await this.getCurrentUser();
    if (!user) {
      return { success: false, message: 'Not authenticated' };
    }
    
    if (this.useAPI) {
      return await API.createComplaint(data);
    }
    
    const complaint = Store.addComplaint(data);
    return { success: true, message: 'Complaint submitted', complaint };
  },

  async updateComplaint(id, data) {
    const user = await this.getCurrentUser();
    if (!user || user.role !== 'admin') {
      return { success: false, message: 'Unauthorized' };
    }
    
    if (this.useAPI) {
      return await API.updateComplaint(id, data);
    }
    
    const complaint = Store.updateComplaint(id, data);
    if (!complaint) {
      return { success: false, message: 'Complaint not found' };
    }
    
    return { success: true, message: 'Complaint updated', complaint };
  },

  // ==================== USER MANAGEMENT ====================

  async getUsers() {
    const user = await this.getCurrentUser();
    if (!user || user.role !== 'admin') {
      return { success: false, message: 'Unauthorized' };
    }
    
    if (this.useAPI) {
      return await API.getUsers();
    }
    
    const users = Store.getUsers().map(u => ({ ...u, password: undefined }));
    return { success: true, users };
  },

  async updateUser(id, data) {
    const user = await this.getCurrentUser();
    if (!user || user.role !== 'admin') {
      return { success: false, message: 'Unauthorized' };
    }
    
    if (this.useAPI) {
      return await API.updateUser(id, data);
    }
    
    const updatedUser = Store.updateUser(id, data);
    if (!updatedUser) {
      return { success: false, message: 'User not found' };
    }
    
    return { success: true, message: 'User updated', user: { ...updatedUser, password: undefined } };
  },

  async deleteUser(id) {
    const user = await this.getCurrentUser();
    if (!user || user.role !== 'admin') {
      return { success: false, message: 'Unauthorized' };
    }
    
    if (this.useAPI) {
      return await API.deleteUser(id);
    }
    
    const deleted = Store.deleteUser(id);
    if (!deleted) {
      return { success: false, message: 'User not found' };
    }
    
    return { success: true, message: 'User deleted' };
  }
};

// Make HybridStore available globally
window.HybridStore = HybridStore;
