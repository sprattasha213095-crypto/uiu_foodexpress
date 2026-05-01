<?php
include "db.php";

// Read JSON body from JS
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
  echo json_encode(["success" => false, "message" => "No data received."]);
  exit;
}

$student_id = $data['student_id'];
$full_name = $data['full_name'];
$room_number = $data['room_number'];
$building_block = $data['building_block'];
$phone_number = $data['phone_number'];
$total_amount = $data['total_amount'];
$order_details = json_encode($data['order_details']); // store cart items as JSON

$stmt = $conn->prepare("INSERT INTO orders (student_id, full_name, room_number, building_block, phone_number, total_amount, order_details)
VALUES (?, ?, ?, ?, ?, ?, ?)");


$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $student_id, $full_name, $room_number, $building_block, $phone_number, $total_amount, $order_details);

if ($stmt->execute()) {
  echo json_encode(["success" => true, "message" => "Order placed successfully!"]);
} else {
  echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>
