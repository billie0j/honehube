/**
 * Honehube API Client
 * Handles all API communication with the backend
 */

const API = {
  baseURL: window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
    ? '/honehube/backend/api'  // Local development
    : '/backend/api',          // Production

  // CSRF token storage
  csrfToken: null,

  /**
   * Initialize API - Get CSRF token
   */
  async init() {
    try {
      const response = await fetch(`${this.baseURL}/auth.php?action=csrf`, {
        credentials: 'include'
      });
      const data = await response.json();
      if (data.success) {
        this.csrfToken = data.csrf_token;
        return true;
      }
    } catch (error) {
      console.error('API initialization failed:', error);
      // Fallback to localStorage mode if API is not available
      return false;
    }
  },

  /**
   * Make API request
   */
  async request(endpoint, options = {}) {
    const url = `${this.baseURL}${endpoint}`;
    const config = {
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json',
        ...options.headers
      },
      ...options
    };

    try {
      const response = await fetch(url, config);
      const data = await response.json();
      
      // Update CSRF token if provided
      if (data.csrf_token) {
        this.csrfToken = data.csrf_token;
      }
      
      return data;
    } catch (error) {
      console.error('API request failed:', error);
      return { success: false, message: 'Network error. Please try again.' };
    }
  },

  /**
   * Register new user
   */
  async register(userData) {
    const data = {
      ...userData,
      csrf_token: this.csrfToken,
      recaptcha_response: typeof grecaptcha !== 'undefined' ? grecaptcha.getResponse() : null
    };

    return await this.request('/auth.php?action=register', {
      method: 'POST',
      body: JSON.stringify(data)
    });
  },

  /**
   * Login user
   */
  async login(credentials) {
    const data = {
      ...credentials,
      csrf_token: this.csrfToken,
      recaptcha_response: typeof grecaptcha !== 'undefined' ? grecaptcha.getResponse() : null
    };

    return await this.request('/auth.php?action=login', {
      method: 'POST',
      body: JSON.stringify(data)
    });
  },

  /**
   * Logout user
   */
  async logout() {
    return await this.request('/auth.php?action=logout', {
      method: 'POST',
      body: JSON.stringify({ csrf_token: this.csrfToken })
    });
  },

  /**
   * Get current user
   */
  async getCurrentUser() {
    return await this.request('/auth.php?action=user');
  },

  /**
   * Check if API is available
   */
  async isAvailable() {
    try {
      const response = await fetch(`${this.baseURL}/auth.php?action=csrf`, {
        credentials: 'include'
      });
      return response.ok;
    } catch {
      return false;
    }
  },

  // ========== LISTINGS API ==========

  /**
   * Get all listings
   */
  async getListings(filters = {}) {
    const params = new URLSearchParams(filters);
    return await this.request(`/listings.php?action=list&${params}`);
  },

  /**
   * Get single listing
   */
  async getListing(id) {
    return await this.request(`/listings.php?action=get&id=${id}`);
  },

  /**
   * Create new listing (Admin only)
   */
  async createListing(listingData) {
    return await this.request('/listings.php?action=create', {
      method: 'POST',
      body: JSON.stringify({
        ...listingData,
        csrf_token: this.csrfToken
      })
    });
  },

  /**
   * Update listing (Admin only)
   */
  async updateListing(id, listingData) {
    return await this.request('/listings.php?action=update', {
      method: 'POST',
      body: JSON.stringify({
        id,
        ...listingData,
        csrf_token: this.csrfToken
      })
    });
  },

  /**
   * Delete listing (Admin only)
   */
  async deleteListing(id) {
    return await this.request('/listings.php?action=delete', {
      method: 'POST',
      body: JSON.stringify({
        id,
        csrf_token: this.csrfToken
      })
    });
  },

  // ========== PURCHASE REQUESTS API ==========

  /**
   * Get all purchase requests
   */
  async getRequests(filters = {}) {
    const params = new URLSearchParams(filters);
    return await this.request(`/requests.php?action=list&${params}`);
  },

  /**
   * Get single purchase request
   */
  async getRequest(id) {
    return await this.request(`/requests.php?action=get&id=${id}`);
  },

  /**
   * Create purchase request
   */
  async createRequest(requestData) {
    return await this.request('/requests.php?action=create', {
      method: 'POST',
      body: JSON.stringify({
        ...requestData,
        csrf_token: this.csrfToken
      })
    });
  },

  /**
   * Accept purchase request (Admin only)
   */
  async acceptRequest(id) {
    return await this.request('/requests.php?action=accept', {
      method: 'POST',
      body: JSON.stringify({
        id,
        csrf_token: this.csrfToken
      })
    });
  },

  /**
   * Deny purchase request (Admin only)
   */
  async denyRequest(id, reason = '') {
    return await this.request('/requests.php?action=deny', {
      method: 'POST',
      body: JSON.stringify({
        id,
        reason,
        csrf_token: this.csrfToken
      })
    });
  },

  /**
   * Make counter-offer (Admin only)
   */
  async counterOffer(id, offeredPrice, message = '') {
    return await this.request('/requests.php?action=counter', {
      method: 'POST',
      body: JSON.stringify({
        id,
        offered_price: offeredPrice,
        message,
        csrf_token: this.csrfToken
      })
    });
  },

  /**
   * Cancel purchase request
   */
  async cancelRequest(id) {
    return await this.request('/requests.php?action=cancel', {
      method: 'POST',
      body: JSON.stringify({
        id,
        csrf_token: this.csrfToken
      })
    });
  },

  // ========== USERS API ==========

  /**
   * Get all users (Admin only)
   */
  async getUsers(filters = {}) {
    const params = new URLSearchParams(filters);
    return await this.request(`/users.php?action=list&${params}`);
  },

  /**
   * Get single user (Admin only)
   */
  async getUserDetails(id) {
    return await this.request(`/users.php?action=get&id=${id}`);
  },

  /**
   * Update user (Admin only)
   */
  async updateUser(id, userData) {
    return await this.request('/users.php?action=update', {
      method: 'POST',
      body: JSON.stringify({
        id,
        ...userData,
        csrf_token: this.csrfToken
      })
    });
  },

  /**
   * Deactivate user (Admin only)
   */
  async deactivateUser(id, reason = '') {
    return await this.request('/users.php?action=deactivate', {
      method: 'POST',
      body: JSON.stringify({
        id,
        reason,
        csrf_token: this.csrfToken
      })
    });
  },

  /**
   * Activate user (Admin only)
   */
  async activateUser(id) {
    return await this.request('/users.php?action=activate', {
      method: 'POST',
      body: JSON.stringify({
        id,
        csrf_token: this.csrfToken
      })
    });
  },

  /**
   * Delete user (Admin only)
   */
  async deleteUser(id) {
    return await this.request('/users.php?action=delete', {
      method: 'POST',
      body: JSON.stringify({
        id,
        csrf_token: this.csrfToken
      })
    });
  }
};

