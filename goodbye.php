<?php
session_start();
require_once 'templates/header.php';
?>

<div class="form-container">
    <h2>Goodbye</h2>
    <p>Your account has been successfully deleted. We're sad to see you go!</p>
    <a href="login.php" class="button">Go to Login Page</a>
</div>

<?php require_once 'templates/footer.php'; ?>
