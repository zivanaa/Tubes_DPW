<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include "header.php";

// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "db_itsave");

// Check connection
if (mysqli_connect_errno()) {
    echo "<script>alert('Koneksi database gagal: " . mysqli_connect_error() . "');</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all users from the database
$query = "SELECT id, name, username, profile_image FROM users";
$result = mysqli_query($koneksi, $query);

// Check if query execution was successful
if (!$result) {
    echo "<script>alert('Error: " . mysqli_error($koneksi) . "');</script>";
    exit();
}

// Fetch user's follow data
$follow_query = "SELECT following_id FROM follows WHERE follower_id = $user_id";
$follow_result = mysqli_query($koneksi, $follow_query);

// Check if query execution was successful
if (!$follow_result) {
    echo "<script>alert('Error: " . mysqli_error($koneksi) . "');</script>";
    exit();
}

$following_ids = [];
while ($row = mysqli_fetch_assoc($follow_result)) {
    $following_ids[] = $row['following_id'];
}
?>

<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<div class="container-fluid" style="margin-top: 15px; display: flex; justify-content: center;">
    <div class="row" style="width: 100%; max-width: 1200px;">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped table-dark">
                    <thead>
                        <tr>
                            <th>Profile Image</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>
                                    <img src="<?= !empty($row['profile_image']) ? htmlspecialchars($row['profile_image']) : 'assets/profile/none.png' ?>" class="rounded-circle" alt="User Image" style="width: 50px; height: 50px;">
                                </td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td>
                                    <?php if ($row['id'] != $user_id): ?>
                                        <form method="post" action="follow_action.php" style="display: inline;">
                                            <input type="hidden" name="following_id" value="<?= $row['id'] ?>">
                                            <?php if (in_array($row['id'], $following_ids)): ?>
                                                <button type="submit" class="btn btn-danger" name="action" value="unfollow">Unfollow</button>
                                            <?php else: ?>
                                                <button type="submit" class="btn btn-primary" name="action" value="follow">Follow</button>
                                            <?php endif; ?>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
