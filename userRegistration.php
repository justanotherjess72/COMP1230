<?php
session_start();

/**************************************** COOKIE ***************************************/
//ENABLE COOKIE
if(isset($_COOKIE['test_cookie'])){
    $cookieEnabled = true;
}else{
    $cookieEnabled = false;
    setCookie('test_cookie', 'test', time() + 3600); //<--- one hour cookie
}

/*to check if the user is already logged in. If user is logged in, they will return to their dashboard
if they are not they will be taken to the home page to register or login */

if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']){
    header("dashboard.php"); //<---- this will could change according to the filename structure we use
    exit;
}
header("home.php");


/* if user is logged in and decides to log out*/
if(isset($_GET['logout'])){
    if(isset($_COOKIE['test_cookie'])){
        setcookie('test_cookie', '', time() - 3600, "/"); //<---- destroying cookie
        session_destroy();
        header("index.php");
        exit;
    }
}

/**************************************** USER REGISTRATION ***************************************/

/*
I used mysqli for this, although we can change it to PDO depending on the group preference
*/

$hostName = "localhost:3307"; // Correct details
$username = "admin";
$password = "password1";
$databaseName = "group12database";
$database = new mysqli($hostName, $username, $password, $databaseName);

if ($database->connect_error) {
    die("Connection failed to establish: " . $database->connect_error);
}

// REGISTRATION FORM HANDLING
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email']; // Assuming you're handling emails
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Use prepared statement for security
    $stmt = $database->prepare("INSERT INTO users(username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        header("Location: login.php"); // Redirect to login after successful registration
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
$database->close();

?>
