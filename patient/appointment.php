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
$pat_res = $database->query("SELECT pid FROM patient WHERE pemail='$user_email'");
if($pat_res->num_rows == 0){
    header("location: ../logout.php");
    exit();
}
$pid = $pat_res->fetch_assoc()['pid'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings | Edoc</title>
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
        .booking-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="mb-5">
            <h3 class="fw-bold text-primary">Edoc.</h3>
        </div>
        <nav>
            <a href="index.php" class="nav-link-custom"><i class="bi bi-grid-fill"></i> Dashboard</a>
            <a href="doctors.php" class="nav-link-custom"><i class="bi bi-person-heart"></i> All Doctors</a>
            <a href="schedule.php" class="nav-link-custom"><i class="bi bi-calendar-event-fill"></i> Scheduled Sessions</a>
            <a href="appointment.php" class="nav-link-custom active"><i class="bi bi-clock-fill"></i> My Bookings</a>
            <div class="mt-auto pt-5">
                <a href="../logout.php" class="nav-link-custom text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </nav>
    </div>

    <div class="main-content">
        <div class="mb-5">
            <h2 class="fw-bold m-0">My Bookings</h2>
            <p class="text-muted">Track your upcoming and past medical appointments</p>
        </div>

        <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> Your appointment has been successfully booked!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <?php
            $query = "SELECT appointment.*, schedule.title, schedule.scheduledate, schedule.scheduletime, doctor.docname, specialties.sname 
                      FROM appointment 
                      JOIN schedule ON appointment.scheduleid = schedule.scheduleid 
                      JOIN doctor ON schedule.docid = doctor.docid
                      JOIN specialties ON doctor.specialties = specialties.id
                      WHERE appointment.pid=$pid
                      ORDER BY schedule.scheduledate DESC";
            $res = $database->query($query);
            if($res->num_rows > 0){
                while($row = $res->fetch_assoc()){
                    $is_past = (strtotime($row['scheduledate']) < strtotime(date('Y-m-d')));
                    echo "
                    <div class='col-md-6'>
                        <div class='booking-card p-4'>
                            <div class='d-flex justify-content-between mb-3'>
                                <span class='badge ".($is_past ? 'bg-secondary' : 'bg-primary')." rounded-pill'>".($is_past ? 'Completed' : 'Upcoming')."</span>
                                <span class='text-muted small'>Appo #".$row['apponum']."</span>
                            </div>
                            <h5 class='fw-bold mb-1'>".$row['title']."</h5>
                            <p class='text-primary fw-semibold mb-0'>Dr. ".$row['docname']."</p>
                            <p class='text-muted small'>".$row['sname']."</p>
                            <hr class='opacity-25'>
                            <div class='row mt-3'>
                                <div class='col-6'>
                                    <p class='text-muted small mb-0'>Date</p>
                                    <p class='fw-bold mb-0'>".$row['scheduledate']."</p>
                                </div>
                                <div class='col-6 text-end'>
                                    <p class='text-muted small mb-0'>Time</p>
                                    <p class='fw-bold mb-0'>".date('h:i A', strtotime($row['scheduletime']))."</p>
                                </div>
                            </div>
                        </div>
                    </div>";
                }
            } else {
                echo "<div class='col-12 text-center py-5'><p class='text-muted'>You haven't booked any appointments yet.</p></div>";
            }
            ?>
        </div>
    </div>

</body>
</html>
