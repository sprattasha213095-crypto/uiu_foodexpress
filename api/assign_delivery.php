<?php
// ============================
// UIU FoodExpress - Assign Delivery
// ============================
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id'] ?? 0);
    $delivery_id = intval($_POST['delivery_id'] ?? 0);

    if ($order_id <= 0 || $delivery_id <= 0) {
        echo "<script>alert('Missing fields!'); window.history.back();</script>";
        exit;
    }

    // ✅ Update order assignment
    $stmt = $conn->prepare("UPDATE orders SET assigned_delivery_id = ?, status = 'out for delivery' WHERE id = ?");
    $stmt->bind_param("ii", $delivery_id, $order_id);

    if ($stmt->execute()) {
        // ✅ Mark delivery person as busy
        $conn->query("UPDATE delivery_availability SET available = 0 WHERE delivery_person_id = $delivery_id");
        echo "<script>alert('Delivery assigned successfully!'); window.location='../canteen-dashboard.php';</script>";
    } else {
        echo "<script>alert('Failed to assign delivery!'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Invalid request.'); window.history.back();</script>";
}
?>
