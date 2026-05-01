<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>UIU FoodExpress</title>
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>

  <!-- Navbar -->
<!-- Navbar -->
<nav class="navbar">
  <div class="logo">
    <img src="images/logo.png" alt="Logo">
    <div>
      <h1>UIU FoodExpress</h1>
      <p>University Canteen</p>
    </div>
  </div>

  <div class="nav-right">
    <a href="staff-login.php" class="login-btn">Staff Login</a>

    <div id="cartIcon" class="cart-icon-wrapper" title="View Cart">
      <i class="fa fa-shopping-cart"></i>
      <span id="cartCount" class="cart-count">0</span>
    </div>
  </div>
</nav>


  <!-- Hero -->
  <section class="hero">
    <h2>Delicious Food from Your Favorite <span>University Canteens</span></h2>
    <p>Order from multiple canteens, filter by price, and get it delivered to your room.</p>
  </section>

  <!-- Filter Section -->
  <section class="filter">
    <div class="filter-box">
      <label>Choose Canteen:</label>
      <select id="canteenSelect">
        <option value="">All Canteens</option>
      </select>

      <label>Price Range:</label>
      <input type="range" id="priceRange" min="50" max="500" value="500">
      <span id="priceValue">৳500</span>

      <button id="applyFilter">Apply Filter</button>
    </div>
    <button class="order-btn">Order Now</button>
  </section>

  <!-- Food List -->
  <section class="food-list" id="foodList"></section>

  <!-- 🛒 Cart Modal -->
  <div id="cartModal" class="modal">
    <div class="modal-content">
      <span id="closeCart" class="close">&times;</span>
      <h3>Your Cart</h3>
      <ul id="cartItems"></ul>
      <div class="cart-total">Total: ৳<span id="cartTotal">0</span></div>

      <form id="deliveryForm" class="delivery-form">
        <input type="text" placeholder="Student ID" required>
        <input type="text" placeholder="Full Name" required>
        <input type="text" placeholder="Room Number" required>
        <input type="text" placeholder="Building/Block" required>
        <input type="tel" placeholder="Phone Number" required>
        <button type="submit" class="confirm-btn">Confirm Order</button>
      </form>
    </div>
  </div>

<!-- ✅ Order Success Popup -->
<div id="successPopup" class="popup">
  <div class="popup-content">
    <span id="closePopup" class="popup-close">&times;</span>
    <div class="checkmark"></div>
    <h3>Order Placed Successfully!</h3>
    <p>Your order has been successfully placed. Thank you for using UIU FoodExpress 🍱</p>
  </div>
</div>



  <!-- Footer -->
  <footer>
    <p>© 2025 UIU FoodExpress | Designed for University Canteen System</p>
  </footer>

  <script src="js/main.js"></script>
</body>
</html>
