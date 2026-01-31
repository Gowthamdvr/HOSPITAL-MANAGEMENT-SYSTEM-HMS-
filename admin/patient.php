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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients | Edoc</title>
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
        .card-custom { background: white; border-radius: 20px; border: 1px solid #e2e8f0; }
        .table thead th { background: #f8fafc; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; border: none; padding: 15px; }
        .table tbody td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="mb-5">
            <h3 class="fw-bold text-primary">Edoc.</h3>
        </div>
        <nav>
            <a href="index.php" class="nav-link-custom"><i class="bi bi-grid-fill"></i> Dashboard</a>
            <a href="doctors.php" class="nav-link-custom"><i class="bi bi-person-fill"></i> Doctors</a>
            <a href="schedule.php" class="nav-link-custom"><i class="bi bi-calendar-event-fill"></i> Schedule</a>
            <a href="appointment.php" class="nav-link-custom"><i class="bi bi-clock-fill"></i> Appointment</a>
            <a href="patient.php" class="nav-link-custom active"><i class="bi bi-people-fill"></i> Patients</a>
            <div class="mt-auto pt-5">
                <a href="../logout.php" class="nav-link-custom text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </nav>
    </div>

    <div class="main-content">
        <div class="mb-4">
            <h2 class="fw-bold m-0">Patients</h2>
            <p class="text-muted">Registered patients in the system</p>
        </div>

        <div class="card card-custom overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover m-0">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Email</th>
                            <th>NIC</th>
                            <th>Date of Birth</th>
                            <th>Contact</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $list = $database->query("SELECT * FROM patient ORDER BY pid DESC");
                        if($list->num_rows > 0){
                            while($row = $list->fetch_assoc()){
                                echo "<tr>
                                    <td>
                                        <div class='d-flex align-items-center'>
                                            <img src='https://ui-avatars.com/api/?name=".$row['pname']."&background=random' class='rounded-circle me-3' width='40'>
                                            <div class='fw-semibold'>".$row['pname']."</div>
                                        </div>
                                    </td>
                                    <td class='text-muted'>".$row['pemail']."</td>
                                    <td>".$row['pnic']."</td>
                                    <td>".$row['pdob']."</td>
                                    <td>".$row['ptel']."</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-5 text-muted'>No patients registered yet.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
