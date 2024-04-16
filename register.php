<?php include 'header.php'; ?>

<?php
// Avoid starting a new session if one is already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**************************************** USER REGISTRATION ***************************************/

$hostName = "localhost:3307";
$username = "admin";
$password = "password1";
$databaseName = "group12database";

$database = new mysqli($hostName, $username, $password, $databaseName);

if ($database->connect_error) {
    die("Connection failed to establish: " . $database->connect_error);
}

// REGISTRATION FORM HANDLING
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'], $_POST['email'], $_POST['password'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); //<---- hash for security

    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";

    $stmt = $database->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $password);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // if Successful upload, display success message
        echo "Registration successful! Welcome, $username.";
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$database->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
</head>
<body>

<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <!-- PHP_SELF <--- sends the form data to the page itself,
         -- will not jump to a different page
         -- the user will get an error message (if any) on the same page as the form -->

    <label>Username: </label><input type="text" name="username" required><br><br>
    <label>Email: </label><input type="email" name="email" required><br><br>
    <label>Password: </label><input type="password" name="password" required><br><br>
    <input type="submit" value="Register">
</form>

</body>
</html>

<?php include 'footer.php'; ?>
