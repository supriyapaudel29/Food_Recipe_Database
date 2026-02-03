<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_login(); // Only logged-in users

include '../includes/header.php';

$errors = [];

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    if(!verify_csrf($_POST['csrf_token'] ?? '')){
        $errors[] = "Invalid CSRF token.";
    }

    $name = trim($_POST['name'] ?? '');
    if(empty($name)){
        $errors[] = "Restaurant name is required.";
    }

    if(empty($errors)){
        $stmt = $pdo->prepare("INSERT INTO restaurants (name) VALUES (:name)");
        $stmt->execute([':name' => $name]);
        echo "<script>alert('Restaurant added successfully!'); window.location.href='view_restaurant.php';</script>";
        exit;
    }
}
?>

<h2>Add Restaurant</h2>

<?php if(!empty($errors)): ?>
    <div>
        <?php foreach($errors as $err) echo e($err)."<br>"; ?>
    </div>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

    <label>Name:</label><br>
    <input type="text" name="name" required value="<?php echo e($_POST['name'] ?? ''); ?>"><br><br>

    <button type="submit">Add Restaurant</button>
</form>

<?php include '../includes/footer.php'; ?>
