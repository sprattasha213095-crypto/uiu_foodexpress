<?php
include "db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = intval($_POST['id']);

  $stmt = $conn->prepare("DELETE FROM canteens WHERE id = ?");
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    echo "success";
  } else {
    echo "error";
  }
  $stmt->close();
}
$conn->close();
?>
