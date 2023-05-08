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

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM recipes WHERE user_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
} else {
    die("Error preparing statement: " . $conn->error);
}

$result = $stmt->get_result();

$uploadedRecipes = [];
while ($row = $result->fetch_assoc()) {
    $uploadedRecipes[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="description"
        content="Access your CulinaryQuest profile, where you can manage your personal collection of recipes, view your uploads, and customize your account settings.">
    <meta name="author" content="Yinuo Zhou">

    <title>CulinaryQuest | Profile</title>
    <!--GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <!--BOOTSTRAP MAIN STYLES -->
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <!--CUSTOM STYLE -->
    <link href="css/style.css" rel="stylesheet" />
</head>

<body>

    <!--NAV SECTION -->
    <header id="header-nav" role="banner">
        <div id="navbar" class="navbar navbar-default">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">CulinaryQuest</a>
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav flot-nav">
                    <li><a href="homepage.html">Home</a></li>
                    <li><a href="community.php">Community</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>
        </div>
    </header>
    <!--END NAV SECTION -->

    <!--PROFIEL SAMES AS COMMUNITY SECTION -->
    <section id="community-section">
        <div class="wrap-pad">
            <div class="row">
                <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 ">
                    <div class="text-center">
                        <h1>Hi, Tommy Trojan!</h1>
                        <a class="btn btn-secondary" href="login.html" role="button">Log Out</a>
                        <p class="lead">
                            View all your recipes.
                        </p>
                    </div>

                </div>
                <!-- ./ Heading div-->

                <div class="col-md-10 col-md-offset-1 col-sm-12">
                    <div class="text-center">
                        <h2>Your Uploads</h2>
                    </div>
                    <ul class="community-items col-3">
                        <?php foreach ($uploadedRecipes as $recipe): ?>
                            <li class="community-item ">
                                <div class="item-main">
                                    <div class="community-image">
                                        <img src="<?php echo htmlspecialchars($recipe['image_url']); ?>" alt="recipe">
                                        <div class="overlay">
                                            <a class="preview btn btn-primary" title="View Details"
                                                href="recipedetail.php?id=<?php echo $recipe['recipe_id']; ?>">View
                                                Details</a>
                                            <br>
                                            <a class="delete btn btn-danger" title="Delete" href="#">Delete</a>
                                        </div>
                                    </div>
                                    <h5>
                                        <?php echo htmlspecialchars($recipe['title']); ?>
                                    </h5>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- ./ uploaded recipes div-->
            </div>
        </div>

    </section>
    <!--END community SECTION -->

    <!--FOOTER SECTION -->
    <footer id="footer">
        <div class="row">
            <div class="col-md-12  col-sm-12">
                &copy; 2023 Yinuo Zhou | All Rights Reserved

            </div>
        </div>
    </footer>
    <!--END FOOTER SECTION -->
</body>

</html>