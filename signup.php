<?php
// signup.php
session_start();
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_var($_POST['username'], FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        echo "All fields are required.";
        exit;
    }

    // Check if the email already exists
    $query = "SELECT COUNT(*) as count FROM users WHERE email = :email";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':email' => $email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
        echo "Email already exists.";
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $query = "INSERT INTO users (username, email, hashedpassword) VALUES (:username, :email, :hashedpassword)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':hashedpassword' => $hashedPassword
    ]);

    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Signup</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
      <h2>Signup</h2>
      <form method="POST" action="signup.php" id="signup-form">
          <label for="username">Username:</label>
          <input type="text" id="username" name="username" required><br><br>
  
          <label for="email">Email:</label>
          <input type="email" id="email" name="email" required><br><br>
  
          <label for="password">Password:</label>
          <input type="password" id="password" name="password" required><br><br>
  
          <button type="submit">Signup</button>
      </form>
      <p>Already have an account? <a href="login.php">Login here</a></p>
  </div>
  <script src="script.js"></script>
</body>
</html>
