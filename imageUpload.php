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

// Check if the user is already logged in
if (!isset($_SESSION['is_user']) || $_SESSION['is_user'] !== true) {
    header('Location: login.php');
    exit;
}

/* if user is logged in and decides to log out*/
if(isset($_GET['logout'])){
    if(isset($_COOKIE['test_cookie'])){
        setcookie('test_cookie', '', time() - 3600, "/"); //<---- destroying cookie
        session_destroy();
        header("home.php");
        exit;
    }
}
/**************************************** IMAGE UPLOAD ***************************************/
echo '<form action="imageUpload.php" method="POST" enctype="multipart/form-data">
    Choose the image you would like to upload:
    <input type = "file" name="userImage" id=" ">
    <input type="submit" value="upload">
</form>';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uploadDir = "images/";
    $uploadFile = $uploadDir . basename($_FILES['userImage']['name']);

    // VALIDATE AND MOVE TO TARGETED DIRECTORY WITHIN DATABASE
    $allowedExtensions = ['jpg', 'jpeg', 'png'];  // Add more if needed
    $fileExtension = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

    if (in_array($fileExtension, $allowedExtensions)) {
        if (move_uploaded_file($_FILES['userImage']['tmp_name'], $uploadFile)) {
            echo "Your picture " . htmlspecialchars(basename($_FILES['userImage']['name'])) . " has been uploaded!";
        } else {
            echo "There was an error uploading this picture. Check the format.";
        }
    } else {
        echo "Invalid file format. Allowed formats: " . implode(', ', $allowedExtensions);
    }
}



