<?php
include "db.php";

$sql = "SELECT id, name FROM canteens ORDER BY name ASC";
$result = $conn->query($sql);

$canteens = [];
while ($row = $result->fetch_assoc()) {
  $canteens[] = $row;
}

echo json_encode($canteens);
?>
