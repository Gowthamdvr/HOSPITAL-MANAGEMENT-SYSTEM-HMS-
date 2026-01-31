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

if($_POST){
    if($action == 'add'){
        $docid = $_POST['docid'];
        $title = $_POST['title'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $nop = $_POST['nop'];

        $database->query("INSERT INTO schedule (docid,title,scheduledate,scheduletime,nop) VALUES ($docid,'$title','$date','$time',$nop)");
        header('location: schedule.php?status=success');
    }
}

if($action == 'delete'){
    $id = $_GET['id'];
    $database->query("DELETE FROM schedule WHERE scheduleid=$id");
    header('location: schedule.php?status=deleted');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Sessions | Edoc</title>
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
            <a href="schedule.php" class="nav-link-custom active"><i class="bi bi-calendar-event-fill"></i> Schedule</a>
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
                <h2 class="fw-bold m-0">Schedule Manager</h2>
                <p class="text-muted">Set doctor availability and session slots</p>
            </div>
            <button class="btn btn-primary px-4 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                <i class="bi bi-calendar-plus me-2"></i> Add Session
            </button>
        </div>

        <div class="card card-custom overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover m-0">
                    <thead>
                        <tr>
                            <th>Session Title</th>
                            <th>Doctor</th>
                            <th>Scheduled Date & Time</th>
                            <th>Max Number</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $list = $database->query("SELECT schedule.*, doctor.docname FROM schedule JOIN doctor ON schedule.docid = doctor.docid ORDER BY scheduledate DESC");
                        if($list->num_rows > 0){
                            while($row = $list->fetch_assoc()){
                                echo "<tr>
                                    <td><div class='fw-semibold'>".$row['title']."</div></td>
                                    <td>".$row['docname']."</td>
                                    <td>".$row['scheduledate']." @ ".$row['scheduletime']."</td>
                                    <td>".$row['nop']."</td>
                                    <td>
                                        <a href='?action=delete&id=".$row['scheduleid']."' class='btn btn-light btn-sm rounded-3 text-danger' onclick='return confirm(\"Delete this session?\")'><i class='bi bi-trash3'></i></a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-5 text-muted'>No sessions scheduled.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Schedule Modal -->
    <div class="modal fade" id="addScheduleModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="modal-title fw-bold">Schedule New Session</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="?action=add" method="POST">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Doctor</label>
                            <select name="docid" class="form-select rounded-3" required>
                                <?php
                                $docs = $database->query("SELECT * FROM doctor ORDER BY docname ASC");
                                while($drow = $docs->fetch_assoc()){
                                    echo "<option value='".$drow['docid']."'>".$drow['docname']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Session Title</label>
                            <input type="text" name="title" class="form-control rounded-3" placeholder="e.g. General Consultation" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold">Date</label>
                                <input type="date" name="date" class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold">Time</label>
                                <input type="time" name="time" class="form-control rounded-3" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Max Number of Patients</label>
                            <input type="number" name="nop" class="form-control rounded-3" value="50" required>
                        </div>
                        <div class="mt-4 text-end">
                            <button type="button" class="btn btn-light px-4 rounded-3 me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4 rounded-3">Create Session</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
