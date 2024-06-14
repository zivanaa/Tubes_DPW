<?php 
session_start(); // Start session at the beginning of the file

include "header.php"; // Include header file for HTML structure

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_itsave";

// Establishing MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Checking connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the follow/unfollow request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $user_id = $_SESSION['user_id'];
    $target_id = $_POST['target_id'];
    $action = $_POST['action'];

    // Prepare SQL statements
    if ($action == 'follow') {
        $sql_follow = "INSERT INTO followers (user_id, follower_id) VALUES ($target_id, $user_id)";
        $update_followers = "UPDATE users SET followers_count = followers_count + 1 WHERE id = $target_id";
        $update_following = "UPDATE users SET following_count = following_count + 1 WHERE id = $user_id";
    } elseif ($action == 'unfollow') {
        $sql_unfollow = "DELETE FROM followers WHERE user_id = $target_id AND follower_id = $user_id";

        // Execute unfollow query first
        if ($conn->query($sql_unfollow) === TRUE) {
            // Then update followers count
            $update_followers = "UPDATE users SET followers_count = followers_count - 1 WHERE id = $target_id";
            $update_following = "UPDATE users SET following_count = following_count - 1 WHERE id = $user_id";
        } else {
            echo "Error deleting follower: " . $conn->error;
            exit; // Exit script if there's an error
        }
    }

    // Execute SQL queries
    if ($conn->query($sql_follow) === TRUE || $conn->query($sql_unfollow) === TRUE) {
        $conn->query($update_followers);
        $conn->query($update_following);
        echo "success";
    } else {
        echo "Error: " . $conn->error;
    }

    // Exit after processing the POST request
    exit;
}


// Fetch user data from database
$user_id = 1; // Assuming you have a user ID to fetch data for a specific user
$sql = "SELECT name, username, bio, dashboard, followers_count, following_count FROM users WHERE id = $user_id";
$result = $conn->query($sql);

// Check if the query was successful
if ($result === false) {
    die("Error in SQL query: " . $conn->error);
}

// Fetch user data
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "No user found";
}

// Determine the follow/unfollow state based on session
if (isset($_SESSION['user_id'])) {
    $followed = false;
    $check_follow_sql = "SELECT * FROM followers WHERE user_id = $user_id AND follower_id = {$_SESSION['user_id']}";

    // Execute the query and handle errors
    $result_check_follow = $conn->query($check_follow_sql);
    if ($result_check_follow === false) {
        die("Error in SQL query: " . $conn->error);
    }

    if ($result_check_follow->num_rows > 0) {
        $followed = true;
    }
} else {
    echo 'Follow';
}

// Close the database connection
$conn->close();
?>

<!-- HTML content -->
<style>
    .dashboard {
        background-color: #BBD4E0;
        border-radius: 5px;
        padding: 15px;
        margin-top: 20px;
        text-align: left;
    }

    .profile-stats {
        display: flex;
        justify-content: space-around;
        margin: 20px 0;
    }

    .profile-stats div {
        text-align: center;
    }

    .profile-stats div span {
        display: block;
        font-size: 18px;
        font-weight: bold;
    }
</style>

