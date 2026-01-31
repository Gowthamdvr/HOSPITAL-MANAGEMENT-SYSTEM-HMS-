<?php
session_start();
include("connection.php");

$error = "";

if($_POST){
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $name = $fname." ".$lname;
    $address = $_POST['address'];
    $nic = $_POST['nic'];
    $dob = $_POST['dob'];
    $email = $_POST['email'];
    $tele = $_POST['tele'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    if($password == $cpassword){
        $result = $database->query("SELECT * FROM webuser WHERE email='$email'");
        if($result->num_rows == 1){
            $error = "Already have an account for this Email address.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $database->query("INSERT INTO patient(pemail,pname,ppassword,paddress,pnic,pdob,ptel) VALUES('$email','$name','$hashed_password','$address','$nic','$dob','$tele')");
            $database->query("INSERT INTO webuser VALUES('$email','p')");
            
            $_SESSION['user'] = $email;
            $_SESSION['usertype'] = 'p';
            header('location: patient/index.php');
        }
    } else {
        $error = "Password confirmation failed!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | HMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }
        .signup-container {
            background: white;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 600px;
        }
        .signup-title {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 30px;
        }
        .form-control {
            padding: 12px 15px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            margin-bottom: 15px;
        }
        .btn-signup {
            background-color: #0d6efd;
            color: white;
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-signup:hover {
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
    <div class="signup-container">
        <h2 class="signup-title text-center">Create Account</h2>
        <?php if($error != ""): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">First Name</label>
                    <input type="text" name="fname" class="form-control" placeholder="John" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Last Name</label>
                    <input type="text" name="lname" class="form-control" placeholder="Doe" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Address</label>
                <input type="text" name="address" class="form-control" placeholder="Enter your address" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">NIC Number</label>
                    <input type="text" name="nic" class="form-control" placeholder="123456789V" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Date of Birth</label>
                    <input type="date" name="dob" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="example@email.com" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Phone Number</label>
                    <input type="tel" name="tele" class="form-control" placeholder="07XXXXXXXX" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Confirm Password</label>
                    <input type="password" name="cpassword" class="form-control" placeholder="••••••••" required>
                </div>
            </div>
            <button type="submit" class="btn-signup mt-3">Sign Up</button>
        </form>
        <p class="text-center mt-4 text-muted">Already have an account? <a href="login.php" class="text-primary text-decoration-none fw-semibold">Login</a></p>
    </div>
</body>
</html>
