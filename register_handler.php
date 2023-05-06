<?php
$servername = "303.itpwebdev.com";
$username = "zoez_db_user";
$password = "uscitp2023";
$dbname = "zoez_recipes";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$fullName = $_POST['fullName'];
$username = $_POST['username'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];

if (empty($username) || empty($password)) {
    header("Location: register.html?error=empty_fields"); // Redirect back to register page with error
    exit();
}

if ($password !== $confirmPassword) {
  header("Location: register.html?error=password_mismatch"); // Redirect back to register page with error
  exit();
}

$password_hash = hash('sha256', $password);

// Check if username exists
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  header("Location: register.html?error=username_exists"); // Redirect back to register page with error
  exit();
}

$stmt->close();

$sql = "INSERT INTO users (username, password_hash) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password_hash);

if ($stmt->execute()) {
  header("Location: login.html?success=registration_successful"); // Redirect to the login page with success message
} else {
  header("Location: register.html?error=registration_failed"); // Redirect back to register page with error
}

$stmt->close();
$conn->close();
?>
