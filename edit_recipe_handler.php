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
$recipeInstructions = isset($_POST['recipeInstructions']) ? $_POST['recipeInstructions'] : '';
$recipeImageUrl = $_POST['recipeImageUrl'];
$ingredientQuantities = $_POST['ingredientQuantity'];
$ingredientUnits = $_POST['ingredientUnit'];
$ingredientNames = $_POST['ingredientName'];
$user_id = $_SESSION['user_id'];

$sql = "UPDATE recipes SET title = ?, user_id = ?, image_url = ?, direction_text = ? WHERE recipe_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("sisss", $recipeName, $user_id, $recipeImageUrl, $recipeInstructions, $recipeId);
} else {
    die("Error preparing statement: " . $conn->error);
}

$stmt->execute();

// Delete existing ingredients
$sql = "DELETE FROM ingredients WHERE recipe_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $recipeId);
    $stmt->execute();
} else {
    die("Error preparing statement: " . $conn->error);
}

// Insert all ingredients (both new and existing) for the recipe
$sql = "INSERT INTO ingredients (recipe_id, quantity, unit_of_measure, ingredient_name) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

for ($i = 0; $i < count($ingredientNames); $i++) {
    $quantity = $ingredientQuantities[$i];
    $unit = $ingredientUnits[$i];
    $name = $ingredientNames[$i];

    $stmt->bind_param("idss", $recipeId, $quantity, $unit, $name);
    $stmt->execute();
}

$stmt->close();
$conn->close();

header("Location: profile.php");
?>