<div class="profile">
    <div class="user-info">
        <img src="assets/img/gambar.png" alt="Avatar" class="avatar">
        <div>
            <div class="profile-stats">
                <div>
                    <span>45</span>
                    Posts
                </div>
                <div>
                    <span><?php echo $user['followers_count']; ?></span>
                    Followers
                </div>
                <div>
                    <span><?php echo $user['following_count']; ?></span>
                    Following
                </div>
            </div>
            <button id="follow-btn" data-user-id="<?php echo $user_id; ?>"
                style="background-color: #87CEFA; color: #11174F; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; margin-top: 10px; font-size: 14px; margin-left: 110px">
                <?php echo $followed ? 'Unfollow' : 'Follow'; ?>
            </button>
            <button style="background-color: #87CEFA; color: #11174F; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; margin-top: 10px; font-size: 14px; margin-left: 100px">Message</button>
            <?php if (!empty($user)): ?>
            <h4><?php echo htmlspecialchars($user['name']); ?></h4>
            <p>@<?php echo htmlspecialchars($user['username']); ?></p>
            <div class="profile-bio">
                <p><?php echo htmlspecialchars($user['bio']); ?></p>
                <a href="#">See Translation</a>
            </div>
            <div class="dashboard">
                <p style="color: #0C0C0C">Professional dashboard</p>
                <div class="profile-links">
                    <a href="<?php echo htmlspecialchars($user['dashboard']); ?>"><?php echo htmlspecialchars($user['dashboard']); ?></a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <br>
    </div>
    <div style="clear: both;"></div>
    <h3>Tweets:</h3>
</div>

<div class="container-fluid" style="margin-top: 15px; display: flex; justify-content: center;">
    <div class="row" style="width: 100%; max-width: 2500px;">
        <div class="col-md-6 feed" style="margin: 0 auto;">
            <div class="post" style="border: 1px solid #ddd; padding: 15px; border-radius: 10px; background-color: #11174F; color: white;">
                <div class="d-flex">
                    <img src="https://via.placeholder.com/50" class="rounded-circle" alt="User Image">
                    <div class="ms-3">
                        <h5 class="mb-0">User Name</h5>
                        <small style="color: #fff">@username</small>
                    </div>
                </div>
                <p class="mt-3">This is a sample post content. It can be a tweet, an update, or anything you want to share with your followers.</p>
                <div class="d-flex justify-content-between" style="color: white;">
                    <a href="#" style="color: white;">
                        <div class="post" data-post-id="7712">
                            <div class="post-ratings-container">
                                <div class="post-rating">
                                    <span class="post-rating-button material-icons">thumb_up</span>
                                    <span class="post-rating-count">0</span>
                                </div>
                            </div>
                        </div>
                    </a>
                    <a href="#" style="color: white;">
                        <div class="post" data-post-id="7712">
                            <div class="post-ratings-container">
                                <div class="post-rating">
                                    <span class="post-rating-button material-icons">thumb_down</span>
                                    <span class="post-rating-count">0</span>
                                </div>
                            </div>
                        </div>
                    </a>
                    <a href="#" style="color: white;">
                        <img src="assets/img/gambar9.png" alt="Gambar 3" style="width: 20px; height: 30px;">
                        0
                    </a>
                    <a href="#" style="color: white;">
                        <img src="assets/img/gambar3.png" alt="Gambar 4" style="width: 20px; height: 20px;">
                        0
                    </a>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
</div>
</div>
<nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center">
        <li class="page-item disabled">
            <a class="page-link">Previous</a>
        </li>
        <li class="page-item"><a class="page-link" href="#">1</a></li>
        <li class="page-item"><a class="page-link" href="#">2</a></li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>
        <li class="page-item">
            <a class="page-link" href="#">Next
            </a>
        </li>
        <li class="page-item">
            <a class="page-link" href="#">Next</a>
        </li>
    </ul>
</nav>
<?php include "footer.php"; ?>
<script>
    document.getElementById('follow-btn').addEventListener('click', function () {
        var action = this.textContent.trim() === 'Follow' ? 'follow' : 'unfollow';
        var targetId = this.getAttribute('data-user-id');

        fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'target_id=' + targetId + '&action=' + action
        })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    var followersCount = document.querySelector('.profile-stats div:nth-child(2) span');
                    var newCount = parseInt(followersCount.textContent);

                    if (action === 'follow') {
                        newCount++;
                    } else {
                        newCount--;
                    }

                    followersCount.textContent = newCount;
                    this.textContent = action === 'follow' ? 'Unfollow' : 'Follow';
                } else {
                    alert('Error: ' + data);
                }
            })
            .catch(error => console.error('Error:', error));
    });
</script>
