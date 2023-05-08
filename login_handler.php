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

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  if (hash('sha256', $password) === $row['password_hash']) {
    session_start();
    $_SESSION['user_id'] = $row['user_id'];
    $_SESSION['username'] = $row['username'];
    $_SESSION['is_admin'] = $row['is_admin'];
    // If the user is not found or the password is incorrect
    $_SESSION['login_failed'] = true;
    header("Location: login.html");

    header("Location: homepage.html"); // Redirect to the homepage or dashboard
  } else {
    header("Location: login.html?error=invalid_password"); // Redirect back to login page with error
  }
} else {
  header("Location: login.html?error=user_not_found"); // Redirect back to login page with error
}

$conn->close();
?>