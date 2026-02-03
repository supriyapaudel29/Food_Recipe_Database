<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_login(); // Only logged-in users

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Invalid Restaurant ID");
}

$id = (int)$_GET['id'];

// Fetch restaurant for confirmation
$stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id = :id");
$stmt->execute([':id' => $id]);
$restaurant = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$restaurant){
    die("Restaurant not found");
}

// Show confirmation form if GET request
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    include '../includes/header.php';
    ?>
    <h2>Delete Restaurant</h2>
    <p>Are you sure you want to delete the restaurant <b><?php echo e($restaurant['name']); ?></b>?</p>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <button type="submit">Yes, Delete</button>
        <a href="view_restaurant.php" style="margin-left:10px;">Cancel</a>
    </form>
    <?php
    include '../includes/footer.php';
    exit;
}

// POST request: delete
if(!verify_csrf($_POST['csrf_token'] ?? '')){
    die("Invalid CSRF token.");
}

// Delete restaurant
$stmt_del = $pdo->prepare("DELETE FROM restaurants WHERE id = :id");
$stmt_del->execute([':id' => $id]);

// Redirect back to restaurant list
header("Location: view_restaurant.php");
exit;
