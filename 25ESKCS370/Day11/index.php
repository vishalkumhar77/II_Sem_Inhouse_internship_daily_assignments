<?php
session_start();
include "db_connect.php";

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
$search = $_GET['search'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

// Handle search
$searchQuery = "";
if ($search) {
    $searchEscaped = mysqli_real_escape_string($conn, $search);
    $searchQuery = " WHERE name LIKE '%$searchEscaped%' OR email LIKE '%$searchEscaped%' OR branch LIKE '%$searchEscaped%' OR college LIKE '%$searchEscaped%'";
}

$students = mysqli_query($conn, "SELECT * FROM students$searchQuery ORDER BY id DESC");

// Count
$countQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM students$searchQuery");
$totalResults = mysqli_fetch_assoc($countQuery)['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Ready - Day 11</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
       body{
    background: linear-gradient(135deg,#eef6ff,#dceeff,#f8fbff);
    font-family: "Trebuchet MS", Arial, sans-serif;
}

.navbar{
    background: linear-gradient(90deg,#0061ff,#60a5fa)!important;
    box-shadow:0 6px 18px rgba(0,0,0,.15);
}

.navbar-brand{
    font-size:1.4rem;
    letter-spacing:1px;
}

.profile-img{
    width:50px;
    height:50px;
    border-radius:12px;
    object-fit:cover;
    border:3px solid #0d6efd;
    transition:.3s;
}

.profile-img:hover{
    transform:scale(1.1) rotate(3deg);
}

.card{
    border:none;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    transition:.3s;
}

.card:hover{
    transform:translateY(-4px);
}

.card-header{
    font-weight:bold;
    letter-spacing:.5px;
}

.table{
    margin-bottom:0;
}

.table thead{
    background:#0d6efd;
    color:#fff;
}

.table thead th{
    border:none;
}

.table tbody tr{
    transition:.25s;
}

.table tbody tr:hover{
    background:#e8f1ff;
}

.btn{
    border-radius:30px;
    font-weight:600;
    transition:.3s;
}

.btn:hover{
    transform:translateY(-2px);
}

.form-control,
.form-select{
    border-radius:10px;
    border:1px solid #bcd3ff;
}

.form-control:focus,
.form-select:focus{
    border-color:#0d6efd;
    box-shadow:0 0 0 .2rem rgba(13,110,253,.15);
}

.badge{
    padding:8px 12px;
    border-radius:20px;
    font-size:.8rem;
}

footer{
    background:#0d47a1!important;
    letter-spacing:.5px;
}

.alert{
    border:none;
    border-radius:12px;
}

small.text-muted{
    font-size:.9rem;
}
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-gradient">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-graduation-cap me-2"></i>Student Management System
            </a>
            <div class="d-flex align-items-center">
                <span class="navbar-text text-white me-3">
                    <i class="fas fa-user-shield me-1"></i>Production Ready
                </span>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if ($success) { ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php } ?>
        <?php if ($error) { ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <!-- Search Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="index.php" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by name, email, branch, or college..."
                           value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>Search
                    </button>
                    <?php if ($search) { ?>
                        <a href="index.php" class="btn btn-outline-secondary">Clear</a>
                    <?php } ?>
                </form>
                <small class="text-muted mt-2 d-block">
                    <?= $totalResults ?> result(s) found
                    <?php if ($search) { ?> for "<?= htmlspecialchars($search) ?>" <?php } ?>
                </small>
            </div>
        </div>

        <!-- Student Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Student Records</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>College</th>
                                <th>Branch</th>
                                <th>CGPA</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            while ($row = mysqli_fetch_assoc($students)) {
                                $cgpa = $row['cgpa'] ?? 0;
                                $grade = $cgpa >= 9 ? 'A+' : ($cgpa >= 8 ? 'A' : ($cgpa >= 7 ? 'B+' : 'B'));
                                $badgeClass = $cgpa >= 9 ? 'bg-success' : ($cgpa >= 8 ? 'bg-primary' : ($cgpa >= 7 ? 'bg-warning' : 'bg-danger'));
                                $photoId = $i;
                            ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td>
                                        <img src="https://i.pravatar.cc/100?img=<?= $photoId + 10 ?>" 
                                             class="profile-img" alt="Photo">
                                    </td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['college']) ?></td>
                                    <td><?= htmlspecialchars($row['branch']) ?></td>
                                    <td><?= $cgpa ?></td>
                                    <td><span class="badge <?= $badgeClass ?>"><?= $grade ?></span></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($totalResults == 0) { ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-search fa-3x mb-3 d-block"></i>
                        No students found matching your search.
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- File Upload Section -->
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-upload me-2"></i>Profile Photo Upload</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="upload.php" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Select Student</label>
                            <select name="student_id" class="form-select" required>
                                <option value="">Choose student...</option>
                                <?php
                                $allStudents = mysqli_query($conn, "SELECT id, name FROM students");
                                while ($s = mysqli_fetch_assoc($allStudents)) {
                                    echo "<option value='{$s['id']}'>{$s['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload Photo</label>
                            <input type="file" name="photo" class="form-control" accept="image/*" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-upload me-1"></i>Upload
                            </button>
                        </div>
                    </div>
                </form>
                <small class="text-muted">Supported formats: JPG, PNG, GIF (max 2MB)</small>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-4">
        <p class="mb-0">&copy; 2026 Student Management System — Day 11 | Production Ready</p>
    </footer>
</body>
</html>
