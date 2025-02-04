<?php
require_once 'db.php';
require_once 'templates/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        
        session_destroy();
        header('Location: goodbye.php');
        exit();
    } catch (PDOException $e) {
        error_log("Account deletion error: " . $e->getMessage());
        die("Error deleting account. Please try again.");
    }
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<div class="form-container">
    <h2>Delete Account</h2>
    <p class="error">Warning: This action cannot be undone. All your data will be permanently deleted.</p>
    
    <form method="post" onsubmit="return confirm('Are you absolutely sure you want to delete your account?')">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <div class="form-group">
            <label>Enter your password to confirm:</label>
            <input type="password" name="confirm_password" required>
        </div>
        <button type="submit" class="danger">Permanently Delete Account</button>
    </form>
</div>
<?php require_once 'templates/footer.php'; ?>