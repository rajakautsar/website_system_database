<?php
$pageTitle = 'Login - RAB System';
include __DIR__ . '/includes/head.php';
?>
<div class="login-wrapper">
  <div class="login-container">
    <div class="login-card">
      <!-- Header dengan gradient -->
      <div class="login-header">
        <div class="login-icon">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"></path>
          </svg>
        </div>
        <h2 class="login-title">Selamat Datang</h2>
        <p class="login-subtitle">Sistem Database RAB</p>
      </div>

      <!-- Alert area -->
      <div id="msg" class="mb-3"></div>

      <!-- Form -->
      <form id="loginForm" class="login-form">
        <div class="form-group">
          <label class="form-label" for="username">Username</label>
          <div class="input-icon-wrapper">
            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <input 
              type="text" 
              class="form-control input-with-icon" 
              name="username" 
              placeholder="Username" 
              required
              autocomplete="username"
            >
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div class="input-icon-wrapper">
            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            <input 
              type="password" 
              class="form-control input-with-icon" 
              name="password" 
              placeholder="Password" 
              required
              autocomplete="current-password"
            >
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-login">
          <span class="btn-text">Login</span>
          <span class="btn-arrow">→</span>
        </button>
      </form>

      <!-- Footer -->
      <div class="login-footer">
        <a href="index.php" class="login-back-link">← Kembali ke beranda</a>
      </div>
    </div>

    <!-- Decorative shapes -->
    <div class="login-decor login-decor-1"></div>
    <div class="login-decor login-decor-2"></div>
  </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const fd = new FormData(this);
  const res = await fetch('api/login.php', {method:'POST', body: fd});
  const j = await res.json();
  const msg = document.getElementById('msg');
  if (j.success) {
    window.location = j.redirect;
  } else {
    msg.innerHTML = `<div class="alert alert-danger">${j.message}</div>`;
  }
});
</script>

<?php include __DIR__ . '/includes/scripts.php'; ?>