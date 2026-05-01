<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uiu_foodexpress";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("<h3 style='color:red;'>❌ Connection failed: " . $conn->connect_error . "</h3>");
} else {
  echo "<h3 style='color:green;'>✅ Successfully connected to the UIU FoodExpress database!</h3>";
}

$conn->close();
?>
