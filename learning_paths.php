<?php
global $conn;
include 'header.php';
include 'database.php';

/**************************************** COOKIE ***************************************/
// ENABLE COOKIE
if (!isset($_COOKIE['test_cookie'])) {
    setcookie('test_cookie', 'test', time() + 3600); // one-hour cookie
}

/* Redirect non-logged-in users to a different page - the login page */
if (!isset($_SESSION['loggedIn']) || !$_SESSION['loggedIn']) {
    header("Location: login.php"); // Redirect to login page or index.php
    exit;
}

/**************************************** CREATE PATHWAYS ***************************************/

// FORM INPUT
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submitCreate'])) {
        // Create post form was submitted
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $userId = $_SESSION['userId'] ?? 1;  // Ensure this gets a valid user ID
        $author = $_POST['author'] ?? '';
        $url = $_POST['url'] ?? '';

        if (!empty($title) && !empty($description) && !empty($author) && !empty($url)) {
            savePathway($conn, $title, $description, $userId, $author, $url);
        } else {
            echo "Please fill in all of the form fields";
        }
    }

    // Check if search form is submitted
    if (isset($_POST['submitSearch'])) {
        // Only attempt to use 'search' and 'query' if they exist
        $field = $_POST['search'] ?? '';
        $keyword = $_POST['query'] ?? '';

        if (!empty($field) && !empty($keyword)) {
            searchPathways($field, $keyword);
        } else {
            echo "There is a search field that is invalid";
        }
    }

    // Handle upvote and downvote submissions
    if (isset($_POST['submitUpvote'])) {
        $pathId = $_POST['upvotePathId'];
        $username = $_SESSION['username'];
        upVote($username, $pathId, $conn);
    } elseif (isset($_POST['submitDownvote'])) {
        $pathId = $_POST['downvotePathId'];
        $username = $_SESSION['username'];
        downVote($username, $pathId, $conn);
    }
}

// SAVING PATHWAY
function savePathway($conn, $title, $description, $userId, $author, $url): void {
    $title = htmlspecialchars($title);
    $description = htmlspecialchars($description);
    $author = htmlspecialchars($author);
    $url = htmlspecialchars($url);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert into learning_paths
        $stmt = $conn->prepare("INSERT INTO learning_paths(user_id, title, description, created_at, author, url) VALUES (?, ?, ?, NOW(), ?, ?)");
        $stmt->bind_param("issss", $userId, $title, $description, $author, $url);
        $stmt->execute();

        // Get the last inserted ID (assuming path_id is auto-incremented)
        $pathId = $stmt->insert_id;

        // Commit transaction
        $conn->commit();
        echo "Learning Pathway Created!";
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        echo "Error: " . $exception->getMessage();
    }
}


