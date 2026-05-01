<?php
include "db.php";

// Get filter inputs from query string
$canteen = isset($_GET['canteen']) ? $_GET['canteen'] : '';
$price = isset($_GET['price']) ? $_GET['price'] : '';

// Build SQL query
$query = "
  SELECT f.*, c.name AS canteen_name
  FROM foods f
  JOIN canteens c ON f.canteen_id = c.id
  WHERE f.available = 'Yes'
";

// Apply filters if selected
if (!empty($canteen)) {
  $query .= " AND c.name = '" . $conn->real_escape_string($canteen) . "'";
}

if (!empty($price)) {
  $query .= " AND f.price <= " . (float)$price;
}

$query .= " ORDER BY c.name ASC, f.price ASC";

$result = $conn->query($query);

$foods = [];
while ($row = $result->fetch_assoc()) {
  $foods[] = $row;
}

// Return JSON
echo json_encode($foods);
?>
