<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
?>

<form method="get">
    <input type="text" name="q" placeholder="Search recipe"
           value="<?php echo isset($_GET['q']) ? e($_GET['q']) : ''; ?>">
    <button type="submit">Search</button>
</form>

<?php
if (isset($_GET['q']) && $_GET['q'] !== '') {

    $q = "%".$conn->real_escape_string($_GET['q'])."%";

    $stmt = $conn->prepare("SELECT * FROM recipes WHERE title LIKE ?");
    $stmt->bind_param("s", $q);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()){
            echo "<p><a href='view.php?id={$row['id']}'>".e($row['title'])."</a></p>";
        }
    } else {
        echo "<p>No recipes found.</p>";
    }

    $stmt->close();

} else {
    echo "<p>Enter keyword to search.</p>";
}
?>


