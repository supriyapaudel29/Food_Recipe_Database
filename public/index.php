<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Require login to access this page
require_login();

include '../includes/header.php';
?>


<h2>All Recipes</h2>

<form method="get">
    <input type="text" name="q" placeholder="Search recipe..." value="<?php echo e($_GET['q'] ?? ''); ?>">
    <button type="submit">Search</button>
</form>

<table>
<tr>
    <th>Title</th>
    <th>Cuisine</th>
    <th>Difficulty</th>
    <th>Cooking Time (min)</th>
    <th>Actions</th>
</tr>

<?php
$search = $_GET['q'] ?? '';

if($search !== ''){
    $stmt = $pdo->prepare("SELECT * FROM recipes WHERE title LIKE :search ORDER BY id ASC");
    $stmt->execute([':search' => "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM recipes ORDER BY id ASC");
}

$recipes = $stmt->fetchAll();

if(count($recipes) > 0){
    foreach($recipes as $row){
        echo "<tr>";
        echo "<td>".e($row['title'])."</td>";
        echo "<td>".e($row['cuisine'])."</td>";
        echo "<td>".e($row['difficulty'])."</td>";
        echo "<td>".e($row['cooking_time'])."</td>";
        echo "<td>
                <a href='view.php?id={$row['id']}'>View</a> | 
                <a href='edit.php?id={$row['id']}'>Edit</a> | 
                <a href='delete.php?id={$row['id']}' onclick='return confirm(\"Are you sure?\")'>Delete</a>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>No recipes found</td></tr>";
}
?>
</table>

<?php include '../includes/footer.php'; ?>
