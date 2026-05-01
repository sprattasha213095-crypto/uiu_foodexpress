<?php
include('../config/db.php');
$sql = "SELECT p.*, c.name AS canteen_name 
        FROM products p 
        JOIN canteens c ON p.canteen_id=c.id 
        WHERE p.available=1";
$result = $conn->query($sql);

$products = [];
while($row = $result->fetch_assoc()){
    $products[] = $row;
}
echo json_encode($products);
$conn->close();
?>
