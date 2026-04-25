// Honehub - Navbar Component
function renderNavbar() {
  const navbar = document.getElementById('navbar');
  if (!navbar) return;

  const user = Store.getCurrentUser();
  
  let navbarHTML = `
    <div class="navbar-brand">
      <span>🏠</span> Honehube
    </div>
    <div class="navbar-menu">
      <a href="index.html">Home</a>
      <a href="listing.html">Listings</a>
  `;

  if (user) {
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
function handleLogout(event) {
  if (event) event.preventDefault();
  Store.logout();
  window.location.href = 'index.html';
}

// Make functions available globally
window.renderNavbar = renderNavbar;
window.handleLogout = handleLogout;