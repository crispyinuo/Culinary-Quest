<?php
session_start();

$servername = "303.itpwebdev.com";
$username = "zoez_db_user";
$password = "uscitp2023";
$dbname = "zoez_recipes";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$recipe_id = $_GET['id'];

// First, delete the associated ingredients
$sql = "DELETE FROM ingredients WHERE recipe_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
} else {
    die("Error preparing statement: " . $conn->error);
}

// Now, delete the recipe
$sql = "DELETE FROM recipes WHERE recipe_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        // Redirect to the community page
        header("Location: profile.php");
        exit();
    } else {
        // Handle error: Recipe deletion failed
        echo "Error: Recipe deletion failed";
        exit();
    }
} else {
    die("Error preparing statement: " . $conn->error);
}

$stmt->close();
$conn->close();
?>
