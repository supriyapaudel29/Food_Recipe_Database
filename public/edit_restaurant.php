<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
include '../includes/header.php';

if (!isset($_GET['id'])) die("Invalid ID");
$id = (int)$_GET['id'];

// Fetch current name
$stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id = :id");
$stmt->execute(['id' => $id]);
$restaurant = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$restaurant) die("Restaurant not found");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = e($_POST['name']);
    $stmt = $pdo->prepare("UPDATE restaurants SET name = :name WHERE id = :id");
    $stmt->execute(['name' => $name, 'id' => $id]);
    echo "<script>alert('Restaurant updated!'); window.location.href='view_restaurant.php';</script>";
    exit;
}
?>

<h2>Edit Restaurant</h2>
<form method="post">
    <label>Name:</label><br>
    <input type="text" name="name" value="<?php echo e($restaurant['name']); ?>" required><br><br>
    <button type="submit">Update Restaurant</button>
</form>

<?php include '../includes/footer.php'; ?>
