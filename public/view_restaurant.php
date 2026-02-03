<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
include '../includes/header.php';

// Fetch all restaurants
$stmt = $pdo->query("SELECT * FROM restaurants ORDER BY id ASC");
$restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>List of restaurants using our Database</h2>
<a href="add_restaurant.php">Add Restaurant</a> 


<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Actions</th>
    </tr>
    <?php foreach($restaurants as $rest): ?>
    <tr>
        <td><?php echo e($rest['id']); ?></td>
        <td><?php echo e($rest['name']); ?></td>
        <td>
            <a href="edit_restaurant.php?id=<?php echo $rest['id']; ?>">Edit</a> 
            <a href="delete_restaurant.php?id=<?php echo $rest['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include '../includes/footer.php'; ?>
