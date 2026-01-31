<?php
session_start();

if(isset($_SESSION['user'])){
    if(($_SESSION['user'])=="" or $_SESSION['usertype']!='p'){
        header("location: ../login.php");
    }
}else{
    header("location: ../login.php");
}

include("../connection.php");
$user_email = $_SESSION['user'];

// Get Patient Info
$pat_res = $database->query("SELECT * FROM patient WHERE pemail='$user_email'");
if($pat_res->num_rows == 0){
    // This should not happen if session is valid, but good for safety
    header("location: ../logout.php");
    exit();
}
$pat_data = $pat_res->fetch_assoc();
$pid = $pat_data['pid'];

// Stats
$appo_count = $database->query("SELECT * FROM appointment WHERE pid=$pid")->num_rows;
$upcoming_appo = $database->query("SELECT appointment.* FROM appointment JOIN schedule ON appointment.scheduleid = schedule.scheduleid WHERE appointment.pid=$pid AND schedule.scheduledate >= CURDATE()")->num_rows;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard | HMS</title>
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
        .card-custom { background: white; border-radius: 24px; border: 1px solid #e2e8f0; padding: 30px; }
        .search-box { background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); padding: 20px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="mb-5">
            <h3 class="fw-bold text-primary">HMS.</h3>
        </div>
        <nav>
            <a href="index.php" class="nav-link-custom active"><i class="bi bi-grid-fill"></i> Dashboard</a>
            <a href="doctors.php" class="nav-link-custom"><i class="bi bi-person-heart"></i> All Doctors</a>
            <a href="schedule.php" class="nav-link-custom"><i class="bi bi-calendar-event-fill"></i> Scheduled Sessions</a>
            <a href="appointment.php" class="nav-link-custom"><i class="bi bi-clock-fill"></i> My Bookings</a>
            <div class="mt-auto pt-5">
                <a href="../logout.php" class="nav-link-custom text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold m-0">Hello, <?php echo $pat_data['pname']; ?></h2>
                <p class="text-muted">How are you feeling today?</p>
            </div>
            <div class="d-flex align-items-center">
                <img src="https://ui-avatars.com/api/?name=<?php echo $pat_data['pname']; ?>&background=0D6EFD&color=fff" class="rounded-circle" width="45">
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card">
                    <h5 class="text-muted mb-1">Total Appointments</h5>
                    <h2 class="fw-bold mb-0"><?php echo $appo_count; ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <h5 class="text-muted mb-1">Upcoming</h5>
                    <h2 class="fw-bold mb-0"><?php echo $upcoming_appo; ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <h5 class="text-muted mb-1">Last Visit</h5>
                    <h2 class="fw-bold mb-0">None</h2>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12">
                <div class="search-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold m-0">Search for Doctors</h5>
                        <a href="schedule.php" class="btn btn-primary btn-sm rounded-pill px-4">Book New Appointment</a>
                    </div>
                    <form action="doctors.php" method="GET" class="row g-2">
                        <div class="col-md-9">
                            <input type="text" name="search" class="form-control border-0 bg-light p-3 rounded-3" placeholder="Search Doctor Name or Email">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100 p-3 rounded-3 fw-bold">Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card-custom">
                    <h5 class="fw-bold mb-4">Your Upcoming Sessions</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Appo. No</th>
                                    <th>Title</th>
                                    <th>Doctor</th>
                                    <th>Date & Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT appointment.*, schedule.title, schedule.scheduledate, schedule.scheduletime, doctor.docname 
                                          FROM appointment 
                                          JOIN schedule ON appointment.scheduleid = schedule.scheduleid 
                                          JOIN doctor ON schedule.docid = doctor.docid
                                          WHERE appointment.pid=$pid AND schedule.scheduledate >= CURDATE()
                                          ORDER BY schedule.scheduledate ASC";
                                $res = $database->query($query);
                                if($res->num_rows > 0){
                                    while($row = $res->fetch_assoc()){
                                        echo "<tr>
                                            <td class='fw-bold'>#".$row['apponum']."</td>
                                            <td>".$row['title']."</td>
                                            <td>Dr. ".$row['docname']."</td>
                                            <td>".$row['scheduledate']." @ ".$row['scheduletime']."</td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center py-4 text-muted'>No upcoming appointments.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="appointment.php" class="btn btn-outline-primary mt-3">View All Bookings</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
