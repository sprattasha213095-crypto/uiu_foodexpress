<?php
include('../config/db.php');
session_start();

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role == 'user') {
    $sql = "SELECT * FROM reviews WHERE user_id='$user_id' ORDER BY created_at DESC";
} else {
    $sql = "SELECT r.*, u.name AS user_name, p.name AS product_name
            FROM reviews r
            JOIN users u ON r.user_id=u.id
            JOIN orders o ON r.order_id=o.id
            JOIN order_items oi ON o.id=oi.order_id
            JOIN products p ON oi.product_id=p.id";
}

$result = $conn->query($sql);
$reviews = [];
while($row = $result->fetch_assoc()){
    $reviews[] = $row;
}
echo json_encode($reviews);
$conn->close();
?>
