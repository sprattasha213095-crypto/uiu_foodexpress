<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Staff Registration | UIU FoodExpress</title>
  <link rel="stylesheet" href="css/staff-signup.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

  <!-- ===== Navbar ===== -->
  <nav class="navbar">
    <div class="logo">
      <img src="images/logo.png" alt="Logo">
      <div>
        <h1>UIU FoodExpress</h1>
        <p>University Canteen</p>
      </div>
    </div>
    <div class="nav-right">
      <a href="staff-login.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Login</a>
    </div>
  </nav>

  <!-- ===== Signup Section ===== -->
  <div class="signup-container">
    <div class="signup-box">
      <h2>Staff Registration</h2>
      <form method="POST" action="api/staff_register.php">
        <div class="form-group">
          <label for="full_name">Full Name</label>
          <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="Enter your email" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>

        <div class="form-group">
          <label for="confirm_password">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" required>
        </div>

        <div class="form-group">
          <label for="role">Select Role</label>
          <select name="role" id="role" required>
            <option value="">-- Select Role --</option>
            <option value="admin">Admin</option>
            <option value="canteen_manager">Canteen Manager</option>
            <option value="delivery_person">Delivery Person</option>
            <option value="user">User</option>
          </select>
        </div>

        <button type="submit" class="signup-btn">Register</button>

        <p class="login-text">
          Already have an account? <a href="staff-login.php">Sign In</a>
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
