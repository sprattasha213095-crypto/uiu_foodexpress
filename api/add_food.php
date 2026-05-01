<?php
/**
 * UIU FoodExpress - Add Food API
 * Handles adding new food items by canteen managers.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db.php'; // your DB connection

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// ---------- Validate request ----------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "invalid_request";
    exit;
}

// ---------- Collect and sanitize inputs ----------
$canteen_id  = intval($_POST['canteen_id'] ?? 0);
$name        = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$price       = floatval($_POST['price'] ?? 0);
$image       = null;

// ---------- Validate required fields ----------
if ($canteen_id <= 0 || empty($name) || $price <= 0) {
    echo "missing_fields";
    exit;
}

// ---------- Handle file upload ----------
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = dirname(__DIR__) . "/uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $imageName = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "_", $_FILES["image"]["name"]);
    $targetPath = $uploadDir . $imageName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
        $image = $imageName;
    } else {
        echo "upload_error";
        exit;
    }
}

// ---------- Insert into DB ----------
$stmt = $conn->prepare("INSERT INTO foods (canteen_id, name, description, price, image) VALUES (?, ?, ?, ?, ?)");
if (!$stmt) {
    echo "prepare_failed: " . $conn->error;
    exit;
}

$stmt->bind_param("issds", $canteen_id, $name, $description, $price, $image);
if ($stmt->execute()) {
    echo "success";
} else {
    echo "db_error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