// Hybrid Store - Uses API if available, falls back to localStorage
const HybridStore = {
  useAPI: false,

  /**
   * Initialize - Check if API is available
   */
  async init() {
    this.useAPI = await API.isAvailable();
    if (this.useAPI) {
      await API.init();
      console.log('✓ Using MySQL database backend');
    } else {
      console.log('⚠ API not available, using localStorage fallback');
    }
    return this.useAPI;
  },

  /**
   * Register user
   */
  async register(userData) {
    if (this.useAPI) {
      return await API.register(userData);
    } else {
      // Fallback to localStorage
      return Store.registerUser(userData);
    }
  },

  /**
   * Login user
   */
  async login(credentials) {
    if (this.useAPI) {
      return await API.login(credentials);
    } else {
      // Fallback to localStorage
      const user = Store.authenticate(credentials.email, credentials.password);
      if (user) {
        Store.setCurrentUser(user, credentials.remember);
        return { success: true, user: user };
      } else {
        return { success: false, message: 'Invalid email or password' };
      }
    }
  },

  /**
   * Logout user
   */
  async logout() {
    if (this.useAPI) {
      return await API.logout();
    } else {
      Store.logout();
      return { success: true };
    }
  },

  /**
   * Get current user
   */
  async getCurrentUser() {
    if (this.useAPI) {
      const result = await API.getCurrentUser();
      return result.success ? result.user : null;
    } else {
      return Store.getCurrentUser();
    }
  },

  /**
   * Get CSRF token
   */
  getCSRFToken() {
    return this.useAPI ? API.csrfToken : sessionStorage.getItem('csrf_token');
  }
};

// Add registerUser method to Store for fallback
Store.registerUser = function(userData) {
  // Check if user exists
  if (this.findUserByEmail(userData.email)) {
    return { success: false, message: 'Unable to create account. Please try a different email.' };
  }

  // Create user object
  const user = {
    id: Date.now(),
    name: userData.name,
    email: userData.email,
    password: userData.password, // In real app, this should be hashed
    student_id: userData.student_id || null,
    role: 'user',
    created_at: new Date().toISOString()
  };

  // Add user
  this.addUser(user);
  this.setCurrentUser(user, true);

  return { success: true, user: user };
};

// Make API and HybridStore available globally
window.API = API;
window.HybridStore = HybridStore;
