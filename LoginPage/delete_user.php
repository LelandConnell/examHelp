<?php
// delete_user.php — Remove a user by username

session_start();

// Optional: require login to delete
// if (!isset($_SESSION['username'])) {
//     header('Location: login.php');
//     exit;
// }

$USERS_FILE = __DIR__ . '/users.txt';

function delete_user(string $username, string $file): bool {
    if (!file_exists($file)) return false;

    $fh = fopen($file, 'r+'); // read/write
    if (!$fh) return false;

    $lines = [];
    if (flock($fh, LOCK_EX)) {
        while (($line = fgets($fh)) !== false) {
            $line = trim($line);
            if ($line === '') continue;
            $parts = explode(':', $line, 2);
            if (count($parts) === 2 && $parts[0] === $username) {
                // skip this line (delete)
                continue;
            }
            $lines[] = $line;
        }
        // Truncate and write back filtered lines
        ftruncate($fh, 0);
        rewind($fh);
        foreach ($lines as $l) {
            fwrite($fh, $l . PHP_EOL);
        }
        fflush($fh);
        flock($fh, LOCK_UN);
    } else {
        fclose($fh);
        return false;
    }

    fclose($fh);
    return true;
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    if ($username === '') {
        $error = 'Please enter a username to delete.';
    } else {
        if (delete_user($username, $USERS_FILE)) {
            $success = "Deleted user '{$username}' (if they existed).";
        } else {
            $error = 'Could not delete user. Please try again.';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="style.css">
<title>Delete user</title>
</head>
<body>
<h1>Delete user</h1>
<div class="nav">
  <a href="protected.php">Protected</a>
  <a href="list_users.php">List users</a>
  <a href="register.php">Register</a>
  <a href="login.php">Login</a>
  <a href="logout.php">Logout</a>
</div>
<?php if ($error): ?>
  <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
  <div class="success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<form method="post" action="delete_user.php" autocomplete="off">
  <label>
    Username to delete
    <input type="text" name="username" required>
  </label>
  <button type="submit">Delete</button>
</form>
</body>
</html>


