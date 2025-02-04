<?php
require_once 'db.php';
require_once 'templates/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    die("Error fetching user data.");
}

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>

<div class="dashboard-container">
    <h2>Welcome, <?= htmlspecialchars($user['first_name']) ?>!</h2>
    
<!-- Profile Picture Section -->
<div class="profile-picture">
    <?php 
    $profilePic = !empty($user['profile_picture']) ? "uploads/" . htmlspecialchars($user['profile_picture']) : "assets/default-profile.png";
    ?>
    <img src="<?= $profilePic ?>" alt="Profile Picture">
    
    <form action="upload_profile_picture.php" method="post" enctype="multipart/form-data">
        <input type="file" name="profile_picture" accept="image/*" required>
        <button type="submit">Upload Profile Picture</button>
    </form>
</div>

    
    <!-- User Information -->
    <div class="profile-info">
        <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Full Name:</strong> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></p>
        <p><strong>Member Since:</strong> <?= date('F j, Y', strtotime($user['created_at'])) ?></p>
        <p><strong>Phone Number:</strong> <?= htmlspecialchars($user['phone_number']) ?></p>
    </div>
    
    <!-- Actions -->
    <div class="actions">
        <a href="update_profile.php" class="button">Edit Profile</a>
        <a href="delete_account.php" class="button danger">Delete Account</a>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>