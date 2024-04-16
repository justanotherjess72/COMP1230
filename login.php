<?php
include 'header.php';

// Start a new session if one is not already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**************************************** USER LOGIN ***************************************/
$hostName = "localhost:3307";
$username = "admin"; // Database username
$password = "password1"; // Database password
$databaseName = "group12database";

// Create database connection
$database = new mysqli($hostName, $username, $password, $databaseName);

// Check connection
if ($database->connect_error) {
    die("Connection failed to establish: " . $database->connect_error);
}

// LOGIN FORM HANDLING
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'], $_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // VALIDATE USER using prepared statement
    $stmt = $database->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Correct password, redirect to dashboard
            $_SESSION['loggedIn'] = true; // Set session variable
            $_SESSION['username'] = $row['username'];
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['bio'] = $row['bio'];

            // Debug: Print session variables
            echo "Debug - Session Variables: ";
            print_r($_SESSION);

            header("Location: dashboard.php");
            exit;
        } else {
            echo "Invalid password";
        }
    } else {
        echo "Invalid username";
    }

}

$database->close();
?>

<main class="container">
    <h1>Login</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>
    </form>
</main>

<?php include 'footer.php';?>
