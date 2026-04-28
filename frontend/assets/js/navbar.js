// Honehub - Navbar Component
async function renderNavbar() {
  const navbar = document.getElementById('navbar');
  if (!navbar) return;

  // Try to get user from HybridStore if available
  let user = null;
  if (typeof HybridStore !== 'undefined' && HybridStore.useAPI !== undefined) {
    user = await HybridStore.getCurrentUser();
  } else {
    user = Store.getCurrentUser();
  }
  
  let navbarHTML = `
    <div class="navbar-brand">
      <a href="home.html" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 8px;">
        <span>💻</span> Honehube
      </a>
    </div>
    <div class="navbar-menu">
      <a href="home.html">Home</a>
      <a href="index.html">Browse</a>
  `;

  if (user) {
    // Add dashboard link based on role
    if (user.role === 'admin') {
      navbarHTML += `<a href="admin-dashboard.html">📊 Admin Dashboard</a>`;
    } else {
      navbarHTML += `<a href="dashboard.html">📊 My Dashboard</a>`;
    }
    
    navbarHTML += `
      <a href="#" class="user-greeting">Hello, ${user.name || user.email}</a>
      <a href="#" onclick="handleLogout(event)">Logout</a>
    `;
  } else {
    navbarHTML += `
      <a href="login.html">Login</a>
      <a href="register.html">Register</a>
    `;
  }

  navbarHTML += `</div>`;
  navbar.innerHTML = navbarHTML;
}

// Logout function
async function handleLogout(event) {
  if (event) event.preventDefault();
  
  // Use HybridStore if available
  if (typeof HybridStore !== 'undefined' && HybridStore.useAPI !== undefined) {
    await HybridStore.logout();
  } else {
    Store.logout();
  }
  
  window.location.href = 'home.html';
}

// Make functions available globally
window.renderNavbar = renderNavbar;
window.handleLogout = handleLogout;