<?php
session_start();

if(isset($_SESSION['user'])){
    if(($_SESSION['user'])=="" or $_SESSION['usertype']!='a'){
        header("location: ../login.php");
    }
}else{
    header("location: ../login.php");
}

include("../connection.php");

$action = isset($_GET['action']) ? $_GET['action'] : '';
$error = "";

if($_POST){
    if($action == 'add'){
        $name = $_POST['name'];
        $email = $_POST['email'];
        $nic = $_POST['nic'];
        $tele = $_POST['tele'];
        $spec = $_POST['spec'];
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];

        if($password == $cpassword){
            $result = $database->query("SELECT * FROM webuser WHERE email='$email'");
            if($result->num_rows == 1){
                $error = "Email already registered!";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $database->query("INSERT INTO doctor(docemail,docname,docpassword,docnic,doctel,specialties) VALUES('$email','$name','$hashed_password','$nic','$tele',$spec)");
                $database->query("INSERT INTO webuser VALUES('$email','d')");
                header('location: doctors.php?status=success');
            }
        } else {
            $error = "Passwords do not match!";
        }
    } elseif($action == 'edit'){
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $nic = $_POST['nic'];
        $tele = $_POST['tele'];
        $spec = $_POST['spec'];
        $old_email = $_POST['old_email'];

        $database->query("UPDATE doctor SET docemail='$email', docname='$name', docnic='$nic', doctel='$tele', specialties=$spec WHERE docid=$id");
        $database->query("UPDATE webuser SET email='$email' WHERE email='$old_email'");
        header('location: doctors.php?status=updated');
    }
}

