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

$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT schedule.*, doctor.docname, specialties.sname 
        FROM schedule 
        JOIN doctor ON schedule.docid = doctor.docid 
        JOIN specialties ON doctor.specialties = specialties.id
        WHERE schedule.scheduledate >= CURDATE()";

if($search != ''){
    if(is_numeric($search)){
        $sql .= " AND schedule.docid = $search";
    } else {
        $sql .= " AND (doctor.docname LIKE '%$search%' OR schedule.title LIKE '%$search%' OR specialties.sname LIKE '%$search%')";
    }
}
$sql .= " ORDER BY scheduledate ASC";
$list = $database->query($sql);

if(isset($_GET['id'])){
    $scheduleid = $_GET['id'];
    
    // Get next appointment number
    $apponum_res = $database->query("SELECT MAX(apponum) as last_num FROM appointment WHERE scheduleid=$scheduleid");
    $apponum = 1;
    if($apponum_res->num_rows > 0){
        $last_num = $apponum_res->fetch_assoc()['last_num'];
        $apponum = $last_num + 1;
    }

    // Check if patient already booked this session
    $check_res = $database->query("SELECT * FROM appointment WHERE scheduleid=$scheduleid AND pid=$pid");
    if($check_res->num_rows > 0){
        header('location: schedule.php?error=already_booked');
    } else {
        // Check availability
        $nop_res = $database->query("SELECT nop FROM schedule WHERE scheduleid=$scheduleid");
        $max_nop = $nop_res->fetch_assoc()['nop'];
        
        $current_appo = $database->query("SELECT * FROM appointment WHERE scheduleid=$scheduleid")->num_rows;

        if($current_appo < $max_nop){
            $today = date('Y-m-d');
            $database->query("INSERT INTO appointment (pid, apponum, scheduleid, appodate) VALUES ($pid, $apponum, $scheduleid, '$today')");
            header('location: appointment.php?status=success');
        } else {
            header('location: schedule.php?error=full');
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheduled Sessions | Edoc</title>
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
        .session-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; transition: all 0.3s ease; }
        .session-card:hover { transform: translateY(-5px); border-color: var(--primary-color); }
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
            <a href="schedule.php" class="nav-link-custom active"><i class="bi bi-calendar-event-fill"></i> Scheduled Sessions</a>
            <a href="appointment.php" class="nav-link-custom"><i class="bi bi-clock-fill"></i> My Bookings</a>
            <div class="mt-auto pt-5">
                <a href="../logout.php" class="nav-link-custom text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </nav>
    </div>

    <div class="main-content">
        <div class="mb-5">
            <h2 class="fw-bold m-0">Available Sessions</h2>
            <p class="text-muted">Book your slot for upcoming medical consultations</p>
        </div>

        <?php if(isset($_GET['error'])): ?>
            <?php if($_GET['error'] == 'full'): ?>
            <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">
                This session is already full. Please select another slot.
            </div>
            <?php elseif($_GET['error'] == 'already_booked'): ?>
            <div class="alert alert-warning rounded-4 border-0 shadow-sm mb-4">
                You have already booked this session. Check "My Bookings" for details.
            </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="row g-4">
            <?php if($list->num_rows > 0): ?>
                <?php while($row = $list->fetch_assoc()): 
                    $sid = $row['scheduleid'];
                    $booked = $database->query("SELECT * FROM appointment WHERE scheduleid=$sid")->num_rows;
                    $rem = $row['nop'] - $booked;
                ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="session-card p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="fw-bold mb-1"><?php echo $row['title']; ?></h5>
                                    <p class="text-primary small fw-semibold mb-0">Dr. <?php echo $row['docname']; ?></p>
                                    <span class="text-muted extra-small"><?php echo $row['sname']; ?></span>
                                </div>
                                <div class="bg-light p-2 rounded-3 text-center" style="min-width: 60px;">
                                    <h4 class="fw-bold m-0 text-primary"><?php echo date('d', strtotime($row['scheduledate'])); ?></h4>
                                    <span class="small text-muted text-uppercase"><?php echo date('M', strtotime($row['scheduledate'])); ?></span>
                                </div>
                            </div>
                            <hr class="text-muted opacity-25">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <i class="bi bi-clock text-muted me-1"></i>
                                    <span class="small"><?php echo date('h:i A', strtotime($row['scheduletime'])); ?></span>
                                </div>
                                <div class="text-end">
                                    <span class="badge <?php echo ($rem > 0) ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?> rounded-pill">
                                        <?php echo ($rem > 0) ? $rem.' slots left' : 'Full'; ?>
                                    </span>
                                </div>
                            </div>
                            <?php 
                            $check_booked = $database->query("SELECT * FROM appointment WHERE scheduleid=$sid AND pid=$pid");
                            if($check_booked->num_rows > 0): ?>
                                <button class="btn btn-success w-100 rounded-pill py-2 fw-bold" disabled><i class="bi bi-check-circle-fill me-2"></i>Already Booked</button>
                            <?php elseif($rem > 0): ?>
                                <a href="?id=<?php echo $sid; ?>" class="btn btn-primary w-100 rounded-pill py-2 fw-bold" onclick="return confirm('Do you want to book this session?')">Book Now</a>
                            <?php else: ?>
                                <button class="btn btn-secondary w-100 rounded-pill py-2 fw-bold" disabled>Fully Booked</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="80" class="mb-3 opacity-25">
                    <h5 class="fw-bold">No sessions found for your search</h5>
                    <p class="text-muted">Try using different keywords or browse all available sessions.</p>
                    <a href="schedule.php" class="btn btn-primary rounded-pill px-4 mt-2">Show All Sessions</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
