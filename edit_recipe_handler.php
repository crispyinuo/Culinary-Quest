<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$servername = "303.itpwebdev.com";
$username = "zoez_db_user";
$password = "uscitp2023";
$dbname = "zoez_recipes";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$recipeId = $_POST['recipeId'];
$recipeName = $_POST['recipeName'];
$recipeInstructions = $_POST['recipeInstructions'];
$recipeImage = $_FILES['recipeImage'];
$ingredients = $_POST['ingredients'];
$user_id = $_SESSION['user_id'];


$sql = "UPDATE recipes SET title = ?, user_id = ?, image_url = ?, direction_text = ? WHERE recipe_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("sissi", $recipeName, $user_id, $recipeImage['name'], $recipeInstructions, $recipeId);
    $stmt->execute();
} else {
    die("Error preparing statement: " . $conn->error);
}


// Remove all existing ingredients for the recipe
$sql = "DELETE FROM ingredients WHERE recipe_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $recipeId);
    $stmt->execute();
} else {
    die("Error preparing statement: " . $conn->error);
}

$stmt->close();
$conn->close();

header("Location: profile.php");
?>