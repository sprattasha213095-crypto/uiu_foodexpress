<?php
session_start();
include "db.php";  // Database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if (empty($email) || empty($password) || empty($role)) {
        echo "<script>alert('Please fill in all fields.'); window.history.back();</script>";
        exit;
    }

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT * FROM staff_users WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();  // ✅ Correct variable name

        // Verify password
        if (password_verify($password, $user['password'])) {
            // ✅ Set sessions
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['full_name'];

            // ✅ Only for canteen managers
            if ($user['role'] === 'canteen_manager' && isset($user['canteen_id'])) {
                $_SESSION['canteen_id'] = $user['canteen_id'];
            }

            // ✅ Redirect based on role
            switch ($user['role']) {
                case 'admin':
                    header("Location: ../admin-dashboard.php");
                    break;
                case 'canteen_manager':
                    header("Location: ../canteen-dashboard.php");
                    break;
                case 'delivery_person':
                    header("Location: ../delivery-dashboard.php");
                    break;
                default:
                    header("Location: ../user-dashboard.php");
                    break;
            }
            exit;
        } else {
            echo "<script>alert('Incorrect password!'); window.history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('No account found for this email and role.'); window.history.back();</script>";
        exit;
    }
}
?>
