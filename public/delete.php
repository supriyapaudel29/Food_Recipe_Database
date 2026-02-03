<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_login(); // Only logged-in users

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Invalid Recipe ID");
}

$recipe_id = (int)$_GET['id'];

// If confirmation not sent via POST, show a confirmation form
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    include '../includes/header.php';
    ?>
    <h2>Delete Recipe</h2>
    <p>Are you sure you want to delete this recipe?</p>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <button type="submit">Yes, Delete</button>
        <h4><a href="view.php?id=<?php echo $recipe_id; ?>">Cancel</a></h4>
    </form>
    <?php
    include '../includes/footer.php';
    exit;
}

// POST request: actually delete
if(!verify_csrf($_POST['csrf_token'] ?? '')){
    die("Invalid CSRF token.");
}

// Delete recipe
$stmt = $pdo->prepare("DELETE FROM recipes WHERE id = :id");
$stmt->execute([':id' => $recipe_id]);

// Redirect to recipe list
header("Location: index.php");
exit;
