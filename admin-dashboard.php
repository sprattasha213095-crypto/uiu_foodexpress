<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
  header("Location: staff-login.php");
  exit;
}
include 'api/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard | UIU FoodExpress</title>
  <link rel="stylesheet" href="css/dashboard.css">
  <style>
    /* Tabs */
    .tab-container {
      margin: 30px auto;
      width: 95%;
      max-width: 1100px;
    }

    .tab-buttons {
      display: flex;
      border-bottom: 2px solid #f97316;
    }

    .tab-buttons button {
      flex: 1;
      padding: 12px;
      border: none;
      background: #f1f1f1;
      color: #333;
      font-weight: 600;
      cursor: pointer;
      transition: 0.3s;
    }

    .tab-buttons button.active {
      background: #f97316;
      color: white;
    }

    .tab-content {
      display: none;
      padding: 20px;
      background: white;
      border-radius: 0 0 10px 10px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    .tab-content.active {
      display: block;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    table, th, td {
      border: 1px solid #ddd;
      text-align: left;
    }

    th, td {
      padding: 10px;
    }

    th {
      background: #f97316;
      color: white;
    }

    form {
      margin-top: 15px;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      align-items: center;
    }

    input, select {
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    button {
      background: #f97316;
      color: white;
      border: none;
      padding: 8px 14px;
      border-radius: 6px;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      background: #e45d0b;
    }

    h2 {
      margin-top: 0;
      color: #253858;
    }
  </style>
</head>
<body>
  <nav class="navbar">
    <h1>Admin Dashboard</h1>
    <div>
      Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
      <a href="api/logout.php" class="logout-btn">Logout</a>
    </div>
  </nav>

  <div class="tab-container">
    <div class="tab-buttons">
      <button class="tab-btn active" data-tab="canteens">Canteen Management</button>
      <button class="tab-btn" data-tab="reports">Orders & Earnings</button>
    </div>

    <!-- TAB 1: Add/Delete Canteens -->
    <div id="canteens" class="tab-content active">
      <h2>Add or Delete Canteen</h2>

      <form id="addCanteenForm">
        <input type="text" name="name" placeholder="Canteen Name" required>
        <input type="text" name="location" placeholder="Location">
        <input type="text" name="contact_info" placeholder="Contact Info">
        <button type="submit">Add Canteen</button>
      </form>

      <table>
        <thead>
          <tr><th>ID</th><th>Name</th><th>Location</th><th>Contact</th><th>Actions</th></tr>
        </thead>
        <tbody id="canteenTable">
          <?php
            $result = $conn->query("SELECT * FROM canteens ORDER BY id DESC");
            while ($row = $result->fetch_assoc()) {
              echo "<tr>
                      <td>{$row['id']}</td>
                      <td>{$row['name']}</td>
                      <td>{$row['location']}</td>
                      <td>{$row['contact_info']}</td>
                      <td><button class='delete-btn' data-id='{$row['id']}'>Delete</button></td>
                    </tr>";
            }
          ?>
        </tbody>
      </table>
    </div>

    <!-- TAB 2: Orders, Commissions & Earnings -->
    <div id="reports" class="tab-content">
      <h2>Orders & Commissions</h2>

      <form id="commissionForm">
        <select name="canteen_id" required>
          <option value="">Select Canteen</option>
          <?php
            $result = $conn->query("SELECT id, name FROM canteens");
            while ($row = $result->fetch_assoc()) {
              echo "<option value='{$row['id']}'>{$row['name']}</option>";
            }
          ?>
        </select>
        <input type="number" name="commission" placeholder="Commission %" step="0.1" required>
        <button type="submit">Apply</button>
      </form>

      <h3>Recent Orders</h3>
      <table>
        <thead><tr><th>Order ID</th><th>Canteen</th><th>Total</th><th>Commission</th><th>Date</th></tr></thead>
        <tbody>
          <?php
            $sql = "
              SELECT 
                o.id AS order_id,
                c.name AS canteen_name,
                o.total_amount,
                COALESCE(oc.commission_amount, 0) AS commission_amount,
                o.order_time
              FROM orders o
              LEFT JOIN canteens c ON c.id = JSON_UNQUOTE(JSON_EXTRACT(o.order_details, '$[0].canteen_id'))
              LEFT JOIN order_commissions oc ON oc.order_id = o.id
              ORDER BY o.order_time DESC
              LIMIT 10
            ";
            $orders = $conn->query($sql);
            while ($row = $orders->fetch_assoc()) {
              echo "<tr>
                      <td>#{$row['order_id']}</td>
                      <td>{$row['canteen_name']}</td>
                      <td>৳{$row['total_amount']}</td>
                      <td>৳{$row['commission_amount']}</td>
                      <td>{$row['order_time']}</td>
                    </tr>";
            }
          ?>
        </tbody>
      </table>

      <h3>Total Earnings</h3>
      <table>
        <thead><tr><th>Canteen</th><th>Total Commission (৳)</th></tr></thead>
        <tbody>
          <?php
            $earnings = $conn->query("
              SELECT 
                c.name AS canteen_name,
                ROUND(SUM(oc.commission_amount), 2) AS total_commission
              FROM order_commissions oc
              INNER JOIN canteens c ON oc.canteen_id = c.id
              GROUP BY oc.canteen_id
              ORDER BY c.name ASC
            ");
            while ($row = $earnings->fetch_assoc()) {
              echo "<tr><td>{$row['canteen_name']}</td><td>৳{$row['total_commission']}</td></tr>";
            }
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    // ===== Tabs Switch =====
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        tabButtons.forEach(b => b.classList.remove('active'));
        tabContents.forEach(tc => tc.classList.remove('active'));

        btn.classList.add('active');
        document.getElementById(btn.dataset.tab).classList.add('active');
      });
    });

    // ===== Add Canteen =====
    document.getElementById('addCanteenForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const res = await fetch('api/add_canteen.php', { method: 'POST', body: formData });
      const text = await res.text();
      if (text.includes('success')) location.reload();
      else alert('Error adding canteen');
    });

    // ===== Delete Canteen =====
    document.querySelectorAll('.delete-btn').forEach(btn => {
      btn.addEventListener('click', async () => {
        if (!confirm('Delete this canteen?')) return;
        const formData = new FormData();
        formData.append('id', btn.dataset.id);
        const res = await fetch('api/delete_canteen.php', { method: 'POST', body: formData });
        const text = await res.text();
        if (text.includes('success')) location.reload();
        else alert('Error deleting canteen');
      });
    });

    // ===== Set Commission =====
    document.getElementById('commissionForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const res = await fetch('api/set_commission.php', { method: 'POST', body: formData });
      const text = await res.text();
      if (text.includes('success')) alert('Commission set successfully!');
      else alert('Error setting commission');
    });
  </script>
</body>
</html>
