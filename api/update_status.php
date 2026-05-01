<?php
include('../config/db.php');

$order_id = $_POST['order_id'];
$status = $_POST['status'];

$sql = "UPDATE orders SET status='$status' WHERE id='$order_id'";
echo $conn->query($sql) ? "success" : "error: ".$conn->error;
$conn->close();
?>
