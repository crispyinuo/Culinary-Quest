<?php
session_start();

if (isset($_POST['username']) && isset($_POST['password'])) {
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
      $_SESSION['user_id'] = $row['user_id'];
      $_SESSION['username'] = $row['username'];
      $_SESSION['is_admin'] = $row['is_admin'];

      header("Location: homepage.html");
      exit();
    } else {
      $_SESSION['error'] = "Invalid password";
      header("Location: login.php");
      exit();
    }
  } else {
    $_SESSION['error'] = "User not found";
    header("Location: login.php");
    exit();
  }

  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <meta name="description"
    content="Log in to CulinaryQuest to access your personalized collection of recipes, interact with the community, and share your culinary creations.">
  <meta name="author" content="Yinuo Zhou">

  <title>CulinaryQuest | Log In</title>
  <!--GOOGLE FONT -->
  <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
  <!--BOOTSTRAP MAIN STYLES -->
  <link href="css/bootstrap.min.css" rel="stylesheet" />
  <!--CUSTOM STYLE -->
  <link href="css/style.css" rel="stylesheet" />
</head>

<body>
  <main>
    <!-- Login SECTION -->
    <section id="login-section">
      <div class="wrap-pad">
        <div class="row">
          <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
            <div class="text-center">
              <h1>Login</h1>
              <p class="lead">
                Access your account.
              </p>
            </div>
          </div>
          <!-- ./ Heading div-->

          <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div id="error-msg" class="alert alert-danger" role="alert" style="display:none;"></div>
            <div class="login-form">
              <form action="login.php" method="POST">
                <div class="form-group">
                  <label for="username">Username</label>
                  <input type="text" class="form-control" id="username" name="username" placeholder="Enter username">
                </div>
                <div class="form-group">
                  <label for="password">Password</label>
                  <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
                <p class="mt-3">
                  Don't have an account?<a href="register.html" class="btn btn-link">Register</a>
                </p>
              </form>
            </div>
          </div>
          <!-- ./ Content div-->
        </div>
      </div>
    </section>
    <!-- END Login SECTION -->
  </main>
  <script>
    <?php if (isset($_SESSION['error'])): ?>
      document.getElementById('error-msg').style.display = 'block';
      document.getElementById('error-msg').innerHTML = '<?php echo $_SESSION['error']; ?>';
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
  </script>
</body>

</html>