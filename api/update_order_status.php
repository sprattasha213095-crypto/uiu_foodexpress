<?php
// ============================
// UIU FoodExpress - Update Order Status
// ============================
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id'] ?? 0);
    $status = trim($_POST['status'] ?? '');

    if ($order_id <= 0 || empty($status)) {
        echo "<script>alert('Missing fields!'); window.history.back();</script>";
        exit;
    }

    // ✅ Update order status
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);

    if ($stmt->execute()) {
        // ✅ If order is delivered, free delivery person
        if ($status === 'delivered') {
            $orderRes = $conn->query("SELECT assigned_delivery_id FROM orders WHERE id = $order_id");
            if ($orderRes && $orderRes->num_rows > 0) {
                $order = $orderRes->fetch_assoc();
                if (!empty($order['assigned_delivery_id'])) {
                    $conn->query("UPDATE delivery_availability SET available = 1 WHERE delivery_person_id = " . intval($order['assigned_delivery_id']));
                }
            }
        }

        echo "<script>alert('Order status updated successfully!'); window.location='../canteen-dashboard.php';</script>";
    } else {
        echo "<script>alert('Failed to update order status!'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Invalid request.'); window.history.back();</script>";
}
?>
