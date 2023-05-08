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

$recipe_id = $_GET['id']; // Get the recipe ID from the URL, e.g., recipe.php?id=1

$sql_recipe = "SELECT * FROM recipes WHERE recipe_id = $recipe_id";
$result_recipe = $conn->query($sql_recipe);

$sql_ingredients = "SELECT * FROM ingredients WHERE recipe_id = $recipe_id";
$result_ingredients = $conn->query($sql_ingredients);

if ($result_recipe !== false) {
    $recipe_data = $result_recipe->fetch_assoc();
} else {
    // Handle the error when the recipe query fails
    echo "Error: Recipe Query fails" . $conn->error;
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="description"
        content="Dive into the details of a specific recipe on CulinaryQuest. Find the ingredients, cooking instructions, and helpful tips to make your dish a success.">
    <meta name="author" content="Yinuo Zhou">

    <title>CulinaryQuest | Recipe</title>
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
    <!--DETAILS SECTION -->
    <section id="details-section">
        <div class="wrap-pad">
            <div class="row">
                <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 ">
                    <div class="text-center">
                        <h1>
                            <h1>
                                <?php echo $recipe_data['title']; ?>
                            </h1>

                        </h1>
                    </div>
                </div>

                <div class="details-image">
                    <img src="<?php echo $recipe_data['image_url']; ?>" alt="Recipe Image">
                </div>

                <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 ">
                    <div class="text-center">
                        <h2>Ingredients</h2>
                    </div>
                </div>
                <!-- ./ Heading div-->
                <div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 ">
                    <?php if ($result_ingredients !== false): ?>
                        <?php while ($row = $result_ingredients->fetch_assoc()): ?>
                            <div class="col-md-4 col-sm-6">
                                <div class="icon-text-container">
                                    <img src="img/herbs.png" alt="ingredient" class="details-icon">
                                    <h4>
                                        <?php echo $row['quantity'] . ' ' . $row['unit_of_measure'] . ' ' . $row['ingredient_name']; ?>
                                    </h4>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <!-- Handle the error when the ingredients query fails -->
                        <p>Error:
                            <?php echo $conn->error; ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 ">
                    <div class="text-center">
                        <h2>Directions</h2>
                        <p class="lead">
                            <?php echo nl2br($recipe_data['direction_text']); ?>
                        </p>
                    </div>
                </div>
                <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 ">
                    <?php
                    $logged_in_user_id = $_SESSION['user_id']; // Replace this with the actual user ID from your authentication system
                    
                    // Check if the user is an admin
                    $sql_check_admin = "SELECT is_admin FROM users WHERE user_id = ?";
                    $stmt = $conn->prepare($sql_check_admin);
                    $stmt->bind_param("i", $logged_in_user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user_data = $result->fetch_assoc();
                    $is_admin = $user_data['is_admin'];
                    $stmt->close();

                    if ($recipe_data['user_id'] == $logged_in_user_id || $is_admin): ?>
                        <form action="delete_recipe.php" method="get">
                            <input type="hidden" name="id" value="<?php echo $recipe_id; ?>">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>

                    <?php else: ?>
                        <button type="button" class="btn btn-danger" disabled>Delete</button>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </section>
    <!--END DETAILS SECTION -->
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