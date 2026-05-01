<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Staff Login | UIU FoodExpress</title>
  <link rel="stylesheet" href="css/staff-login.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>

  <!-- ===== Navbar ===== -->
  <nav class="navbar">
    <div class="logo">
      <img src="images/logo.png" alt="UIU FoodExpress Logo" />
      <div>
        <h1>UIU FoodExpress</h1>
        <p>University Canteen</p>
      </div>
    </div>
    <div class="nav-right">
      <a href="index.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>
    </div>
  </nav>

  <!-- ===== Login Section ===== -->
  <div class="login-container">
    <div class="login-box">
      <h2>Staff Login</h2>
      <form method="POST" action="api/staff_login.php" id="loginForm">
        <div class="form-group">
          <label for="role">Select Role</label>
          <select name="role" id="role" required>
            <option value="">-- Select Role --</option>
            <option value="admin">Admin</option>
            <option value="canteen_manager">Canteen Manager</option>
            <option value="delivery_person">Delivery Person</option>
            <option value="User">User</option>
          </select>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" name="email" id="email" placeholder="Enter your email" required />
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" name="password" id="password" placeholder="Enter your password" required />
        </div>

        <button type="submit" class="login-btn">Sign In</button>

        <p class="signup-text">
          Don’t have an account? <a href="staff-signup.php">Sign Up</a>
        </p>
      </form>
    </div>
  </div>

  <!-- ===== Footer ===== -->
  <footer>
    <p>© 2025 UIU FoodExpress | All Rights Reserved</p>
  </footer>

</body>
</html>