/**************************************** UPVOTE/DOWNVOTE ***************************************/
function upVote($userId, $pathId, $conn)
{
    $stmt = $conn->prepare(
        "INSERT INTO votes (user_id, path_id, vote_value, created_at)
         VALUES (?, ?, 'upvote', NOW())");
    $stmt->bind_param("ii", $userId, $pathId);
    $stmt->execute();
}

function downVote($userId, $pathId, $conn)
{
    $stmt = $conn->prepare(
        "INSERT INTO votes (user_id, path_id, vote_value, created_at)
         VALUES (?, ?, 'downvote', NOW())");
    $stmt->bind_param("ii", $userId, $pathId);
    $stmt->execute();
}

/**************************************** HIGHTED VOTED PATHWAY ***************************************/
function getHighestVotedPath($conn)
{
    $query = "SELECT path_id, title, description, created_at, author, COUNT(*) AS vote_count
              FROM learning_paths
              LEFT JOIN votes ON learning_paths.path_id = votes.path_id
              WHERE vote_value = 'upvote'
              GROUP BY learning_paths.path_id
              ORDER BY vote_count DESC
              LIMIT 1";

    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }

    return null;
}




/**************************************** SEARCHING PATHWAYS ***************************************/
function searchPathways($field, $keyword): void {
    global $conn;

    // Validation
    $validSearch = ['path_id', 'user_id', 'title', 'description', 'created_at', 'author'];
    if (!in_array($field, $validSearch)) {
        die("There is a search field that is invalid");
    }

    $sql = "SELECT path_id, title, description, created_at, author FROM learning_paths WHERE $field LIKE ?";
    $stmt = $conn->prepare($sql);
    $likeKeyword = "%$keyword%";
    $stmt->bind_param("s", $likeKeyword);

    $stmt->execute();
    $result = $stmt->get_result();

    // Display
    echo '<div class="search-results-container">';
    if ($result->num_rows > 0) {
        echo '<ul class="posts-list">';
        while ($row = $result->fetch_assoc()) {
            echo '<li class="post-item">';
            echo '<div class="post-content">';
            echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
            echo '<p>Description: ' . htmlspecialchars($row['description']) . '</p>';
            echo '<p>Author: ' . htmlspecialchars($row['author']) . '</p>';
            echo '<p>Created at: ' . htmlspecialchars($row['created_at']) . '</p>';
            echo '</div>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No results found.</p>';
    }
    echo '</div>';
}

/**************************************** Tracking Clicks on Pathways **************************************/
if (isset($_GET['pathId'])) {
    $pathId = $_GET['pathId'];

    // Update the click count in the database
    $updateClickCountQuery = "UPDATE learning_paths SET click_count = click_count + 1 WHERE path_id = $pathId";
    $conn->query($updateClickCountQuery);
}

?>

<!-------------------------------- PAGE CONTENT ----------------------------->
<div class="jump-button-container">
    <a href="#posts-container" class="jump-button">Jump to Learning Paths!</a>
</div>

<div class="learning-paths-container">
    <!-- Search Form -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <select name="search">
            <option value="path_id">Pathway ID: </option>
            <option value="title">Title: </option>
            <option value="description">Description: </option>
            <option value="created_at">Created At: </option>
            <option value="author">Author: </option>
        </select>
        <input type="text" name="query">
        <input type="submit" name="submitSearch" value="Search">
    </form>

    <!-- Create Post Form -->
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label>Title: </label><input type="text" name="title"><br><br>
        <label>Description: </label><textarea name="description"></textarea><br><br>
        <label>Author: </label><input type="text" name="author"><br><br>
        <label>URL: </label><input type="text" name="url"><br><br>
        <input type="submit" name="submitCreate" value="Create">
    </form>
</div>

<!-- Learning Paths -->
<div id="posts-container" class="posts-container">
    <h2>Learning Paths</h2>
    <?php
    // Query to fetch learning paths from the database
    $query = "SELECT path_id, title, description, created_at, author FROM learning_paths ORDER BY path_id DESC";
    $result = $conn->query($query);

    // Check for a successful query
    if (!$result) {
        die("Error: " . $conn->error);
    }

    // Display posts if there are entries in the database
    if ($result->num_rows > 0) {
        echo '<ul class="posts-list">';
        while ($row = $result->fetch_assoc()) {
            echo '<li class="post-item">';
            echo '<div class="post-content">';
            echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
            echo '<p>Description: ' . htmlspecialchars($row['description']) . '</p>';
            echo '<p>Author: ' . htmlspecialchars($row['author']) . '</p>';
            echo '<p>Created at: ' . htmlspecialchars($row['created_at']) . '</p>';

            // Upvote and Downvote buttons
            echo '<form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">';
            echo '<input type="hidden" name="upvotePathId" value="' . $row['path_id'] . '">';
            echo '<input type="hidden" name="downvotePathId" value="' . $row['path_id'] . '">';
            echo '<input type="submit" name="submitUpvote" value="Upvote">';
            echo '<input type="submit" name="submitDownvote" value="Downvote">';
            echo '</form>';

            echo '</div>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No learning paths found.</p>';
    }
    ?>
</div>

<?php include 'footer.php';?>
