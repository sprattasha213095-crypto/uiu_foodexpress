<?php
include "db.php"; // include database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Check password match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check email exists
    $check = $conn->prepare("SELECT * FROM staff_users WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered!'); window.history.back();</script>";
        exit;
    }

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO staff_users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $full_name, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Registration Successful! You can now sign in.'); window.location.href='../staff-login.php';</script>";
    } else {
        echo "<script>alert('Error during registration. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
