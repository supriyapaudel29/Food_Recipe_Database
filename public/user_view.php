<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Invalid Recipe ID");
}

$recipe_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = :id");
$stmt->execute([':id' => $recipe_id]);
$recipe = $stmt->fetch();

if(!$recipe){
    die("Recipe not found");
}

// Split ingredients into array for better display
$ingredients = explode(',', $recipe['ingredients']);

// 🔹 IMAGE AUTO-GENERATE FROM TITLE
$imageName = strtolower(str_replace(' ', '_', $recipe['title'])) . '.jpg';
$imagePath = "../public/images/recipes/" . $imageName;

// 🔹 DEFAULT IMAGE IF NOT FOUND
if (!file_exists($imagePath)) {
    $imagePath = "../public/images/recipes/default.jpg";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo e($recipe['title']); ?> | Food Recipe</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<h1>Food Recipe Database</h1>

<nav>
    <a href="user_index.php">Home</a> |
    <button><a href="logout.php">Logout</a></button>
</nav>

<hr>

<h2><?php echo e($recipe['title']); ?></h2>

<!--  IMAGE DISPLAY -->
<img src="<?php echo $imagePath; ?>" 
     alt="<?php echo e($recipe['title']); ?>" 
     class="recipe-img">

<p><b>Cuisine:</b> <?php echo e($recipe['cuisine']); ?></p>
<p><b>Difficulty:</b> <?php echo e($recipe['difficulty']); ?></p>
<p><b>Cooking Time:</b> <?php echo e($recipe['cooking_time']); ?> min</p>
<p><b>Ingredients:</b> <?php echo e(implode(", ", $ingredients)); ?></p>
<p><b>Description:</b> <?php echo e($recipe['description']); ?></p>

<p><b>Cooking Instructions:</b></p>
<pre style="background:#f9f9f9; padding:10px; border:1px solid #ccc;">
<?php echo e($recipe['instructions']); ?>
</pre>

<nav>
    <button><a href="user_index.php">Back</a></button>
</nav>

<?php include '../includes/footer.php'; ?>

</body>
</html>
