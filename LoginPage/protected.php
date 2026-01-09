<?php
// protected.php — Example protected page accessible only after login

session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="style.css">
<title>Protected area</title>
</head>
<body>
<h1>Protected area</h1>
<div class="nav">
  <a href="list_users.php">List users</a>
  <a href="delete_user.php">Delete user</a>
  <a href="logout.php">Logout</a>
</div>
<div class="card">
  <p>Welcome, <strong><?= htmlspecialchars($username) ?></strong>! You’re logged in.</p>
  <p>This page is only accessible to authenticated users.</p>
</div>
</body>
</html>

