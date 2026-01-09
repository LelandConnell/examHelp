<?php
// list_users.php — Display all usernames (hashes hidden)

session_start();

// Optional: require login to view
// if (!isset($_SESSION['username'])) {
//     header('Location: login.php');
//     exit;
// }

$USERS_FILE = __DIR__ . '/users.txt';
$users = [];

if (file_exists($USERS_FILE)) {
    $fh = fopen($USERS_FILE, 'r');
    if ($fh) {
        while (($line = fgets($fh)) !== false) {
            $line = trim($line);
            if ($line === '') continue;
            $parts = explode(':', $line, 2);
            if (count($parts) === 2) {
                $users[] = $parts[0];
            }
        }
        fclose($fh);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="style.css">
<title>Users</title>

</head>
<body>
<h1>Registered users</h1>
<div class="nav">
  <a href="protected.php">Protected</a>
  <a href="login.php">Login</a>
  <a href="register.php">Register</a>
  <a href="delete_user.php">Delete user</a>
  <a href="logout.php">Logout</a>
</div>
<?php if (empty($users)): ?>
  <p>No users found.</p>
<?php else: ?>
  <ul>
    <?php foreach ($users as $u): ?>
      <li><?= htmlspecialchars($u) ?></li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
</body>
</html>

