<?php
$servername = "303.itpwebdev.com";
$username = "zoez_db_user";
$password = "uscitp2023";
$dbname = "zoez_recipes";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$total = 0;
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 9;
$offset = ($page - 1) * $limit;

$sql_count = "SELECT COUNT(*) as total FROM recipes";
if (!empty($search_query)) {
    $sql_count .= " WHERE title LIKE ?"; // Add search query condition
}

$count_stmt = $conn->prepare($sql_count);
if ($count_stmt) {
    if (!empty($search_query)) {
        $search_param = '%' . $search_query . '%';
        $count_stmt->bind_param("s", $search_param);
    }
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $row = $count_result->fetch_assoc();
    $total = $row['total'];
} else {
    die("Error preparing count statement: " . $conn->error);
}
$count_stmt->close();
$pages = ceil($total / $limit);

$sql = "SELECT * FROM recipes";
if (!empty($search_query)) {
    $sql .= " WHERE title LIKE ?"; // Add search query condition
}
$sql .= " LIMIT 9 OFFSET ?"; // Add the limit and offset
$stmt = $conn->prepare($sql);

if ($stmt) {
    if (!empty($search_query)) {
        $stmt->bind_param("si", $search_param, $offset);
    } else {
        $stmt->bind_param("i", $offset);
    }

    $stmt->execute();
} else {
    die("Error preparing statement: " . $conn->error);
}

$result = $stmt->get_result();

$recipes = [];
while ($row = $result->fetch_assoc()) {
    $recipes[] = $row;
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
        content="Explore CulinaryQuest's vibrant community, where you can discover delicious recipes, share your favorite dishes, and connect with food enthusiasts from around the world.">
    <meta name="author" content="Yinuo Zhou">

    <title>CulinaryQuest | Community</title>
    <!--GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <!--BOOTSTRAP MAIN STYLES -->
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <!--CUSTOM STYLE -->
    <link href="css/style.css" rel="stylesheet" />
    <style>
        .community-image img {
            width: 100%;
            height: 200px;
            /* You can adjust the height as needed */
            object-fit: cover;
        }

        .pagination {
            margin-top: 20px;
            margin-bottom: 20px;
        }
    </style>
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

    <!--COMMUNITY SECTION -->
    <section id="community-section">
        <div class="wrap-pad">
            <div class="row">
                <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 ">
                    <div class="text-center">
                        <h1>Community</h1>
                        <p class="lead">
                            Explore the Recipes.
                        </p>
                        <input type="text" class="search-input" id="searchInput" placeholder="Search...">
                        <button type="button" class="btn btn-default" id="searchBtn">Search</button>
                        <p>Total results:
                            <?php echo $total; ?>
                        </p>
                    </div>
                </div>
                <!-- ./ Heading div-->

                <div class="col-md-10 col-md-offset-1 col-sm-12">
                    <ul class="community-items col-3">
                        <?php foreach ($recipes as $recipe): ?>
                            <li class="community-item ">
                                <div class="item-main">
                                    <div class="community-image">
                                        <img src="<?php echo $recipe['image_url']; ?>"
                                            alt="<?php echo $recipe['title']; ?>">
                                        <div class="overlay">
                                            <a class="preview btn btn-primary" title="View Details"
                                            href="recipedetail.php?id=<?php echo $recipe['recipe_id']; ?>">View Details</a>
                                        </div>
                                    </div>
                                    <h5>
                                        <?php echo $recipe['title']; ?>
                                    </h5>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <nav aria-label="Page navigation" class="text-center">
                        <ul class="pagination">
                            <?php for ($i = 1; $i <= $pages; $i++): ?>
                                <li class="<?php echo $page == $i ? 'active' : ''; ?>">
                                    <a href="community.php?search=<?php echo $search_query; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>

                </div>
                <!-- ./ Content div-->
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let offset = 9;
        let currentSearch = "";

        function loadMore() {
            const searchQuery = currentSearch;
            $.ajax({
                url: "community.php",
                type: "GET",
                data: {
                    search: searchQuery,
                    offset: offset,
                },
                success: function (data) {
                    const newItems = $(data).find(".community-items").html();
                    if (!newItems.trim()) {
                        $("#loadMore").prop("disabled", true);
                    } else {
                        $(".community-items").html(newItems); // Replace the existing search results with the new ones
                        offset += 9;
                    }
                },

                error: function (error) {
                    console.error(error);
                },
            });
        }

        $("#searchBtn").on("click", function (e) {
            e.preventDefault();
            currentSearch = $("#searchInput").val();
            window.location.href = `community.php?search=${currentSearch}`;
        });

        $("#loadMore").on("click", function (e) {
            e.preventDefault();
            loadMore();
        });

        $(document).ready(function () {
            if ($(".community-items .community-item").length < 9) {
                $("#loadMore").prop("disabled", true);
            }
            currentSearch = $("#searchInput").val();
        });
    </script>


</body>

</html>