if($action == 'delete'){
    $id = $_GET['id'];
    $email = $_GET['email'];
    $database->query("DELETE FROM webuser WHERE email='$email'");
    $database->query("DELETE FROM doctor WHERE docid=$id");
    header('location: doctors.php?status=deleted');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors | Edoc</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --primary-color: #0d6efd; --sidebar-width: 280px; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .sidebar { width: var(--sidebar-width); height: 100vh; position: fixed; left: 0; top: 0; background: white; border-right: 1px solid #e2e8f0; padding: 20px; z-index: 1000; }
        .main-content { margin-left: var(--sidebar-width); padding: 40px; }
        .nav-link-custom { display: flex; align-items: center; padding: 12px 15px; color: #64748b; text-decoration: none; border-radius: 12px; margin-bottom: 5px; }
        .nav-link-custom.active { background-color: #f1f5f9; color: var(--primary-color); }
        .nav-link-custom i { margin-right: 12px; font-size: 1.25rem; }
        .card-custom { background: white; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        .table thead th { background: #f8fafc; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; border: none; padding: 15px; }
        .table tbody td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        .btn-action { width: 35px; height: 35px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; }
        .btn-edit { background: #eff6ff; color: #2563eb; }
        .btn-delete { background: #fef2f2; color: #dc2626; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="mb-5">
            <h3 class="fw-bold text-primary">Edoc.</h3>
        </div>
        <nav>
            <a href="index.php" class="nav-link-custom"><i class="bi bi-grid-fill"></i> Dashboard</a>
            <a href="doctors.php" class="nav-link-custom active"><i class="bi bi-person-fill"></i> Doctors</a>
            <a href="schedule.php" class="nav-link-custom"><i class="bi bi-calendar-event-fill"></i> Schedule</a>
            <a href="appointment.php" class="nav-link-custom"><i class="bi bi-clock-fill"></i> Appointment</a>
            <a href="patient.php" class="nav-link-custom"><i class="bi bi-people-fill"></i> Patients</a>
            <div class="mt-auto pt-5">
                <a href="../logout.php" class="nav-link-custom text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold m-0">Doctors</h2>
                <p class="text-muted">Manage all medical specialists</p>
            </div>
            <button class="btn btn-primary px-4 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#addDoctorModal">
                <i class="bi bi-plus-lg me-2"></i> Add New Doctor
            </button>
        </div>

        <?php if(isset($_GET['status'])): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> Doctor data successfully updated!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card card-custom overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover m-0">
                    <thead>
                        <tr>
                            <th>Doctor Name</th>
                            <th>Email</th>
                            <th>Specialization</th>
                            <th>Contact</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $list = $database->query("SELECT doctor.*, specialties.sname FROM doctor JOIN specialties ON doctor.specialties = specialties.id ORDER BY docid DESC");
                        if($list->num_rows > 0){
                            while($row = $list->fetch_assoc()){
                                echo "<tr>
                                    <td>
                                        <div class='d-flex align-items-center'>
                                            <img src='https://ui-avatars.com/api/?name=".$row['docname']."&background=random' class='rounded-circle me-3' width='40'>
                                            <div class='fw-semibold'>".$row['docname']."</div>
                                        </div>
                                    </td>
                                    <td class='text-muted'>".$row['docemail']."</td>
                                    <td><span class='badge bg-light text-dark fw-normal px-3 py-2 rounded-pill'>".$row['sname']."</span></td>
                                    <td class='text-muted'>".$row['doctel']."</td>
                                    <td>
                                        <div class='d-flex gap-2'>
                                            <a href='?action=edit&id=".$row['docid']."' class='btn-action btn-edit text-decoration-none'><i class='bi bi-pencil-square'></i></a>
                                            <a href='?action=delete&id=".$row['docid']."&email=".$row['docemail']."' class='btn-action btn-delete text-decoration-none' onclick='return confirm(\"Are you sure?\")'><i class='bi bi-trash3-fill'></i></a>
                                        </div>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-5 text-muted'>No doctors found in the system.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Doctor Modal -->
    <div class="modal fade" id="addDoctorModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="modal-title fw-bold">Add New Doctor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="?action=add" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Full Name</label>
                                <input type="text" name="name" class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">NIC</label>
                                <input type="text" name="nic" class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Phone Number</label>
                                <input type="tel" name="tele" class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-muted small fw-bold">Specialization</label>
                                <select name="spec" class="form-select rounded-3">
                                    <?php
                                    $specs = $database->query("SELECT * FROM specialties ORDER BY sname ASC");
                                    while($srow = $specs->fetch_assoc()){
                                        echo "<option value='".$srow['id']."'>".$srow['sname']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Password</label>
                                <input type="password" name="password" class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Confirm Password</label>
                                <input type="password" name="cpassword" class="form-control rounded-3" required>
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <button type="button" class="btn btn-light px-4 rounded-3 me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4 rounded-3">Register Doctor</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Doctor Modal (Triggered by PHP if action=edit) -->
    <?php if($action == 'edit' && isset($_GET['id'])): 
        $id = $_GET['id'];
        $d_data = $database->query("SELECT * FROM doctor WHERE docid=$id")->fetch_assoc();
    ?>
    <div class="modal fade show" id="editDoctorModal" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="modal-title fw-bold">Edit Doctor Profile</h5>
                    <a href="doctors.php" class="btn-close"></a>
                </div>
                <div class="modal-body p-4">
                    <form action="?action=edit" method="POST">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <input type="hidden" name="old_email" value="<?php echo $d_data['docemail']; ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Full Name</label>
                                <input type="text" name="name" class="form-control rounded-3" value="<?php echo $d_data['docname']; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control rounded-3" value="<?php echo $d_data['docemail']; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">NIC</label>
                                <input type="text" name="nic" class="form-control rounded-3" value="<?php echo $d_data['docnic']; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Phone Number</label>
                                <input type="tel" name="tele" class="form-control rounded-3" value="<?php echo $d_data['doctel']; ?>" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-muted small fw-bold">Specialization</label>
                                <select name="spec" class="form-select rounded-3">
                                    <?php
                                    $specs = $database->query("SELECT * FROM specialties ORDER BY sname ASC");
                                    while($srow = $specs->fetch_assoc()){
                                        $sel = ($srow['id'] == $d_data['specialties']) ? 'selected' : '';
                                        echo "<option value='".$srow['id']."' $sel>".$srow['sname']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <a href="doctors.php" class="btn btn-light px-4 rounded-3 me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 rounded-3">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
