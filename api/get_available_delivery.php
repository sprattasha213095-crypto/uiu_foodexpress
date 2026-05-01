<?php
include('../config/db.php');
$sql = "SELECT id, name FROM users WHERE role='delivery' AND availability='Available'";
$result = $conn->query($sql);

$people = [];
while($row = $result->fetch_assoc()){
    $people[] = $row;
}
echo json_encode($people);
$conn->close();
?>
