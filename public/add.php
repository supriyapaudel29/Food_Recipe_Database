<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_login();

include '../includes/header.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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

    // NEW: image
    $imagePath = null;

    if(empty($title) || empty($cuisine) || empty($difficulty) || empty($cooking_time) || empty($ingredients) || empty($instructions)){
        $errors[] = "All fields are required.";
    }

    //  NEW: IMAGE VALIDATION
    if(isset($_FILES['image']) && $_FILES['image']['error'] === 0){

        $allowed = ['jpg','jpeg','png','webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if(!in_array($ext, $allowed)){
            $errors[] = "Only JPG, PNG, WEBP images are allowed.";
        }

        if($_FILES['image']['size'] > 2 * 1024 * 1024){
            $errors[] = "Image size must be less than 2MB.";
        }

    } else {
        $errors[] = "Recipe image is required.";
    }

    if(empty($errors)) {

        //  FORMAT INSTRUCTIONS
        $raw_steps = explode("\n", $instructions);
        $formatted_steps = "";
        $i = 1;
        foreach($raw_steps as $step){
            $step = trim($step);
            if($step !== "") {
                $formatted_steps .= $i . ". " . $step . "\n";
                $i++;
            }
        }

        //  IMAGE UPLOAD
        $newName = preg_replace('/[^a-z0-9]/i','_', strtolower($title)) . "." . $ext;
        $uploadDir = "../public/images/recipes/";
        $fullPath = $uploadDir . $newName;

        move_uploaded_file($_FILES['image']['tmp_name'], $fullPath);

        // path saved to DB (relative)
        $imagePath = "public/images/recipes/" . $newName;

        //  INSERT WITH IMAGE
        $stmt = $pdo->prepare("
            INSERT INTO recipes 
            (title, cuisine, difficulty, cooking_time, description, ingredients, instructions, image)
            VALUES 
            (:title, :cuisine, :difficulty, :cooking_time, :description, :ingredients, :instructions, :image)
        ");

        $stmt->execute([
            ':title' => $title,
            ':cuisine' => $cuisine,
            ':difficulty' => $difficulty,
            ':cooking_time' => $cooking_time,
            ':description' => $description,
            ':ingredients' => $ingredients,
            ':instructions' => $formatted_steps,
            ':image' => $imagePath
        ]);

        echo "<script>alert('Recipe added successfully!'); window.location.href='index.php';</script>";
        exit;
    }
}
?>

<h2>Add Recipe</h2>

<?php if(!empty($errors)): ?>
    <div>
        <?php foreach($errors as $err) echo e($err)."<br>"; ?>
    </div>
<?php endif; ?>

<!-- enctype REQUIRED -->
<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

    <label>Title:</label><br>
    <input type="text" name="title" required><br><br>

    <label>Cuisine:</label><br>
    <input type="text" name="cuisine" required><br><br>

    <label>Difficulty:</label><br>
    <select name="difficulty" required>
        <option>Easy</option>
        <option>Medium</option>
        <option>Hard</option>
    </select><br><br>

    <label>Cooking Time (minutes):</label><br>
    <input type="number" name="cooking_time" required><br><br>

    <!--  NEW -->
    <label>Recipe Image:</label><br>
    <input type="file" name="image" accept="image/*" required><br><br>

    <label>Ingredients (comma separated):</label><br>
    <input type="text" name="ingredients" required><br><br>

    <label>Cooking Instructions (one step per line):</label><br>
    <textarea name="instructions" rows="10" required></textarea><br><br>

    <label>Description:</label><br>
    <textarea name="description" rows="5" required></textarea><br><br>

    <button type="submit">Save Recipe</button>
</form>

<?php include '../includes/footer.php'; ?>
