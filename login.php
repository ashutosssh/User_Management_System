<?php
require_once 'db.php';
require_once 'templates/header.php';

$errors = [];
$login_attempts = $_SESSION['login_attempts'] ?? 0;

if ($login_attempts >= 5) {
    $errors[] = 'Too many login attempts. Please try again in 15 minutes.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $login_attempts < 5) {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }
    
    // Sanitize inputs
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Validation
    if (empty($username)) $errors[] = 'Username is required';
    if (empty($password)) $errors[] = 'Password is required';
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Successful login
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['login_attempts'] = 0;
                header('Location: dashboard.php');
                exit();
            } else {
                $_SESSION['login_attempts'] = $login_attempts + 1;
                $errors[] = 'Invalid credentials';
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $errors[] = 'Login failed. Please try again.';
        }
    }
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<div class="form-container">
    <h2>Login</h2>
    <?php foreach ($errors as $error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>
    
    <?php if ($login_attempts > 0): ?>
        <div class="error">Login attempts remaining: <?= 5 - $login_attempts ?></div>
    <?php endif; ?>
    
    <form id="loginForm" method="post">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        
        <div class="form-group">
            <label>Username:</label>
            <input type="text" name="username" required>
        </div>
        
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        
        <button type="submit">Login</button>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
