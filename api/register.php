<?php
include('../config/db.php');

$name = $_POST['name'];
$email = $_POST['email'];
$password = md5($_POST['password']);
$role = $_POST['role'];

$sql = "INSERT INTO users (name,email,password,role) VALUES ('$name','$email','$password','$role')";
if ($conn->query($sql)) {
    echo "success";
} else {
    echo "error: ".$conn->error;
}
$conn->close();
?>
