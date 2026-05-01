<?php
include "db.php";
session_start();

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

switch ($role) {
    case 'admin':
        $sql = "SELECT * FROM orders ORDER BY created_at DESC"; break;
    case 'canteen_manager':
        $canteen = $conn->query("SELECT id FROM canteens WHERE manager_id='$user_id'")->fetch_assoc();
        $canteen_id = $canteen['id'];
        $sql = "SELECT * FROM orders WHERE canteen_id='$canteen_id' ORDER BY created_at DESC"; break;
    case 'delivery':
        $sql = "SELECT * FROM orders WHERE delivery_person_id='$user_id' ORDER BY created_at DESC"; break;
    case 'user':
        $sql = "SELECT * FROM orders WHERE user_id='$user_id' ORDER BY created_at DESC"; break;
    default:
        $sql = "";
}

$result = $conn->query($sql);
$orders = [];
while($row = $result->fetch_assoc()){
    $orders[] = $row;
}
echo json_encode($orders);
$conn->close();
?>
