<?php
include "db.php";
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Allow only POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (!$input || !isset($input['items']) || count($input['items']) === 0) {
    http_response_code(400);
    echo "Invalid order data!";
    exit;
}

// Extract order info
$student_id     = trim($input['student_id'] ?? '');
$full_name      = trim($input['full_name'] ?? '');
$room_number    = trim($input['room_number'] ?? '');
$building_block = trim($input['building_block'] ?? '');
$phone_number   = trim($input['phone_number'] ?? '');
$total_amount   = floatval($input['total_amount'] ?? 0);
$order_items    = $input['items'];

// Validation
if (empty($student_id) || empty($full_name) || empty($room_number) || empty($building_block) || empty($phone_number)) {
    http_response_code(400);
    echo "Missing required customer details.";
    exit;
}

// Get canteen_id from first item
$canteen_id = null;
foreach ($order_items as $item) {
    if (isset($item['canteen_id']) && $item['canteen_id'] !== null) {
        $canteen_id = intval($item['canteen_id']);
        break;
    }
}

if (!$canteen_id) {
    http_response_code(400);
    echo "Canteen ID missing in order items.";
    exit;
}

// Convert cart to JSON
$order_details = json_encode($order_items, JSON_UNESCAPED_UNICODE);

// Prepare and execute
$sql = "
INSERT INTO orders 
(student_id, full_name, room_number, building_block, phone_number, total_amount, canteen_id, order_details, status, created_at, order_time)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "SQL Prepare Failed: " . $conn->error;
    exit;
}

// ✅ Corrected bind_param types: "sssssdis"
$stmt->bind_param(
    "sssssdis",
    $student_id,
    $full_name,
    $room_number,
    $building_block,
    $phone_number,
    $total_amount,
    $canteen_id,
    $order_details
);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "SQL Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
