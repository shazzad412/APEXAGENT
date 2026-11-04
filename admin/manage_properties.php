<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
$conn = new mysqli("localhost", "root", "", "apex-agent");
$conn->set_charset("utf8mb4");

// Filter
$filter = $_GET['location'] ?? 'all';
$where = "";
if ($filter !== 'all') {
    $filterSafe = $conn->real_escape_string($filter);
    $where = "WHERE location LIKE '%$filterSafe%'";
}

$result = $conn->query("SELECT * FROM properties $where ORDER BY id DESC");

// Locations list for buttons
$locResult = $conn->query("SELECT DISTINCT location FROM properties ORDER BY location");

function e($v){ return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Properties</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<style>
body{background:#f5f6f8}
.navbar-custom{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:12px 20px}
.property-image{width:80px;height:60px;object-fit:cover;border-radius:5px}
.filter-btn{margin-right:6px}
</style>
</head>
<body>
<nav class="navbar navbar-custom">
<div class="container-fluid">
<a href="dashboard.php" class="text-white text-decoration-none fw-bold">🏢 APEX Agent</a>
<div class="ms-auto"><a href="dashboard.php" class="btn btn-light btn-sm">Dashboard</a> <a href="logout.php" class="btn btn-danger btn-sm">Logout</a></div>
</div>
</nav>

<div class="container-fluid px-4 mt-4">
<div class="d-flex justify-content-between align-items-center mb-3">
<h4>Manage Properties</h4>
<a href="add-property.php" class="btn btn-success">+ Add Room</a>
</div>

<!-- FILTER BUTTONS -->
<div class="mb-3">
<a href="?location=all" class="btn btn-sm filter-btn <?= $filter=='all'?'btn-dark':'btn-outline-dark' ?>">All (<?= $conn->query("SELECT COUNT(*) as c FROM properties")->fetch_assoc()['c'] ?>)</a>
<a href="?location=Niigata" class="btn btn-sm filter-btn <?= $filter=='Niigata'?'btn-primary':'btn-outline-primary' ?>">Niigata</a>
<a href="?location=Fukushima" class="btn btn-sm filter-btn <?= $filter=='Fukushima'?'btn-primary':'btn-outline-primary' ?>">Fukushima</a>
<a href="?location=Tokyo" class="btn btn-sm filter-btn <?= $filter=='Tokyo'?'btn-primary':'btn-outline-primary' ?>">Tokyo</a>
<?php while($l = $locResult->fetch_assoc()): 
if(in_array($l['location'], ['Niigata','Fukushima','Tokyo','nigata japan'])) continue;
?>
<a href="?location=<?= e($l['location']) ?>" class="btn btn-sm filter-btn <?= $filter==$l['location']?'btn-secondary':'btn-outline-secondary' ?>"><?= e($l['location']) ?></a>
<?php endwhile; ?>
</div>

<div class="table-responsive">
<table class="table table-bordered bg-white">
<thead class="table-dark"><tr><th>ID</th><th>Title</th><th>Price</th><th>Location</th><th>Image</th><th>Action</th></tr></thead>
<tbody>
<?php while($row=$result->fetch_assoc()): $img=basename($row['image'] ?? ''); ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= e($row['title']) ?></td>
<td><b>¥<?= e($row['price']) ?></b></td>
<td><span class="badge bg-info"><?= e($row['location']) ?></span></td>
<td><?php if($img): ?><img src="../uploads/<?= e($img) ?>" class="property-image"><?php else: ?>No image<?php endif; ?></td>
<td>
<a href="edit-property.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
<a href="delete-property.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>
</body>
</html>
<?php $conn->close(); ?>