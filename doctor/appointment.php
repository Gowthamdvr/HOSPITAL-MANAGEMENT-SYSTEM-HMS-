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
$doc_res = $database->query("SELECT docid FROM doctor WHERE docemail='$user_email'");
if($doc_res->num_rows == 0){
    header("location: ../logout.php");
    exit();
}
$docid = $doc_res->fetch_assoc()['docid'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments | Edoc</title>
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
        .table-custom { background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="mb-5">
            <h3 class="fw-bold text-primary">Edoc.</h3>
        </div>
        <nav>
            <a href="index.php" class="nav-link-custom"><i class="bi bi-grid-fill"></i> Dashboard</a>
            <a href="schedule.php" class="nav-link-custom"><i class="bi bi-calendar-event-fill"></i> My Sessions</a>
            <a href="appointment.php" class="nav-link-custom active"><i class="bi bi-clock-fill"></i> My Appointments</a>
            <a href="patient.php" class="nav-link-custom"><i class="bi bi-people-fill"></i> My Patients</a>
            <div class="mt-auto pt-5">
                <a href="../logout.php" class="nav-link-custom text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </nav>
    </div>

    <div class="main-content">
        <div class="mb-4">
            <h2 class="fw-bold m-0">My Appointments</h2>
            <p class="text-muted">List of all patients who booked sessions with you</p>
        </div>

        <div class="table-custom">
            <div class="table-responsive">
                <table class="table table-hover m-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 py-3">Patient Name</th>
                            <th class="border-0 px-4 py-3">Appoint. No</th>
                            <th class="border-0 px-4 py-3">Session Title</th>
                            <th class="border-0 px-4 py-3">Session Date</th>
                            <th class="border-0 px-4 py-3">Appo. Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT appointment.*, patient.pname, schedule.title, schedule.scheduledate 
                                  FROM appointment 
                                  JOIN patient ON appointment.pid = patient.pid 
                                  JOIN schedule ON appointment.scheduleid = schedule.scheduleid 
                                  WHERE schedule.docid=$docid
                                  ORDER BY schedule.scheduledate DESC";
                        $res = $database->query($query);
                        if($res->num_rows > 0){
                            while($row = $res->fetch_assoc()){
                                echo "<tr>
                                    <td class='px-4'>".$row['pname']."</td>
                                    <td class='px-4 fw-bold'>#".$row['apponum']."</td>
                                    <td class='px-4'>".$row['title']."</td>
                                    <td class='px-4'>".$row['scheduledate']."</td>
                                    <td class='px-4'>".$row['appodate']."</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-5 text-muted'>No appointments found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
