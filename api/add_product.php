<?php
include('../config/db.php');

$name = $_POST['name'];
$desc = $_POST['description'];
$price = $_POST['price'];
$canteen_id = $_POST['canteen_id'];

$imagePath = "";
if (!empty($_FILES['image']['name'])) {
    $target = "../uploads/food/" . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], $target);
    $imagePath = "uploads/food/" . basename($_FILES['image']['name']);
}

$sql = "INSERT INTO products (name, description, price, image, canteen_id)
        VALUES ('$name','$desc','$price','$imagePath','$canteen_id')";
echo $conn->query($sql) ? "success" : "error: ".$conn->error;
$conn->close();
?>
