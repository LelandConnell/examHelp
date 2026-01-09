<?php
// register.php — Add new user with hashed password

session_start();

$USERS_FILE = __DIR__ . '/users.txt';

// Helper: check if username exists
function user_exists(string $username, string $file): bool {
    if (!file_exists($file)) return false;
    $fh = fopen($file, 'r');
    if (!$fh) return false;
    $exists = false;
    while (($line = fgets($fh)) !== false) {
        $line = trim($line);
        if ($line === '') continue;
        $parts = explode(':', $line, 2);
        if (count($parts) === 2 && $parts[0] === $username) {
            $exists = true;
            break;
        }
    }
    fclose($fh);
    return $exists;
}

// Helper: add user atomically
function add_user(string $username, string $password, string $file): bool {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $entry = $username . ':' . $hash . PHP_EOL;

    $fh = fopen($file, 'a');
    if (!$fh) return false;
    if (flock($fh, LOCK_EX)) {
        fwrite($fh, $entry);
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
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($username === '' || $password === '' || $confirm === '') {
        $error = 'Please fill out all fields.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (!preg_match('/^[A-Za-z0-9_.-]{3,32}$/', $username)) {
        $error = 'Username must be 3–32 chars, letters/numbers/._- only.';
    } elseif (user_exists($username, $USERS_FILE)) {
        $error = 'Username is already taken.';
    } else {
        if (add_user($username, $password, $USERS_FILE)) {
            $success = 'Registration successful. You can now log in.';
        } else {
            $error = 'Could not register user. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="style.css">
    <title>Register</title>

  
  </head>

  <body>
    <h1>Register</h1>

    <?php if ($error): ?>
      <div class="error">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>

    <form method="post" action="register.php" autocomplete="off">
      <label>
        Username
        <input type="text" name="username" required />
      </label>

      <label>
        Password
        <input type="password" name="password" required />
      </label>

      <label>
        Confirm password
        <input type="password" name="confirm" required />
      </label>

      <div class="actions">
        <button type="submit">Create account</button>
        <a href="login.php">Back to login</a>
      </div>
    </form>
  </body>
</html>
