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

$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT doctor.*, specialties.sname FROM doctor JOIN specialties ON doctor.specialties = specialties.id";
if($search != ''){
    if(is_numeric($search)){
        $sql .= " WHERE doctor.docid = $search";
    } else {
        $sql .= " WHERE (doctor.docname LIKE '%$search%' OR doctor.docemail LIKE '%$search%' OR specialties.sname LIKE '%$search%')";
    }
}
$list = $database->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Doctors | Edoc</title>
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
        .doc-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; transition: transform 0.3s ease; height: 100%; display: flex; flex-direction: column; }
        .doc-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="mb-5">
            <h3 class="fw-bold text-primary">Edoc.</h3>
        </div>
        <nav>
            <a href="index.php" class="nav-link-custom"><i class="bi bi-grid-fill"></i> Dashboard</a>
            <a href="doctors.php" class="nav-link-custom active"><i class="bi bi-person-heart"></i> All Doctors</a>
            <a href="schedule.php" class="nav-link-custom"><i class="bi bi-calendar-event-fill"></i> Scheduled Sessions</a>
            <a href="appointment.php" class="nav-link-custom"><i class="bi bi-clock-fill"></i> My Bookings</a>
            <div class="mt-auto pt-5">
                <a href="../logout.php" class="nav-link-custom text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </nav>
    </div>

    <div class="main-content">
        <div class="mb-5">
            <h2 class="fw-bold m-0">Doctors List</h2>
            <p class="text-muted">Find and consult with our expert specialists</p>
        </div>

        <form action="" method="GET" class="mb-5">
            <div class="input-group search-bar">
                <input type="text" name="search" class="form-control border-0 bg-white p-3 shadow-sm rounded-start-4" placeholder="Search doctor by name or email..." value="<?php echo $search; ?>">
                <button type="submit" class="btn btn-primary px-4 rounded-end-4 shadow-sm"><i class="bi bi-search"></i></button>
            </div>
        </form>

        <div class="row g-4">
            <?php if($list->num_rows > 0): ?>
                <?php while($row = $list->fetch_assoc()): ?>
                    <div class="col-md-4">
                        <div class="doc-card p-4">
                            <div class="text-center mb-3">
                                <img src="https://ui-avatars.com/api/?name=<?php echo $row['docname']; ?>&background=random" class="rounded-circle mb-3" width="80">
                                <h5 class="fw-bold mb-1">Dr. <?php echo $row['docname']; ?></h5>
                                <span class="badge bg-primary-subtle text-primary mb-2"><?php echo $row['sname']; ?></span>
                                <p class="text-muted small"><?php echo $row['docemail']; ?></p>
                            </div>
                            <div class="mt-auto">
                                <a href="schedule.php?search=<?php echo $row['docid']; ?>" class="btn btn-primary w-100 rounded-pill">View Sessions</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/3306/3306601.png" width="80" class="mb-3 opacity-25">
                    <h5 class="fw-bold">No doctors found matching your search</h5>
                    <p class="text-muted">Try luxury searching for name, email or medical specialization.</p>
                    <a href="doctors.php" class="btn btn-primary rounded-pill px-4 mt-2">View All Doctors</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
