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

$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;

$sql = "SELECT * FROM recipes LIMIT 12 OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $offset);
$stmt->execute();

$result = $stmt->get_result();

while ($recipe = $result->fetch_assoc()) {
?>
  <li class="community-item">
    <div class="item-main">
      <div class="community-image">
        <img src="<?php echo $recipe['image_url']; ?>" alt="<?php echo $recipe['title']; ?>">
        <div class="overlay">
          <a class="preview btn btn-primary" title="View Details" href="recipedetail.php?id=<?php echo $recipe['id']; ?>">View Details</a>
        </div>
      </div>
      <h5><?php echo $recipe['title']; ?></h5>
    </div>
  </li>
<?php
}

$stmt->close();
$conn->close();
?>
