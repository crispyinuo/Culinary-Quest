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

// Fetch the recipe ID from the request (you may need to modify this depending on how you pass the recipe ID)
$recipe_id = $_GET['id'];
// Fetch the recipe data from the database
$sql = "SELECT * FROM recipes WHERE recipe_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $recipe = $result->fetch_assoc();
} else {
    die("Error preparing statement: " . $conn->error);
}

// Fetch the ingredients data from the database
$sql = "SELECT * FROM ingredients WHERE recipe_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ingredients = $result->fetch_all(MYSQLI_ASSOC);
} else {
    die("Error preparing statement: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="description"
        content="Share your culinary masterpiece with the CulinaryQuest community. Submit your recipe with photos, ingredients, and instructions for others to enjoy.">
    <meta name="author" content="Yinuo Zhou">

    <title>CulinaryQuest | Edit</title>
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
    <main>
        <!-- Submit Recipe SECTION -->
        <section id="submit-recipe-section">
            <div class="wrap-pad">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
                        <div class="text-center">
                            <h1>Edit Recipe</h1>
                            <p class="lead">
                                Edit your delicious recipe.
                            </p>
                        </div>
                    </div>
                    <!-- ./ Heading div-->

                    <div class="col-md-10 col-md-offset-1 col-sm-12">
                        <div class="submit-recipe-form">
                            <!-- Modify the form tag to include the recipeId -->
                            <form action="edit_recipe_handler.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="recipeId" value="<?php echo $recipe_id; ?>">

                                <!-- Pre-populate the form fields with the existing data -->
                                <div class="form-group">
                                    <label for="recipeName">Recipe Name</label>
                                    <input type="text" class="form-control" id="recipeName" name="recipeName"
                                        value="<?php echo htmlspecialchars($recipe['title']); ?>"
                                        placeholder="Enter recipe name">
                                </div>

                                <!-- Pre-populate the ingredients list with the existing data -->
                                <div class="form-group">
                                    <label for="recipeIngredients">Ingredients</label>
                                    <div id="ingredientsList">
                                        <?php foreach ($ingredients as $ingredient): ?>
                                            <div class="ingredient-entry">
                                                <input type="text" class="form-control" name="ingredientQuantity[]"
                                                    value="<?php echo htmlspecialchars($ingredient['quantity']); ?>"
                                                    placeholder="Quantity">
                                                <input type="text" class="form-control" name="ingredientUnit[]"
                                                    value="<?php echo htmlspecialchars($ingredient['unit_of_measure']); ?>"
                                                    placeholder="Unit of measure">
                                                <input type="text" class="form-control" name="ingredientName[]"
                                                    value="<?php echo htmlspecialchars($ingredient['ingredient_name']); ?>"
                                                    placeholder="Ingredient name">

                                                <button type="button"
                                                    class="btn btn-danger remove-ingredient">Remove</button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button type="button" class="btn btn-primary" id="addIngredient">Add
                                        Ingredient</button>
                                </div>

                                <div class="form-group">
                                    <label for="recipeInstructions">Instructions</label>
                                    <textarea class="form-control" id="recipeInstructions" name="recipeInstructions"
                                        rows="5"
                                        placeholder="Enter instructions"><?php echo htmlspecialchars($recipe['direction_text']); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="recipeImageUrl">Image URL</label>
                                    <input type="text" class="form-control" id="recipeImageUrl" name="recipeImageUrl"
                                        value="<?php echo htmlspecialchars($recipe['image_url']); ?>"
                                        placeholder="Enter image URL">
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>

                        </div>
                    </div>
                    <!-- ./ Content div-->
                </div>
            </div>
        </section>
        <!-- END Submit Recipe SECTION -->
    </main>


    <!--FOOTER SECTION -->
    <footer id="footer">
        <div class="row">
            <div class="col-md-12  col-sm-12">
                &copy; 2023 Yinuo Zhou | All Rights Reserved

            </div>
        </div>
    </footer>
    <!--END FOOTER SECTION -->

    <script>
        document.getElementById('addIngredient').addEventListener('click', function () {
            const ingredientsList = document.getElementById('ingredientsList');
            const ingredientEntry = document.createElement('div');
            ingredientEntry.classList.add('ingredient-entry');

            ingredientEntry.innerHTML = `
          <input type="text" class="form-control" name="ingredientQuantity[]" placeholder="Quantity">
          <input type="text" class="form-control" name="ingredientUnit[]" placeholder="Unit of measure">
          <input type="text" class="form-control" name="ingredientName[]" placeholder="Ingredient name">
          <button type="button" class="btn btn-danger remove-ingredient">Remove</button>
        `;

            ingredientEntry.querySelector('.remove-ingredient').addEventListener('click', function () {
                ingredientsList.removeChild(ingredientEntry);
            });

            ingredientsList.appendChild(ingredientEntry);
        });

        document.querySelectorAll('.remove-ingredient').forEach(function (button) {
            button.addEventListener('click', function () {
                const ingredientEntry = button.closest('.ingredient-entry');
                document.getElementById('ingredientsList').removeChild(ingredientEntry);
            });
        });
    </script>
</body>