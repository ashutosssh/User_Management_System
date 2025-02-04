<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
require_once 'db.php';
require_once 'templates/header.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }
    
    // Sanitize inputs
    $username = trim($_POST['username']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone_number = trim($_POST['phone_number']);

    // Server-side validation
    if (empty($username)) $errors[] = 'Username is required';
    if (empty($email)) $errors[] = 'Email is required';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format';
    if (empty($password)) $errors[] = 'Password is required';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters';
    if (!preg_match('/[A-Z]/', $password)) $errors[] = 'Password must contain at least one uppercase letter';
    if (!preg_match('/[a-z]/', $password)) $errors[] = 'Password must contain at least one lowercase letter';
    if (!preg_match('/[0-9]/', $password)) $errors[] = 'Password must contain at least one number';
    if (empty($first_name)) $errors[] = 'First name is required';
    if (empty($last_name)) $errors[] = 'Last name is required';
    if (empty($phone_number)) {
        $errors[] = 'Phone number is required';
    } elseif (!preg_match('/^\+?[0-9]{10,15}$/', $phone_number)) {
        $errors[] = 'Invalid phone number format';
    }

    if (empty($errors)) {
        // Check for existing user
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->rowCount() > 0) {
                $errors[] = 'Username or email already exists';
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, first_name, last_name, phone_number) VALUES (?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$username, $email, $password_hash, $first_name, $last_name, $phone_number])) {
                    $success = 'Registration successful! Please login.';
                }
            }
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<div class="form-container">
    <h2>Register</h2>
    <?php foreach ($errors as $error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>
    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <form id="registerForm" method="post">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        
        <div class="form-group">
            <label>Username:</label>
            <input type="text" name="username" required 
                   pattern="[a-zA-Z0-9_]{4,20}" 
                   title="4-20 characters (letters, numbers, underscores)">
        </div>
        
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" required
                   minlength="8"
                   pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$"
                   title="At least 8 characters with uppercase, lowercase, and number">
            <div id="password-strength" class="password-strength"></div>
        </div>
        
        <div class="form-group">
            <label>First Name:</label>
            <input type="text" name="first_name" required>
        </div>
        
        <div class="form-group">
            <label>Last Name:</label>
            <input type="text" name="last_name" required>
        </div>

        <div class="form-group">
            <label>Phone Number:</label>
            <input type="text" name="phone_number" required
                   pattern="^\+?[0-9]{10,15}$"
                   title="Enter a valid phone number (10-15 digits, optional '+')">
        </div>
        
        <button type="submit">Register</button>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
