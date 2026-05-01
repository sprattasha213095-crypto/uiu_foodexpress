<?php
include('../config/db.php');
session_start();

$email = trim($_POST['email']);
$password = trim($_POST['password']);
$role = trim($_POST['role']);

if (empty($email) || empty($password) || empty($role)) {
    echo json_encode(['status'=>'error','message'=>'Please fill all fields']);
    exit;
}

$sql = "SELECT * FROM users WHERE email='$email' AND role='$role' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (md5($password) === $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        switch ($user['role']) {
            case 'admin':
                $redirect = '../admin-dashboard.html'; break;
            case 'canteen_manager':
                $redirect = '../canteen-dashboard.html'; break;
            case 'delivery':
                $redirect = '../delivery-dashboard.html'; break;
            case 'user':
                $redirect = '../user-dashboard.html'; break;
        }
        echo json_encode(['status'=>'success','redirect'=>$redirect]);
    } else {
        echo json_encode(['status'=>'error','message'=>'Incorrect password']);
    }
} else {
    echo json_encode(['status'=>'error','message'=>'No user found']);
}
$conn->close();
?>
