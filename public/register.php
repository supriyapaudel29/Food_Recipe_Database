<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// // If already logged in, redirect to index
// if(is_logged_in()) {
//     header("Location: index.php");
//     exit;
// }

$errors = [];

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    if(!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = "Invalid CSRF token.";
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';

    if(empty($username) || empty($password) || empty($confirm_password) || empty($role)) {
        $errors[] = "All fields are required.";
    }

    if($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if(empty($errors)) {

        // Check username in correct table based on role
        if ($role === 'admin') {
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = :username");
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        }
        $stmt->execute([':username' => $username]);
        if($stmt->fetch()) {
            $errors[] = "Username already taken.";
        }
    }

    if(empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        if ($role === 'admin') {
            $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (:username, :password)");
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        }

        $stmt->execute([':username' => $username, ':password' => $hashed]);

        echo "<script>alert('Registration successful! Please login.'); window.location.href='login.php';</script>";
        exit;
    }
}
?>

<head>
    <meta charset="UTF-8">
    <title>Food Recipe System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<h2>Register</h2>

<?php if(!empty($errors)): ?>
    <div style="color:red; margin-bottom:10px;">
        <?php foreach($errors as $err) echo e($err)."<br>"; ?>
    </div>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

    <label>Username:</label><br>
    <input type="text" name="username" required value="<?php echo e($_POST['username'] ?? '') ?>"><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <label>Confirm Password:</label><br>
    <input type="password" name="confirm_password" required><br><br>

    <label>Role:</label><br>
    <select name="role" required>
        <option value="">Select Role</option>
        <option value="user" <?= (isset($_POST['role']) && $_POST['role']=='user')?'selected':'' ?>>User</option>
        <option value="admin" <?= (isset($_POST['role']) && $_POST['role']=='admin')?'selected':'' ?>>Admin</option>
    </select><br><br>

    <button type="submit">Register</button>
</form>

<nav>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</nav>

<?php include '../includes/footer.php'; ?>
