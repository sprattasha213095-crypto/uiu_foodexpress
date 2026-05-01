<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  header("Location: index.php");
  exit;
}

$username = $_SESSION['name'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard | UIU FoodExpress</title>
  <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
  <nav class="navbar">
    <h1>User Dashboard</h1>
    <div>
     Welcome, <?php echo htmlspecialchars($username); ?>
      <a href="api/logout.php" class="logout-btn">Logout</a>
    </div>
  </nav>

  <section class="content">
    <h2>My Orders</h2>
    <table>
      <thead>
        <tr><th>Order ID</th><th>Item</th><th>Amount</th><th>Status</th><th>Date</th></tr>
      </thead>
      <tbody>
        <tr><td>#O301</td><td>Chicken Biryani</td><td>৳180</td><td><span class="status pending">Pending</span></td><td>2025-09-22</td></tr>
        <tr><td>#O302</td><td>Veggie Pizza</td><td>৳220</td><td><span class="status delivered">Delivered</span></td><td>2025-09-21</td></tr>
      </tbody>
    </table>
  </section>
</body>
</html>
