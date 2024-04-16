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
/**************************************** UPVOTE/DOWNVOTE ***************************************/
/*
 * There are two approaches to upvote and down-vote,
 * Should decide if we want to use PDO or MYSQLI for this -- just a preference
 * */

$pdo = new PDO('mysql:host=localhost:3307; dbname= group12database', 'admin', 'password1');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    /*^--  ATTR_ERRMODE : controls how PDO should handle errors
                           ERRMODE_EXCEPTION : throws a PDO EXCEPTION in case of an error*/
function upVote($username, $pathId, $pdo){
    $stmt = $pdo->prepare(
        "INSERT INTO votes (username, pathId, voteType)
         VALUES (?, ?, 'upvote')");
    $stmt->exeute([$username, $pathId]);
}

function downVote($username, $pathId, $pdo){
    $stmt = $pdo->prepare(
        "INSERT INTO votes (username, pathId, voteType)
         VALUES (?, ?, 'downVote')");
    $stmt->execute([$username, $pathId]);

}

