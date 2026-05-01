<?php
include 'db.php';
header('Content-Type: application/json');

$canteen_id = isset($_GET['canteen_id']) ? intval($_GET['canteen_id']) : 0;
$price = isset($_GET['price']) ? floatval($_GET['price']) : 500;

$sql = "
SELECT 
  f.id,
  f.canteen_id,
  f.name,
  f.description,
  f.price,
  f.image,
  c.name AS canteen_name
FROM food_items f
LEFT JOIN canteens c ON f.canteen_id = c.id
WHERE f.price <= ?
";

if ($canteen_id > 0) {
  $sql .= " AND f.canteen_id = ?";
}

$sql .= " ORDER BY f.id DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
  echo json_encode(["error" => "Prepare failed: " . $conn->error]);
  exit;
}

if ($canteen_id > 0) {
  $stmt->bind_param("di", $price, $canteen_id);
} else {
  $stmt->bind_param("d", $price);
}

$stmt->execute();
$result = $stmt->get_result();

$foods = [];

while ($row = $result->fetch_assoc()) {
  $foods[] = $row;
}

echo json_encode($foods);
?>