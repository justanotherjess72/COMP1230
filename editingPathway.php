<?php
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
        header("home.php");
        exit;
    }
}
/**************************************** EDITING PATHWAYS ***************************************/


//GETTING PATHWAY VIA pathId <---- this will change depending on database


function getPathway($pathId){

    global $database;
    $database = new mysqli($pathId);
           $pathId = mysqli_real_escape_string($database, $pathId);
    $sql = "SELECT *
            FROM learning_pathways
            WHERE pathId = $pathId";

    $result= mysqli_query($database, $sql);

    if(mysqli_num_rows($result) > 0){
        return mysqli_fetch_assoc($result);
    }else{
        return "Pathway can not be found";
    }
}


//EDITING PATHWAY VIA pathId <---- this will change depending on database
function editPathway($pathId, $title, $author, $description, $resource_url){

    global $database;
    $database = new mysqli($pathId, $title, $author, $description, $resource_url);
                            /*^----- does this work, or does it have to be the server name? */
    $pathId = mysqli_real_escape_string($database, $pathId);
    $title = mysqli_real_escape_string($database, $title);
    $author = mysqli_real_escape_string($database, $author);
    $description = mysqli_real_escape_string($database, $description);
    $resource_url = mysqli_real_escape_string($database, $resource_url);


    $sql= "UPDATE learning_pathways
           SET title ='$title',
               author = '$author',
               description = '$description',
               resource_url = '$resource_url'
           WHERE pathID = '$pathId'";

    if(mysqli_query($database, $sql)){
        echo "Updated!";
    }else{
        echo "Error: " . mysqli_error($database);
    }
}