<?php
include('../config/db.php');
session_start();

$user_id = $_SESSION['user_id'];
$order_id = $_POST['order_id'];
$rating = $_POST['rating'];
$comment = $_POST['comment'];

$sql = "INSERT INTO reviews (order_id,user_id,rating,comment)
        VALUES ('$order_id','$user_id','$rating','$comment')";
echo $conn->query($sql) ? "success" : "error: ".$conn->error;
$conn->close();
?>
