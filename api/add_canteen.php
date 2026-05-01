<?php
include "db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = trim($_POST['name']);
  $location = trim($_POST['location']);
  $contact_info = trim($_POST['contact_info']);

  if (empty($name)) {
    echo "Canteen name required.";
    exit;
  }

  $stmt = $conn->prepare("INSERT INTO canteens (name, location, contact_info) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $name, $location, $contact_info);

  if ($stmt->execute()) {
    echo "success";
  } else {
    echo "error";
  }
  $stmt->close();
}
$conn->close();
?>
