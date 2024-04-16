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
/**************************************** SEARCHING PATHWAYS ***************************************/
/*
 * I worked with the examples that we were given in lab session [reference: form.html on Maz GitHub]
 * this was originally for a flat file, I am unsure if this will effectively function
 *
 * */

echo '<form action="searchPathway.php" method="POST">
    <select name="search">
        <option value="pathId">Pathway ID: </option>
        <option value="title">Title: </option>
        <option value="author"> Author: </option>
        <option value="description">Description: </option>
        <option value="resource_url">URL: </option>
    </select>
    <input type="text" name="query">
    <input type="submit" value="Search">
</form>';

//SEARCH
$field = $_POST['search'];
$query= '%' . $_POST['query'] . '%';

//HANDLING QUERY
$validSearch = ['pathId', 'title', 'author', 'description', 'resource_url'];
if(isset($_GET['search'])){
    $keyword = $_GET['search'];
}
//Validation
if(!in_array($field, $validSearch)){
    die("There is a search field that is invalid");
}

/* used PDO as per examples in lab, however this can change */
$pdo = new PDO('mysql:host=localhost; dbhost=learning_pathway', 'username', 'password');
$sql = "SELECT pathId, title, author, description, resource_url
        FROM learning_pathways
        WHERE title LIKE '% $keyword' OR descrition LIKE '%$keyword'";
$stmt=$pdo->prepare($sql);

$stmt->execute(['query' => $query]);

//DISPLAY
echo '<table>';
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    echo '<tr>
            <td>' . htmlspecialchars($row['pathId']) . '</td>
            <td>' . htmlspecialchars($row['title']) . '</td>
            <td>' . htmlspecialchars($row['author']) . '</td>
            <td>' . htmlspecialchars($row['description']) . '</td>
            <td>' . htmlspecialchars($row['resource_url']) . '</td>
            </tr>';
} echo '</table>';

