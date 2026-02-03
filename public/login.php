<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// // If already logged in, redirect based on role
// if (is_logged_in()) {
//     if ($_SESSION['role'] === 'admin') {
//         header("Location: index.php");
//     } else {
//         header("Location: user_index.php");
//     }
//     exit;
// }

// Handle login form submission
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = "Invalid CSRF token.";
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? '';

    if (empty($username) || empty($password) || empty($role)) {
        $errors[] = "All fields are required.";
    }

    if (empty($errors)) {

    if ($role === 'admin') {
        // ADMIN TABLE
        $stmt = $pdo->prepare(
            "SELECT * FROM admins WHERE username = :username"
        );
    } else {
        // USER TABLE
        $stmt = $pdo->prepare(
            "SELECT * FROM users WHERE username = :username"
        );
    }

    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {

        // Login success
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $role; // admin or user

        // Redirect
        if ($role === 'admin') {
            header("Location: index.php");       // admin page
        } else {
            header("Location: user_index.php");   // user page
        }
        exit;

    } else {
        $errors[] = "Invalid username or password.";
    }
}

}
?>

<head>
    <meta charset="UTF-8">
    <title>Food Recipe System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<h2>Login</h2>

<?php if (!empty($errors)): ?>
    <div style="color:red; margin-bottom:10px;">
        <?php foreach ($errors as $err) echo e($err) . "<br>"; ?>
    </div>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <label>Role:</label><br>
    <select name="role" required>
        <option value="">Select Role</option>
        <option value="admin" <?= (isset($_POST['role']) && $_POST['role']=='admin')?'selected':'' ?>>Admin</option>
        <option value="user" <?= (isset($_POST['role']) && $_POST['role']=='user')?'selected':'' ?>>User</option>
    </select><br><br>

    <button type="submit">Login</button>
</form>

<nav>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</nav>


<?php include '../includes/footer.php'; ?>
