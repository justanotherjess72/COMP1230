<?php
include 'database.php'; // connect to the database connection

// Start a session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/styles.css">
  <title>Learning Path Creator</title>
</head>

<header>
  <div class="header-container">
    <div class="logo">
      <img src="assets/images/logo.png" alt="Learning Path Creator Logo">
    </div>
    <nav>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="learning_paths.php">Learning Paths</a></li>
        <li><a href="index.php#about">About</a></li>
        <li><a href="contact.php">Contact</a></li>

        <?php if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']): ?>
        <!-- Dashboard Button -->
        <li><a href="dashboard.php">Dashboard</a></li>
        <!-- Logout Button -->
        <li><a href="logout.php">Logout</a></li>
    <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>
