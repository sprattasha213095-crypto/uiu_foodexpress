<?php
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo "No data received";
    exit;
}

$student_id = $data['student_id'] ?? '';
$full_name = $data['full_name'] ?? '';
$room_number = $data['room_number'] ?? '';
$building_block = $data['building_block'] ?? '';
$phone_number = $data['phone_number'] ?? '';
$total_amount = floatval($data['total_amount'] ?? 0);
$cart = $data['cart'] ?? [];

if (empty($cart)) {
    echo "Cart is empty";
    exit;
}

$canteen_id = intval($cart[0]['canteen_id']);
$order_details = json_encode($cart);

$stmt = $conn->prepare("
    INSERT INTO orders 
    (canteen_id, full_name, room_number, building_block, phone_number, order_details, total_amount, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
");

if (!$stmt) {
    echo "Prepare failed: " . $conn->error;
    exit;
}

$stmt->bind_param(
    "isssssd",
    $canteen_id,
    $full_name,
    $room_number,
    $building_block,
    $phone_number,
    $order_details,
    $total_amount
);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "Execute failed: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>