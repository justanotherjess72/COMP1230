<?php
global $conn;
session_start();

// Redirect user to login page if not logged in.
if (!isset($_SESSION['loggedIn']) || !$_SESSION['loggedIn']) {
    header('Location: login.php');
    exit;
}

include('header.php');
include('database.php'); // Assuming this file establishes a valid PDO connection and defines $conn

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveBio'])) {
    // Ensure a user is logged in
    if (isset($_SESSION['is_user']) && $_SESSION['is_user'] === true) {
        // Get the new user's bio from the form
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);
        $userBio = $data['userBio'];

        // Get the user ID from the session
        $userId = $_SESSION['user_id'];

        try {
            // Update the user's bio in the database
            $updateBioQuery = "UPDATE users SET bio = :userBio WHERE user_id = :userId";

            // Prepare the query
            $stmt = $conn->prepare($updateBioQuery);

            // Bind parameters
            $stmt->bindParam(':userBio', $userBio, PDO::PARAM_STR);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

            // Execute the query
            if ($stmt->execute()) {
                $rowCount = $stmt->rowCount();  // Get the number of affected rows
                if ($rowCount > 0) {
                    echo "Bio updated successfully! Rows affected: " . $rowCount;
                } else {
                    echo "Bio not updated. No rows affected.";
                }
            } else {
                echo "Error updating bio: ";
                print_r($stmt->errorInfo()); // Display detailed error information
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<main class="dashboard">
    <h1>Dashboard</h1>
    <h2>Welcome, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User'; ?>!</h2>
    <h2>You are user id #<?php echo isset($_SESSION['user_id']) ? htmlspecialchars($_SESSION['user_id']) : 'User'; ?>!</h2>
    <p class="bio-instruction">Here in the dashboard, you can upload a profile photo and write a mini-description (300 characters max).</p>

    <div class="profile-container">
        <form id="photo-upload-form" action="imageUpload.php" method="post" enctype="multipart/form-data">
            <div class="profile-photo-section">
                <?php if (isset($_SESSION['profile_photo'])): ?>
                    <img src="<?php echo htmlspecialchars($_SESSION['profile_photo']); ?>" alt="Profile Photo">
                <?php else: ?>
                    <img src="assets/images/default-profile.png" alt="Profile Photo">
                <?php endif; ?>
                <br>
                <p id="upload-button">Upload Photo</p>
                <input type="file" id="profile-photo-upload" name="profilePhoto" accept=".jpg, .jpeg, .png, .gif" onchange="document.getElementById('photo-upload-form').submit();">
            </div>
        </form>

        <div class="bio-section">
            <strong>Bio: </strong>
            <div id="bio-content"><?php echo isset($userBio) ? htmlspecialchars($userBio) : ''; ?></div>
            <textarea id="bio-edit" name="userBio"><?php echo isset($userBio) ? htmlspecialchars($userBio) : ''; ?></textarea>
            <div class="bio-button-group">
                <button id="edit-bio">Edit</button>
                <button id="save-bio" type="button" name="saveBio">Save</button>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var bioEdit = document.getElementById('bio-edit');
        var bioContent = document.getElementById('bio-content');
        var saveBioButton = document.getElementById('save-bio');

        // Edit button click handler
        document.getElementById('edit-bio').addEventListener('click', function() {
            bioEdit.style.display = 'block';
            bioContent.style.display = 'none';
        });

        // Save button click handler
        saveBioButton.addEventListener('click', function() {
            var userBio = bioEdit.value;

            // Send the bio to the server using Fetch API
            fetch('dashboard.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ saveBio: 1, userBio: userBio }),
            })
                .then(response => response.text())
                .then(data => {
                    // Display the server response
                    alert(data);

                    // Check if the response contains 'Bio updated successfully'
                    if (data.indexOf('Bio updated successfully') !== -1) {
                        // Update the displayed bio content
                        bioContent.innerHTML = userBio;

                        // Hide the textarea and show the content
                        bioEdit.style.display = 'none';
                        bioContent.style.display = 'block';
                    }
                })
                .catch(error => {
                    alert('Error updating bio: ' + error.message);
                });
        });
    });
</script>

<?php include('footer.php'); ?>
