<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
$conn = new mysqli("localhost", "root", "", "apex-agent");
$conn->set_charset("utf8mb4");

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM contact_messages WHERE id=$id");
    header("Location: view-messages.php?deleted=1");
    exit;
}

// Single view
$single = null;
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $single = $conn->query("SELECT * FROM contact_messages WHERE id=$id")->fetch_assoc();
}

$result = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
<title>Messages - APEX Agent</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<style>body{background:#f5f6f8} .navbar-custom{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:12px 20px}</style>
</head>
<body>
<nav class="navbar navbar-custom">
<div class="container-fluid">
<a href="dashboard.php" class="text-white fw-bold text-decoration-none">🏢 APEX Agent</a>
<div class="ms-auto"><a href="dashboard.php" class="btn btn-light btn-sm">Dashboard</a> <a href="logout.php" class="btn btn-danger btn-sm ms-2">Logout</a></div>
</div>
</nav>

<div class="container mt-4">

<?php if(isset($_GET['deleted'])): ?><div class="alert alert-success">Message delete hoise!</div><?php endif; ?>

<?php if($single): ?>
<div class="card p-4 mb-4">
<h4>Message from <?= htmlspecialchars($single['name']) ?></h4>
<p><b>Email:</b> <?= htmlspecialchars($single['email']) ?></p>
<p><b>Date:</b> <?= $single['created_at'] ?></p>
<hr>
<p><b>Message:</b><br><?= nl2br(htmlspecialchars($single['message'])) ?></p>
<a href="view-messages.php?delete=<?= $single['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete korba ni Shinto?')">Delete This Message</a>
<a href="view-messages.php" class="btn btn-secondary ms-2">Back to All</a>
</div>
<?php endif; ?>

<div class="card p-3">
<h5>All Messages (<?= $result->num_rows ?>)</h5>
<table class="table table-bordered mt-3">
<thead class="table-dark"><tr><th>ID</th><th>Name</th><th>Email</th><th>Message</th><th>Date</th><th>Action</th></tr></thead>
<tbody>
<?php while($row=$result->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= htmlspecialchars($row['email']) ?></td>
<td><?= htmlspecialchars(mb_substr($row['message'],0,60)) ?>...</td>
<td><?= $row['created_at'] ?></td>
<td>
<a href="view-messages.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">View</a>
<a href="view-messages.php?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">Delete</a>
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