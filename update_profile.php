<?php
require_once 'db.php';
require_once 'templates/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$errors = [];
$success = '';

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Profile update error: " . $e->getMessage());
    die("Error loading user data");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }
    
    // Sanitize inputs
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone_number = trim($_POST['phone_number']);
    $new_password = trim($_POST['new_password']);

    // Validation
    if (empty($email)) $errors[] = 'Email is required';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format';
    if (empty($first_name)) $errors[] = 'First name is required';
    if (empty($last_name)) $errors[] = 'Last name is required';
    if (empty($phone_number)) {
        $errors[] = 'Phone number is required';
    } elseif (!preg_match('/^\+?[0-9]{10,15}$/', $phone_number)) {
        $errors[] = 'Invalid phone number format';
    }

    // Validate new password only if provided
    if (!empty($new_password)) {
        if (strlen($new_password) < 8) $errors[] = 'Password must be at least 8 characters';
        if (!preg_match('/[A-Z]/', $new_password)) $errors[] = 'Password must contain at least one uppercase letter';
        if (!preg_match('/[a-z]/', $new_password)) $errors[] = 'Password must contain at least one lowercase letter';
        if (!preg_match('/[0-9]/', $new_password)) $errors[] = 'Password must contain at least one number';
    }

    // Check if email already exists for another user
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        if ($stmt->rowCount() > 0) {
            $errors[] = 'Email is already registered';
        }
    } catch (PDOException $e) {
        error_log("Email check error: " . $e->getMessage());
        $errors[] = 'Error checking email availability';
    }

    if (empty($errors)) {
        try {
            // Base SQL statement
            $sql = "UPDATE users SET email = ?, first_name = ?, last_name = ?, phone_number = ?";
            $params = [$email, $first_name, $last_name, $phone_number];

            // Only update password if a new one is provided
            if (!empty($new_password)) {
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $sql .= ", password_hash = ?";
                $params[] = $password_hash;
            }

            // Add WHERE clause
            $sql .= " WHERE id = ?";
            $params[] = $_SESSION['user_id'];

            // Execute the query
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            $success = 'Profile updated successfully!';
        } catch (PDOException $e) {
            error_log("Update error: " . $e->getMessage());
            $errors[] = 'Failed to update profile';
        }
    }
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<div class="form-container">
    <h2>Update Profile</h2>
    <?php foreach ($errors as $error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>
    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" required 
                   value="<?= htmlspecialchars($user['email']) ?>">
        </div>
        
        <div class="form-group">
            <label>First Name:</label>
            <input type="text" name="first_name" required
                   value="<?= htmlspecialchars($user['first_name']) ?>">
        </div>
        
        <div class="form-group">
            <label>Last Name:</label>
            <input type="text" name="last_name" required
                   value="<?= htmlspecialchars($user['last_name']) ?>">
        </div>

        <div class="form-group">
            <label>Phone Number:</label>
            <input type="text" name="phone_number" required
                   pattern="^\+?[0-9]{10,15}$"
                   title="Enter a valid phone number (10-15 digits, optional '+')"
                   value="<?= htmlspecialchars($user['phone_number']) ?>">
        </div>
        
        <div class="form-group">
            <label>New Password:</label>
            <input type="password" name="new_password"
                   pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$"
                   title="At least 8 characters with uppercase, lowercase, and number">
            <div class="password-strength"></div>
        </div>
        
        <button type="submit">Update Profile</button>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
