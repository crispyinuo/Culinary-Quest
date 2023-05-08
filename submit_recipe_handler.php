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

$recipeName = $_POST['recipeName'];
$recipeInstructions = $_POST['recipeInstructions'];
$recipeImage = $_FILES['recipeImage'];
$ingredients = $_POST['ingredients'];
$user_id = $_SESSION['user_id'];

// Save the image and get its URL
$target_dir = "uploads/";
$target_file = $target_dir . basename($recipeImage["name"]);
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

if (move_uploaded_file($recipeImage["tmp_name"], $target_file)) {
    $image_url = $target_file;
} else {
    die("Error uploading the image.");
}

$sql = "INSERT INTO recipes (title, user_id, image_url, direction_text) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("siss", $recipeName, $user_id, $image_url, $recipeInstructions);
    $stmt->execute();
    $recipe_id = $stmt->insert_id;
} else {
    die("Error preparing statement: " . $conn->error);
}

foreach ($ingredients as $key => $ingredient) {
    if (!empty($ingredient)) {
        $quantity = isset($_POST['ingredientQuantity'][$key]) ? $_POST['ingredientQuantity'][$key] : '';
        $unit_of_measure = isset($_POST['ingredientUnit'][$key]) ? $_POST['ingredientUnit'][$key] : '';

        $sql = "INSERT INTO ingredients (recipe_id, quantity, unit_of_measure, ingredient_name) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("iiss", $recipe_id, $quantity, $unit_of_measure, $ingredient);
            $stmt->execute();
        } else {
            die("Error preparing statement: " . $conn->error);
        }
    }
}


$stmt->close();
$conn->close();

header("Location: profile.php");
?>