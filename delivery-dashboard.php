<?php
session_start();
include "api/db.php";

// Restrict access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'delivery_person') {
    header("Location: staff-login.php");
    exit();
}

$delivery_id = $_SESSION['user_id'];

// Handle availability toggle (button version)
if (isset($_POST['toggle_status'])) {
    $availability = $_POST['availability'] === 'available' ? 1 : 0;

    // Update or insert availability
    $check = $conn->query("SELECT * FROM delivery_availability WHERE delivery_person_id = $delivery_id");
    if ($check->num_rows > 0) {
        $conn->query("UPDATE delivery_availability SET available = $availability WHERE delivery_person_id = $delivery_id");
    } else {
        $conn->query("INSERT INTO delivery_availability (delivery_person_id, available) VALUES ($delivery_id, $availability)");
    }

    header("Location: delivery-dashboard.php");
    exit();
}

// Handle order status updates
if (isset($_POST['update_status']) && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];

    $conn->query("UPDATE orders SET status = '$status' WHERE id = $order_id AND assigned_delivery_id = $delivery_id");

    // When delivered, set availability = 1 automatically
    if ($status === 'delivered') {
        $conn->query("UPDATE delivery_availability SET available = 1 WHERE delivery_person_id = $delivery_id");
    }

    header("Location: delivery-dashboard.php");
    exit();
}

// Fetch assigned orders
$query = "
SELECT o.id, o.full_name, o.room_number, o.building_block, o.phone_number, 
       o.total_amount, o.status, c.name AS canteen_name
FROM orders o
LEFT JOIN canteens c ON o.canteen_id = c.id
WHERE o.assigned_delivery_id = $delivery_id
ORDER BY o.created_at DESC
";
$orders = $conn->query($query);

// Get availability
$availRes = $conn->query("SELECT available FROM delivery_availability WHERE delivery_person_id = $delivery_id");
$available = ($availRes && $availRes->num_rows > 0) ? $availRes->fetch_assoc()['available'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Delivery Dashboard | UIU FoodExpress</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root {
      --primary: #f26b1d;
      --dark: #2f2f2f;
      --light: #fffdf8;
      --success: #28a745;
      --danger: #dc3545;
      --shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    body {
      font-family: "Poppins", sans-serif;
      background: var(--light);
      color: var(--dark);
      margin: 0;
    }

    /* Navbar */
    .navbar {
      background: var(--dark);
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 40px;
      box-shadow: var(--shadow);
      position: sticky;
      top: 0;
      z-index: 10;
    }

    .navbar h1 {
      font-size: 22px;
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 0;
    }

    .navbar h1 i { color: var(--primary); }

    .navbar .right {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    /* Availability button */
    .availability-btn {
      border: none;
      border-radius: 6px;
      padding: 8px 16px;
      font-weight: 600;
      cursor: pointer;
      transition: 0.3s;
      color: white;
    }

    .available-btn {
      background: var(--success);
      box-shadow: 0 0 10px rgba(40, 167, 69, 0.6);
    }
    .available-btn:hover {
      background: #218838;
    }

    .unavailable-btn {
      background: var(--danger);
      box-shadow: 0 0 10px rgba(220, 53, 69, 0.6);
    }
    .unavailable-btn:hover {
      background: #c82333;
    }

    /* Logout */
    .logout-btn {
      background: var(--primary);
      color: white;
      padding: 8px 16px;
      border-radius: 6px;
      font-weight: 600;
      text-decoration: none;
      transition: 0.3s;
    }
    .logout-btn:hover {
      background: #ff8538;
      box-shadow: 0 0 8px rgba(255, 133, 56, 0.6);
    }

    /* Container */
    .container {
      width: 92%;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: var(--shadow);
    }

    h2 {
      color: var(--primary);
      text-align: center;
      margin-bottom: 25px;
    }

    /* Table */
    table {
      width: 100%;
      border-collapse: collapse;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: var(--shadow);
    }

    th, td {
      padding: 14px;
      text-align: center;
      border-bottom: 1px solid #f2f2f2;
    }

    th {
      background: var(--primary);
      color: white;
      text-transform: uppercase;
      font-size: 13px;
    }

    tr:hover { background: #fff4eb; }

    select {
      padding: 6px 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }

    .update-btn {
      background: var(--primary);
      color: white;
      border: none;
      padding: 7px 14px;
      border-radius: 6px;
      cursor: pointer;
      transition: 0.3s;
      font-weight: 600;
    }
    .update-btn:hover { background: #d95b12; }

    footer {
      background: var(--dark);
      color: white;
      text-align: center;
      padding: 15px;
      margin-top: 50px;
      font-size: 14px;
    }
  </style>
</head>
<body>

  <nav class="navbar">
    <h1><i class="fa-solid fa-motorcycle"></i> Delivery Dashboard</h1>
    <div class="right">
      <form method="POST" style="display:inline;">
        <input type="hidden" name="toggle_status" value="1">
        <input type="hidden" name="availability" value="<?php echo $available ? 'unavailable' : 'available'; ?>">
        <button type="submit" 
          class="availability-btn <?php echo $available ? 'available-btn' : 'unavailable-btn'; ?>">
          <?php echo $available ? '🟢 Available' : '🔴 Unavailable'; ?>
        </button>
      </form>

      <a href="api/logout.php" class="logout-btn">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
      </a>
    </div>
  </nav>

  <div class="container">
    <h2>Assigned Orders</h2>
    <table>
      <tr>
        <th>ID</th>
        <th>Customer</th>
        <th>Room</th>
        <th>Building</th>
        <th>Phone</th>
        <th>Canteen</th>
        <th>Total</th>
        <th>Status</th>
        <th>Update</th>
      </tr>
      <?php if ($orders->num_rows > 0): ?>
        <?php while ($row = $orders->fetch_assoc()): ?>
          <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
            <td><?php echo htmlspecialchars($row['room_number']); ?></td>
            <td><?php echo htmlspecialchars($row['building_block']); ?></td>
            <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
            <td><?php echo htmlspecialchars($row['canteen_name']); ?></td>
            <td>৳<?php echo number_format($row['total_amount'], 2); ?></td>
            <td><?php echo ucfirst($row['status']); ?></td>
            <td>
              <form method="POST" style="display:flex;gap:6px;justify-content:center;">
                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                <select name="status" required>
                  <option value="">Select</option>
                  <option value="collected">Collected</option>
                  <option value="not delivered">Not Delivered</option>
                  <option value="delivered">Delivered</option>
                </select>
                <button type="submit" name="update_status" class="update-btn">Update</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="9">No orders assigned yet.</td></tr>
      <?php endif; ?>
    </table>
  </div>

  <footer>
    <p>© 2025 UIU FoodExpress | Delivery Management System</p>
  </footer>

</body>
</html>
