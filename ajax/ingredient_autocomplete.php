<?php
require_once '../config/db.php';
$q = $_GET['q'] ?? '';
$q = trim($q);
$suggestions = [];

if(!empty($q)){
    $q = "%".$q."%";
    $stmt = $conn->prepare("SELECT name FROM ingredients WHERE name LIKE ? LIMIT 5");
    $stmt->bind_param("s", $q);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
        $suggestions[] = $row['name'];
    }
    $stmt->close();
}

echo json_encode($suggestions);
?>
