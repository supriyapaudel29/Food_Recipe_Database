<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

require_login();

// user मात्र allow
if ($_SESSION['role'] !== 'user') {
    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Food Recipe Database</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<h1>Food Recipe Database</h1>
<nav>
    <a href="user_index.php">Home</a> |
    <button><a href="logout.php">Logout</a></button>
</nav>
<hr>

<h2>All Recipes</h2>

<form method="get">
    <input type="text" name="q" placeholder="Search recipe..."
           value="<?php echo e($_GET['q'] ?? ''); ?>">
    <button type="submit">Search</button>
</form>

<table>
<tr>
    <th>Title</th>
    <th>Cuisine</th>
    <th>Difficulty</th>
    <th>Cooking Time</th>
    <th>View</th>
</tr>

<?php
$search = $_GET['q'] ?? '';

if ($search !== '') {
    $stmt = $pdo->prepare(
        "SELECT * FROM recipes WHERE title LIKE :search ORDER BY id ASC"
    );
    $stmt->execute([':search' => "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM recipes ORDER BY id ASC");
}

$recipes = $stmt->fetchAll();

if ($recipes) {
    foreach ($recipes as $row) {
        echo "<tr>";
        echo "<td>" . e($row['title']) . "</td>";
        echo "<td>" . e($row['cuisine']) . "</td>";
        echo "<td>" . e($row['difficulty']) . "</td>";
        echo "<td>" . e($row['cooking_time']) . "</td>";
        echo "<td>
                <a href='user_view.php?id={$row['id']}'>View</a>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>No recipes found</td></tr>";
}
?>
</table>

<?php include '../includes/footer.php'; ?>
