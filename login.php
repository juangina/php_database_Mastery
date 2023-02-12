<?php
  require 'startup.php';
  $mssg = "";
  // Check if the user is already logged in, if yes then redirect him to welcome page

  if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: dashboard.php");
    exit;
  }

  // Define variables and initialize with empty values
  $username = $password = "";
  $username_err = $password_err = "";
  
  function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
  // Processing form data when form is submitted
  if($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username is empty
    if(empty(clean_input($_POST["username"]))) {
      $username_err = "Please enter username.";
    } 
    else {
      $username = clean_input($_POST["username"]);
    }

    // Check if password is empty
    if(empty(clean_input($_POST["password"]))) {
      $password_err = "Please enter your password.";
    } 
    else {
      $password = clean_input($_POST["password"]);
    }

    // Validate credentials
    if(empty($username_err) && empty($password_err)) {
      $mssg .= "name and password valid.";

      /* Database credentials */
      define('DB_SERVER', 'localhost');
      define('DB_USERNAME', 'postgres');
      define('DB_PASSWORD', 'mySQLdb03');
      define('DB_NAME', 'postgres');
      $mssg .= "Defining Database Variables.";

      /* Attempt to connect to MySQL database */
      try {

        $pdo = new PDO("pgsql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
        $mssg .= "Creating new PDO Object.";

        // Set the PDO to standard fetch mode
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $mssg .= "Setting Login Default Attributes.";
        // Set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $mssg .= "Connection to Database Made.";

        //Check if the potential user is already registered
        // Prepare a select statement
        $sql = "SELECT id, username, pwd FROM users WHERE username = :username";
        if($stmt = $pdo->prepare($sql)) {
          // Bind variables to the prepared statement as parameters
          $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
          // Set parameters
          $param_username = trim($_POST["username"]);        
          // Attempt to execute the prepared statement
          if($stmt->execute()) {
            $mssg .= "Executing database sql command.";
            // Check if username exists, if yes then verify password
            if($stmt->rowCount() == 1) {
              $mssg .= "Verifying user name in database.";
              if($row = $stmt->fetch()) {
                $id = $row["id"];
                $username = $row["username"];
                $hashed_password = $row["pwd"];
                if(password_verify($password, $hashed_password)) {
                  // Password is correct, so start a new session
                  session_start();
                  // Store data in session variables
                  $_SESSION["loggedin"] = true;
                  $_SESSION["id"] = $id;
                  $_SESSION["username"] = $username;
                  // Close connection
                  unset($pdo);
                  $mssg .= "Closing pdo connection.";
                  $_SESSION["debug3"] = $mssg;
                  // Redirect user to welcome page
                  header("location: dashboard.php");
                } 
                else {
                  // Display an error message if password is not valid
                  $password_err = "The password you entered was not valid.";
                  $mssg .= "Error in password submission";
                }
              }
              else {
                $mssg .= "Unable to fetch row object from datatable.";      
              }
            } 
            else {
              // Display an error message if username doesn't exist
              $username_err = "No account found with that username.";
              $mssg .= "No account found with user name submitted.";
            }
          } 
          else {
            $mssg .= "stmt did not execute.";
          }
        } 
        else {
          $mssg .= "stmt did not open.";
        }
        // Close connection
        unset($pdo); 
      } 
      catch(PDOException $e) {
        die("ERROR: Could not connect. " . $e->getMessage());
        $mssg .= "Error, could not connect to database.";
      }
    } 
    else {
      $mssg .= "Error in Login Submission";
    }
    $_SESSION["debug3"] = $mssg;    
  }
?>

<!DOCTYPE html>

<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Family Application to increase communication and bonding - Login"/>
  <title>Login - PHP-PostgreSQL</title>
    <!-- CSS Stylesheets -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="css/styles.css" />
  <link rel="stylesheet" type="text/css" href="css/navbar.css" />
  <link rel="stylesheet" type="text/css" href="css/get-a-quote-form.css" />
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Great+Vibes|Lato|Raleway" rel="stylesheet">
  <!-- Favicon -->
  <link rel="icon" href="images/coffee-website-favicon.jpg">
</head>

<body>

<?php 
    //Testing Console_Log
    //console_log("Login Page Loading");
    //console_log($_SESSION);
?>

  <div class="topbanner" id="nav-sect">
    <nav class="navbar navbar-default navbar-static navbar-transparent" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=" .navbar-collapse">
            <span class="sr-only">Toggle navigation </span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div><!--end navbar-header-->
        <div class="collapse navbar-collapse" align="center">
          <ul class="nav navbar-nav">
            <?php 
              if ($_SESSION["loggedin"]) {
                echo '<li align="center"><a href="/dashboard.php">Dashboard</a></li>';
                echo '<li align="center"><a href="/logout.php">Logout</a></li>';
                
              } else {
                echo '<li align="center"><a href="/registration.php">Registration</a></li>';
                echo '<li align="center"><a href="/login.php">Login</a></li>';
              }
            ?>
          </ul>
        </div><!--end navbar-collapse-->
      </div><!--end container-->
    </nav><!--end nav-->
    <h1 align="center">The Accidental Lifestyle<br /><span class="cursive">PHP-PostgreSQL Project</span></h1>
  </div><!--end topbanner-->

  <div id="login-sect" class="container">
    <h2>Login</h2>		
    <p>Please fill in your credentials to login.</p>		
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">		
      <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">			
        <label>Username</label>				
        <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">				
        <span class="help-block"><?php echo $username_err; ?></span>				
      </div>    
      <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
          <label>Password</label>
          <input type="password" name="password" class="form-control">
          <span class="help-block"><?php echo $password_err; ?></span>
      </div>
      <div class="form-group">
        <input type="submit" name="submit" class="btn btn-lg" style="background-color: #ffb701" value="Login">
        <input type="reset" name="reset" class="btn btn-default btn-lg" value="Reset">
      </div>
      <p>Don't have an account? <a href="registration.php">Sign up now</a>.</p>
    </form>
  </div><!-- end /.container-->

  <div id="login-stat-sect" class="exclusive-group" align="center">
    <p>
      <?php
          if ($_SESSION["loggedin"]) {
            echo "You are Logged In as: " . $_SESSION["username"];
          }
          else {
            echo "Your are Not Logged In.";
          }
        ?>  
      <!-- <a href="#"><span style="color: white">Link</span></a> -->
    </p>
  </div><!--end exclusive-group-->

  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>

</body>
</html>