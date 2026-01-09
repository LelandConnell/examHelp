<?php
// login.php — Handle login form, verify credentials, start session

session_start();

// Path to user storage (same folder)
$USERS_FILE = __DIR__ . '/users.txt';

// Helper: fetch hashed password by username
function get_user_hash(string $username, string $file): ?string {
    if (!file_exists($file)) return null;
    $fh = fopen($file, 'r');
    if (!$fh) return null;
    $hash = null;
    while (($line = fgets($fh)) !== false) {
        $line = trim($line);
        if ($line === '') continue;
        // Format: username:hash
        $parts = explode(':', $line, 2);
        if (count($parts) === 2) {
            if ($parts[0] === $username) {
                $hash = $parts[1];
                break;
            }
        }
    }
    fclose($fh);
    return $hash;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $storedHash = get_user_hash($username, $USERS_FILE);
        if ($storedHash && password_verify($password, $storedHash)) {
            // Success: start session and redirect
            $_SESSION['username'] = $username;
            header('Location: protected.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
  </head>

  <body>
    <h1>Login</h1>

    <?php if ($error): ?>
      <div class="error">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="post" action="login.php" autocomplete="on">
      <label>
        Username
        <input type="text" name="username" required />
      </label>

      <label>
        Password
        <input type="password" name="password" required />
      </label>

      <div class="actions">
        <button type="submit">Login</button>
        <a href="register.php">Register</a>
      </div>
    </form>
  </body>
</html>


