<?php
// login.php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => true,
    'httponly' => true, 
    'samesite' => 'Strict'
]);

session_start();
require 'database.php';
include('auth.php');

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (authenticateUser($email, $password)) {
        session_regenerate_id(true);
        header('Location: index.php'); 
        exit;
    } else {
        $error_message = 'Invalid credentials!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
      <h2>Login</h2>
      <?php if ($error_message): ?>
          <p class="error"><?php echo $error_message; ?></p>
      <?php endif; ?>
      <form method="POST" action="login.php" id="login-form">
          <label for="email">Email:</label>
          <input type="email" id="email" name="email" required><br><br>
  
          <label for="password">Password:</label>
          <input type="password" id="password" name="password" required><br><br>
  
          <button type="submit">Login</button>
      </form>
      <p>Don't have an account? <a href="signup.php">Signup here</a></p>
  </div>
  <script src="script.js"></script>
</body>
</html>
