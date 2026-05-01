<?php
session_start();
include "api/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'canteen_manager') {
  header("Location: staff-login.php");
  exit;
}

$canteen_id = $_SESSION['canteen_id'] ?? null;
if (!$canteen_id) {
  echo "<script>alert('No canteen assigned to this account!'); window.location='staff-login.php';</script>";
  exit;
}

// Fetch recent orders
$orderQuery = $conn->prepare("
  SELECT o.id, o.order_details, o.status, o.created_at, o.assigned_delivery_id,
         d.full_name AS delivery_person
  FROM orders o
  LEFT JOIN staff_users d ON o.assigned_delivery_id = d.id
  WHERE o.canteen_id = ?
  ORDER BY o.created_at DESC
");
$orderQuery->bind_param("i", $canteen_id);
$orderQuery->execute();
$orderResult = $orderQuery->get_result();

// Delivery persons
$deliveryResult = $conn->query("
  SELECT s.id, s.full_name, da.available
  FROM staff_users s
  JOIN delivery_availability da ON s.id = da.delivery_person_id
  WHERE s.role = 'delivery_person'
  ORDER BY da.available DESC
");

// Pending orders for assigning in delivery tab
$pendingOrders = $conn->prepare("SELECT id FROM orders WHERE canteen_id = ? AND status != 'delivered'");
$pendingOrders->bind_param("i", $canteen_id);
$pendingOrders->execute();
$pendingResult = $pendingOrders->get_result();
$pendingList = [];
while ($p = $pendingResult->fetch_assoc()) $pendingList[] = $p['id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Canteen Manager | UIU FoodExpress</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<style>
body {
  font-family: "Poppins", sans-serif;
  margin: 0;
  background: #f7f7f9;
  color: #333;
}

/* ===== Navbar ===== */
.navbar {
  background: #f97316;
  color: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 40px;
  box-shadow: 0 3px 6px rgba(0,0,0,0.15);
}
.logo { display: flex; align-items: center; gap: 10px; }
.logo img { width: 45px; height: 45px; border-radius: 50%; }
.nav-right button {
  background: #253858;
  color: white;
  border: none;
  padding: 8px 18px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: 0.3s;
}
.nav-right button:hover { background: #1b2d4f; }

/* ===== Tabs ===== */
.tab-bar-wrapper { background: #f7f7f9; padding: 20px 0; display: flex; justify-content: center; }
.tab-bar {
  display: flex; background: #ffffff; border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.08); overflow: hidden;
  width: 75%; justify-content: center;
}
.tab-bar button {
  flex: 1; padding: 14px 0; background: #ffffff; color: #333;
  border: none; font-weight: 600; cursor: pointer;
  transition: 0.3s; border-right: 1px solid #eee;
}
.tab-bar button:last-child { border-right: none; }
.tab-bar button:hover { background: #f3f3f3; }
.tab-bar button.active {
  background: #e5e7eb; color: #000;
  box-shadow: inset 0 -3px 0 #f97316;
}

/* ===== Content ===== */
.content {
  padding: 40px 60px; max-width: 1200px; margin: 0 auto;
  animation: fadeIn 0.3s ease; background: #ffffff;
  border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px);} to { opacity: 1; transform: translateY(0);} }
h2 { color: #f97316; border-left: 5px solid #f97316; padding-left: 10px; font-size: 22px; }

/* ===== Form & Tables ===== */
#addFoodForm { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 20px; }
#addFoodForm input, #addFoodForm button {
  padding: 10px 12px; border-radius: 8px; border: 1px solid #ccc; font-size: 14px;
}
#addFoodForm button {
  background: #f97316; color: white; border: none; cursor: pointer;
  font-weight: 600; transition: 0.3s;
}
#addFoodForm button:hover { background: #d75c0d; }

table {
  width: 100%; border-collapse: collapse; margin-top: 25px;
  border-radius: 10px; overflow: hidden;
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}
th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: center; }
th { background: #f97316; color: white; }
select, button { border-radius: 6px; padding: 6px 10px; }
.assign-btn, .update-btn {
  background: #253858; color: white; border: none; cursor: pointer;
  font-weight: 600; transition: 0.3s;
}
.assign-btn:hover, .update-btn:hover { background: #1a2d4a; }

/* ===== Delivery Tab ===== */
.available { color: #22c55e; font-weight: 600; }
.unavailable { color: #ef4444; font-weight: 600; }

/* ===== Popup ===== */
.popup {
  display: none; position: fixed; top: 20px; right: 20px;
  background: #22c55e; color: white; padding: 12px 20px;
  border-radius: 8px; box-shadow: 0 3px 8px rgba(0,0,0,0.2);
}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
  <div class="logo">
    <img src="images/logo.png" alt="Logo">
    <div><h3>Canteen Manager Dashboard</h3></div>
  </div>
  <div class="nav-right">
    <form action="api/logout.php" method="POST">
      <button type="submit"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
    </form>
  </div>
</nav>

<!-- Tabs -->
<div class="tab-bar-wrapper">
  <div class="tab-bar">
    <button class="tab active" data-target="addfood"><i class="fa-solid fa-plus"></i> Add Food</button>
    <button class="tab" data-target="orders"><i class="fa-solid fa-list"></i> Recent Orders</button>
    <button class="tab" data-target="deliveries"><i class="fa-solid fa-motorcycle"></i> Available Deliveries</button>
  </div>
</div>

<!-- Content -->
<div class="content">

  <!-- Add Food -->
  <section id="addfood" class="tab-content active">
    <h2>Add New Food</h2>
    <form id="addFoodForm" enctype="multipart/form-data">
      <input type="hidden" name="canteen_id" value="<?php echo $canteen_id; ?>">
      <input type="text" name="name" placeholder="Food Name" required>
      <input type="text" name="description" placeholder="Description">
      <input type="number" name="price" placeholder="Price" required>
      <input type="file" name="image" accept="image/*">
      <button type="submit"><i class="fa-solid fa-plus"></i> Add Food</button>
    </form>
  </section>

  <!-- Recent Orders -->
  <section id="orders" class="tab-content" style="display:none;">
    <h2>Recent Orders</h2>
    <table>
      <tr><th>ID</th><th>Food</th><th>Status</th><th>Delivery Person</th><th>Assign</th><th>Update</th></tr>
      <?php while ($order = $orderResult->fetch_assoc()):
        $details = json_decode($order['order_details'], true);
      ?>
      <tr>
        <td><?php echo $order['id']; ?></td>
        <td><?php echo htmlspecialchars($details[0]['name'] ?? 'N/A'); ?></td>
        <td><?php echo ucfirst($order['status']); ?></td>
        <td><?php echo $order['delivery_person'] ?? 'Not Assigned'; ?></td>
        <td>
          <form method="POST" action="api/assign_delivery.php">
            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
            <select name="delivery_id" required>
              <option value="">Select</option>
              <?php mysqli_data_seek($deliveryResult, 0);
              while ($d = $deliveryResult->fetch_assoc()): ?>
                <option value="<?php echo $d['id']; ?>">
                  <?php echo $d['full_name']; ?> (<?php echo $d['available'] ? 'Available' : 'Busy'; ?>)
                </option>
              <?php endwhile; ?>
            </select>
            <button type="submit" class="assign-btn">Assign</button>
          </form>
        </td>
        <td>
          <form method="POST" action="api/update_order_status.php">
            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
            <select name="status">
              <option value="order taken">Order Taken</option>
              <option value="preparing">Preparing</option>
              <option value="out for delivery">Out for Delivery</option>
              <option value="delivered">Delivered</option>
            </select>
            <button type="submit" class="update-btn">Update</button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  </section>

  <!-- Available Deliveries -->
  <section id="deliveries" class="tab-content" style="display:none;">
    <h2>Available Delivery Persons</h2>
    <table>
      <tr><th>ID</th><th>Name</th><th>Status</th><th>Assign Order</th></tr>
      <?php mysqli_data_seek($deliveryResult, 0);
      while ($d = $deliveryResult->fetch_assoc()): ?>
      <tr>
        <td><?php echo $d['id']; ?></td>
        <td><?php echo $d['full_name']; ?></td>
        <td class="<?php echo $d['available'] ? 'available' : 'unavailable'; ?>">
          <?php echo $d['available'] ? 'Available' : 'Unavailable'; ?>
        </td>
        <td>
          <?php if ($d['available']): ?>
          <form method="POST" action="api/assign_delivery.php">
            <input type="hidden" name="delivery_id" value="<?php echo $d['id']; ?>">
            <select name="order_id" required>
              <option value="">Select Order</option>
              <?php foreach ($pendingList as $orderId): ?>
              <option value="<?php echo $orderId; ?>">Order #<?php echo $orderId; ?></option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="assign-btn">Assign</button>
          </form>
          <?php else: ?>
          <span style="color:#888;">—</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  </section>
</div>

<div id="popup" class="popup">✅ Food added successfully!</div>

<script>
document.querySelectorAll(".tab").forEach(btn => {
  btn.addEventListener("click", () => {
    document.querySelectorAll(".tab").forEach(b => b.classList.remove("active"));
    document.querySelectorAll(".tab-content").forEach(c => c.style.display = "none");
    btn.classList.add("active");
    document.getElementById(btn.dataset.target).style.display = "block";
  });
});

const form = document.getElementById("addFoodForm");
form.addEventListener("submit", async e => {
  e.preventDefault();
  const res = await fetch("api/add_food.php", { method: "POST", body: new FormData(form) });
  const text = await res.text();
  if (text.includes("success")) {
    const popup = document.getElementById("popup");
    popup.style.display = "block";
    setTimeout(() => { popup.style.display = "none"; location.reload(); }, 2000);
  } else alert("Error: " + text);
});
</script>
</body>
</html>
