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
$user_email = $_SESSION['user'];

// Fetch some stats
$doctor_count = $database->query("SELECT * FROM doctor")->num_rows;
$patient_count = $database->query("SELECT * FROM patient")->num_rows;
$appointment_count = $database->query("SELECT * FROM appointment")->num_rows;
$schedule_count = $database->query("SELECT * FROM schedule")->num_rows;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | HMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --sidebar-width: 280px;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: white;
            border-right: 1px solid #e2e8f0;
            padding: 20px;
            z-index: 1000;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 40px;
        }
        .nav-link-custom {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #64748b;
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 5px;
            transition: all 0.3s ease;
        }
        .nav-link-custom:hover, .nav-link-custom.active {
            background-color: #f1f5f9;
            color: var(--primary-color);
        }
        .nav-link-custom i {
            margin-right: 12px;
            font-size: 1.25rem;
        }
        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        .bg-primary-soft { background: rgba(13, 110, 253, 0.1); color: #0d6efd; }
        .bg-success-soft { background: rgba(25, 135, 84, 0.1); color: #198754; }
        .bg-warning-soft { background: rgba(255, 193, 7, 0.1); color: #ffc107; }
        .bg-info-soft { background: rgba(13, 202, 240, 0.1); color: #0dcaf0; }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="mb-5">
            <h3 class="fw-bold text-primary">HMS.</h3>
        </div>
        <nav>
            <a href="index.php" class="nav-link-custom active"><i class="bi bi-grid-fill"></i> Dashboard</a>
            <a href="doctors.php" class="nav-link-custom"><i class="bi bi-person-fill"></i> Doctors</a>
            <a href="schedule.php" class="nav-link-custom"><i class="bi bi-calendar-event-fill"></i> Schedule</a>
            <a href="appointment.php" class="nav-link-custom"><i class="bi bi-clock-fill"></i> Appointment</a>
            <a href="patient.php" class="nav-link-custom"><i class="bi bi-people-fill"></i> Patients</a>
            <div class="mt-auto pt-5">
                <a href="../logout.php" class="nav-link-custom text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </nav>
    </div>

    <div class="main-content">
        <div class="header">
            <div>
                <h2 class="fw-bold m-0">Dashboard</h2>
                <p class="text-muted">Welcome back, Admin</p>
            </div>
            <div class="d-flex align-items-center">
                <div class="text-end me-3">
                    <p class="m-0 fw-semibold"><?php echo $user_email; ?></p>
                    <span class="badge bg-primary">Administrator</span>
                </div>
                <img src="https://ui-avatars.com/api/?name=Admin&background=0D6EFD&color=fff" class="rounded-circle" width="45" alt="Profile">
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-primary-soft">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h5 class="text-muted mb-1">Doctors</h5>
                    <h2 class="fw-bold mb-0"><?php echo $doctor_count; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-success-soft">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h5 class="text-muted mb-1">Patients</h5>
                    <h2 class="fw-bold mb-0"><?php echo $patient_count; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-warning-soft">
                        <i class="bi bi-calendar-event-fill"></i>
                    </div>
                    <h5 class="text-muted mb-1">Schedules</h5>
                    <h2 class="fw-bold mb-0"><?php echo $schedule_count; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-info-soft">
                        <i class="bi bi-clock-fill"></i>
                    </div>
                    <h5 class="text-muted mb-1">Appointments</h5>
                    <h2 class="fw-bold mb-0"><?php echo $appointment_count; ?></h2>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-4">Quick Actions</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="doctors.php?action=add" class="btn btn-outline-primary w-100 p-4 rounded-4 text-start">
                                <i class="bi bi-person-plus-fill fs-3 d-block mb-2"></i>
                                <span class="fw-bold">Add New Doctor</span>
                                <p class="small text-muted mb-0">Add a specialist to the system</p>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="schedule.php?action=add" class="btn btn-outline-success w-100 p-4 rounded-4 text-start">
                                <i class="bi bi-calendar-plus-fill fs-3 d-block mb-2"></i>
                                <span class="fw-bold">Create Schedule</span>
                                <p class="small text-muted mb-0">Manage doctor availability</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <h5 class="fw-bold mb-4">System Status</h5>
                    <div class="d-flex align-items-center mb-3">
                        <div class="dot bg-success me-2" style="width: 10px; height: 10px; border-radius: 50%;"></div>
                        <span>Database Operational</span>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="dot bg-success me-2" style="width: 10px; height: 10px; border-radius: 50%;"></div>
                        <span>Booking Engine Online</span>
                    </div>
                    <hr>
                    <p class="small text-muted">Last updated: <?php echo date('Y-m-d H:i'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
