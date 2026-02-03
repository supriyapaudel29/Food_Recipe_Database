<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_login();

include '../includes/header.php';

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Invalid Recipe ID");
}

$recipe_id = (int)$_GET['id'];

// Fetch recipe
$stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = :id");
$stmt->execute([':id' => $recipe_id]);
$recipe = $stmt->fetch();

if(!$recipe){
    die("Recipe not found");
}

$errors = [];

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    if(!verify_csrf($_POST['csrf_token'] ?? '')){
        $errors[] = "Invalid CSRF token.";
    }

    $title = trim($_POST['title'] ?? '');
    $cuisine = trim($_POST['cuisine'] ?? '');
    $difficulty = $_POST['difficulty'] ?? '';
    $cooking_time = (int)($_POST['cooking_time'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $ingredients = trim($_POST['ingredients'] ?? '');
    $instructions = trim($_POST['instructions'] ?? '');

    //  IMAGE PATH (default old one)
    $imagePath = $recipe['image'];

    if(empty($title) || empty($cuisine) || empty($difficulty) || empty($cooking_time) || empty($ingredients) || empty($instructions)){
        $errors[] = "All fields are required.";
    }

    //  IMAGE VALIDATION (only if new image chosen)
    if(isset($_FILES['image']) && $_FILES['image']['error'] === 0){

        $allowed = ['jpg','jpeg','png','webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if(!in_array($ext, $allowed)){
            $errors[] = "Only JPG, PNG, WEBP images allowed.";
        }

        if($_FILES['image']['size'] > 2 * 1024 * 1024){
            $errors[] = "Image must be under 2MB.";
        }
    }

    if(empty($errors)){

        //  FORMAT INSTRUCTIONS
        $raw_steps = explode("\n", $instructions);
        $formatted_steps = "";
        $i = 1;
        foreach($raw_steps as $step){
            $step = preg_replace('/^\d+\.\s*/', '', trim($step));
            if($step !== ""){
                $formatted_steps .= $i . ". " . $step . "\n";
                $i++;
            }
        }

        //  IMAGE UPLOAD IF NEW IMAGE
        if(isset($_FILES['image']) && $_FILES['image']['error'] === 0){

            // delete old image
            if(!empty($recipe['image']) && file_exists("../".$recipe['image'])){
                unlink("../".$recipe['image']);
            }

            $newName = preg_replace('/[^a-z0-9]/i','_', strtolower($title))
                       . "." . $ext;

            $uploadDir = "../public/images/recipes/";
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir.$newName);

            $imagePath = "public/images/recipes/".$newName;
        }

        //  UPDATE QUERY WITH IMAGE
        $stmt_update = $pdo->prepare("
            UPDATE recipes
            SET title=:title, cuisine=:cuisine, difficulty=:difficulty,
                cooking_time=:cooking_time, description=:description,
                ingredients=:ingredients, instructions=:instructions,
                image=:image
            WHERE id=:id
        ");

        $stmt_update->execute([
            ':title' => $title,
            ':cuisine' => $cuisine,
            ':difficulty' => $difficulty,
            ':cooking_time' => $cooking_time,
            ':description' => $description,
            ':ingredients' => $ingredients,
            ':instructions' => $formatted_steps,
            ':image' => $imagePath,
            ':id' => $recipe_id
        ]);

        header("Location: view.php?id=$recipe_id");
        exit;
    }
}
?>

<h2>Edit Recipe</h2>

<?php if(!empty($errors)): ?>
<div style="color:red">
    <?php foreach($errors as $err) echo e($err)."<br>"; ?>
</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

    <label>Title:</label><br>
    <input type="text" name="title" required value="<?php echo e($_POST['title'] ?? $recipe['title']); ?>"><br><br>

    <label>Cuisine:</label><br>
    <input type="text" name="cuisine" required value="<?php echo e($_POST['cuisine'] ?? $recipe['cuisine']); ?>"><br><br>

    <label>Difficulty:</label><br>
    <select name="difficulty" required>
        <option <?php if(($_POST['difficulty'] ?? $recipe['difficulty'])=="Easy") echo "selected"; ?>>Easy</option>
        <option <?php if(($_POST['difficulty'] ?? $recipe['difficulty'])=="Medium") echo "selected"; ?>>Medium</option>
        <option <?php if(($_POST['difficulty'] ?? $recipe['difficulty'])=="Hard") echo "selected"; ?>>Hard</option>
    </select><br><br>

    <label>Cooking Time (minutes):</label><br>
    <input type="number" name="cooking_time" required value="<?php echo e($_POST['cooking_time'] ?? $recipe['cooking_time']); ?>"><br><br>


    <!-- NEW IMAGE -->
    <label>Change Image (optional):</label><br>
    <input type="file" name="image" accept="image/*"><br><br>

    <label>Ingredients:</label><br>
    <input type="text" name="ingredients" required value="<?php echo e($_POST['ingredients'] ?? $recipe['ingredients']); ?>"><br><br>

    <label>Cooking Instructions:</label><br>
    <textarea name="instructions" rows="10" required><?php echo e($_POST['instructions'] ?? $recipe['instructions']); ?></textarea><br><br>

    <label>Description:</label><br>
    <textarea name="description" rows="5" required><?php echo e($_POST['description'] ?? $recipe['description']); ?></textarea><br><br>

    <button type="submit">Update Recipe</button>
</form>

<nav>
    <a href="index.php">Back</a>
</nav>


<?php include '../includes/footer.php'; ?>
