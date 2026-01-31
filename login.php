<?php
session_start();
include("connection.php");

$error = "";

if($_POST){
    $email = $_POST['useremail'];
    $password = $_POST['userpassword'];

    $result = $database->query("SELECT * FROM webuser WHERE email='$email'");

    if($result->num_rows == 1){
        $utype = $result->fetch_assoc()['usertype'];

        if($utype == 'a'){
            $result = $database->query("SELECT * FROM admin WHERE aemail='$email'");
            $user_data = $result->fetch_assoc();
            if(password_verify($password, $user_data['apassword'])){
                $_SESSION['user'] = $email;
                $_SESSION['usertype'] = 'a';
                header('location: admin/index.php');
            } else {
                $error = "Invalid email or password";
            }
        } elseif($utype == 'd'){
            $result = $database->query("SELECT * FROM doctor WHERE docemail='$email'");
            $user_data = $result->fetch_assoc();
            if(password_verify($password, $user_data['docpassword'])){
                $_SESSION['user'] = $email;
                $_SESSION['usertype'] = 'd';
                header('location: doctor/index.php');
            } else {
                $error = "Invalid email or password";
            }
        } elseif($utype == 'p'){
            $result = $database->query("SELECT * FROM patient WHERE pemail='$email'");
            $user_data = $result->fetch_assoc();
            if(password_verify($password, $user_data['ppassword'])){
                $_SESSION['user'] = $email;
                $_SESSION['usertype'] = 'p';
                header('location: patient/index.php');
            } else {
                $error = "Invalid email or password";
            }
        }
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | HMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 450px;
        }
        .login-title {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 30px;
        }
        .form-control {
            padding: 12px 15px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            margin-bottom: 20px;
        }
        .btn-login {
            background-color: #0d6efd;
            color: white;
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            background-color: #0b5ed7;
            transform: translateY(-2px);
        }
        .error-msg {
            color: #ef4444;
            font-size: 0.875rem;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="login-title text-center">Welcome Back</h2>
        <?php if($error != ""): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label text-muted">Email Address</label>
                <input type="email" name="useremail" class="form-control" placeholder="example@email.com" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Password</label>
                <input type="password" name="userpassword" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login mt-3">Login</button>
        </form>
        <p class="text-center mt-4 text-muted">Don't have an account? <a href="signup.php" class="text-primary text-decoration-none fw-semibold">Sign Up</a></p>
        <div class="text-center mt-3">
            <a href="index.php" class="text-muted text-decoration-none small">← Back to Home</a>
        </div>
    </div>
</body>
</html>
