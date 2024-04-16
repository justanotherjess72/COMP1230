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
/**************************************** CREATE PATHWAYS ***************************************/

//FORM INPUT
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $title=$_POST['title'];
    $author=$_POST['author'];
    $description=$_POST['post'];
    $resource_url=$_POST['resource_url'];
}
//VALIDATING FORM INPUT
if(!empty($title) && !empty($author) && !empty($description) && !empty($resource_url)){
    savePathway($title, $author, $description, $resource_url);
}else{
    echo "Please fill in all of the form fields";
}

//SAVING PATHWAY
function SavePathway($title, $author, $description, $resource_url){

    //preventing sql injections
    global $database;
    $database = new mysqli($hostName, $title, $author, $description, $resource_url);
                            /*^----- server name */
    $title = mysqli_real_escape_string($database, $title);
    $author = mysqli_real_escape_string($database, $author);
    $description = mysqli_real_escape_string($database, $description);
    $resource_url= mysqli_real_escape_string($database, $resource_url);

    $sql = "INSERT INTO learning_pathways(title, author, description, resource_url)
            VALUES ($title, $author, $description, $resource_url)";

    if(mysqli_query($database, $sql)){
        echo "Learning Pathway Created!";
    }else{
        echo "Error: " . mysqli_error($database);
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<title>Create Pathway</title>
</head>
<body>

<form method = "POST" action="<?php php_echo: htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
 <!-- PHP_SELF <--- sends the form data to the page itself,
      -- will not jump to a different page
      -- user will get error message (if any) on the same page as form -->

    <label>Title: </label>              <input type="text" name="title"><br><br>
    <label>Author: </label>             <input type="text" name="author"><br><br>
    <label>Description: </label>        <textarea name="description"></textarea><br><br>
    <label>Resource URL: </label>       <input type="text" name="resource_url"><br><br>
     <!--button -->                     <input type="submit" value="Create">

</form>
</body>
</html>






