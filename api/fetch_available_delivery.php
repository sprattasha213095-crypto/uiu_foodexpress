<?php
include 'db.php';
$result = $conn->query("SELECT id, full_name, email, phone FROM delivery_persons WHERE is_available = 1");
$people = [];
while ($row = $result->fetch_assoc()) $people[] = $row;
echo json_encode($people);
?>
