<?php
session_start();

if(isset($_SESSION['user'])){
    if(($_SESSION['user'])=="" or $_SESSION['usertype']!='d'){
        header("location: ../login.php");
    }
}else{
    header("location: ../login.php");
}

include("../connection.php");
$user_email = $_SESSION['user'];

// Get Doctor Info
$doc_res = $database->query("SELECT * FROM doctor WHERE docemail='$user_email'");
if($doc_res->num_rows == 0){
    header("location: ../logout.php");
    exit();
}
$doc_data = $doc_res->fetch_assoc();
$docid = $doc_data['docid'];

// Stats
$session_count = $database->query("SELECT * FROM schedule WHERE docid=$docid")->num_rows;
$appo_count = $database->query("SELECT appointment.* FROM appointment JOIN schedule ON appointment.scheduleid = schedule.scheduleid WHERE schedule.docid=$docid")->num_rows;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard | HMS</title>
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
        .stat-card { background: white; padding: 24px; border-radius: 20px; border: 1px solid #e2e8f0; }
        .table-custom { background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="mb-5">
            <h3 class="fw-bold text-primary">HMS.</h3>
        </div>
        <nav>
            <a href="index.php" class="nav-link-custom active"><i class="bi bi-grid-fill"></i> Dashboard</a>
            <a href="schedule.php" class="nav-link-custom"><i class="bi bi-calendar-event-fill"></i> My Sessions</a>
            <a href="appointment.php" class="nav-link-custom"><i class="bi bi-clock-fill"></i> My Appointments</a>
            <a href="patient.php" class="nav-link-custom"><i class="bi bi-people-fill"></i> My Patients</a>
            <div class="mt-auto pt-5">
                <a href="../logout.php" class="nav-link-custom text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold m-0">Doctor Dashboard</h2>
                <p class="text-muted">Welcome back, Dr. <?php echo $doc_data['docname']; ?></p>
            </div>
            <div class="d-flex align-items-center">
                <div class="text-end me-3">
                    <p class="m-0 fw-semibold">Dr. <?php echo $doc_data['docname']; ?></p>
                    <span class="badge bg-success">Specialist</span>
                </div>
                <img src="https://ui-avatars.com/api/?name=<?php echo $doc_data['docname']; ?>&background=0D6EFD&color=fff" class="rounded-circle" width="45">
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card">
                    <h5 class="text-muted mb-1">Total Sessions</h5>
                    <h2 class="fw-bold mb-0"><?php echo $session_count; ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <h5 class="text-muted mb-1">Total Appointments</h5>
                    <h2 class="fw-bold mb-0"><?php echo $appo_count; ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <h5 class="text-muted mb-1">Working Days</h5>
                    <h2 class="fw-bold mb-0">Monday - Friday</h2>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="table-custom">
                    <div class="p-4 border-bottom">
                        <h5 class="fw-bold m-0">Upcoming Appointments Today</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover m-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 px-4 py-3">Patient</th>
                                    <th class="border-0 px-4 py-3">Appoint. No</th>
                                    <th class="border-0 px-4 py-3">Session Title</th>
                                    <th class="border-0 px-4 py-3">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $today = date('Y-m-d');
                                $query = "SELECT appointment.*, patient.pname, schedule.title, schedule.scheduletime 
                                          FROM appointment 
                                          JOIN patient ON appointment.pid = patient.pid 
                                          JOIN schedule ON appointment.scheduleid = schedule.scheduleid 
                                          WHERE schedule.docid=$docid AND schedule.scheduledate='$today'
                                          ORDER BY schedule.scheduletime ASC";
                                $res = $database->query($query);
                                if($res->num_rows > 0){
                                    while($row = $res->fetch_assoc()){
                                        echo "<tr>
                                            <td class='px-4'>".$row['pname']."</td>
                                            <td class='px-4'>#".$row['apponum']."</td>
                                            <td class='px-4'>".$row['title']."</td>
                                            <td class='px-4'>".$row['scheduletime']."</td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center py-5 text-muted'>No appointments for today.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
