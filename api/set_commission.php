<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $commission = floatval($_POST['commission']);
  $canteen_id = intval($_POST['canteen_id']);

  // Fetch all orders for that canteen
  $orders = $conn->query("SELECT id, total_amount FROM orders WHERE canteen_id = $canteen_id");

  while ($order = $orders->fetch_assoc()) {
    $order_id = $order['id'];
    $commission_amount = ($order['total_amount'] * $commission) / 100;

    // Insert or update commission for each order
    $stmt = $conn->prepare("
      INSERT INTO order_commissions (order_id, canteen_id, commission_rate, commission_amount)
      VALUES (?, ?, ?, ?)
      ON DUPLICATE KEY UPDATE 
        commission_rate = VALUES(commission_rate),
        commission_amount = VALUES(commission_amount)
    ");
    $stmt->bind_param("iidd", $order_id, $canteen_id, $commission, $commission_amount);
    $stmt->execute();
  }

  // Update admin earnings summary
  $earn = $conn->prepare("
    INSERT INTO admin_earnings (canteen_id, total_commission)
    VALUES (?, (SELECT SUM(commission_amount) FROM order_commissions WHERE canteen_id = ?))
    ON DUPLICATE KEY UPDATE total_commission = (SELECT SUM(commission_amount) FROM order_commissions WHERE canteen_id = ?)
  ");
  $earn->bind_param("iii", $canteen_id, $canteen_id, $canteen_id);
  $earn->execute();

  echo "success";
}
$conn->close();
?>
