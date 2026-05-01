<?php
include('../config/db.php');
session_start();

$user_id = $_SESSION['user_id'];
$status = $_POST['status']; // 'Available' or 'Unavailable'

$sql = "UPDATE users SET availability='$status' WHERE id='$user_id' AND role='delivery'";
echo $conn->query($sql) ? "success" : "error";
$conn->close();
?>
