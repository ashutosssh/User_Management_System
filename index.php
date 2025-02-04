<?php
require_once 'templates/header.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>

<div class="welcome-container">
    <h1>Welcome to the User Management System</h1>
    <p>Manage users efficiently with our secure system.</p>
    
    <div class="cta-buttons">
        <a href="register.php" class="button btn-register">Register</a>
        <a href="login.php" class="button btn-login">Login</a>
    </div>
    
    <div class="features">
        <h2>Features</h2>
        <ul>
            <li>✅ Secure user registration</li>
            <li>🔐 Password-protected login</li>
            <li>📝 Profile management</li>
            <li>🛡️ Account security features</li>
            <li>📱 Responsive design</li>
        </ul>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
