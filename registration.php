<?php
  require 'startup.php';

  // Check if the user is already logged in, if yes then redirect him to welcome page
  if($_SESSION["username"] != 'juaneric') {
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
      $mssg .= "User already logged in.";
      $_SESSION["debug3"] = $mssg;
      header("location: dashboard.php");
      exit;
    }
  }  

  // Processing form data when form is submitted
  if ($_POST["submit"]) {

    if (!$_POST['name']) {
      $error="<br />Please enter your name.";
      $mssg .= "Missing Name.";
    }
    else {
      $username = trim($_POST["name"]);
    }

    if (!$_POST['password1']) {
      $error.="<br />Please enter a password.";
      $mssg .= "Missing Password.";
    }

    if (!$_POST['password2']) {
      $error.="<br />Please confirm your password.";
      $mssg .= "Missing Confirmed Password.";
    } 

    if ($_POST['email']!="" AND !filter_var($_POST['email'],
      FILTER_VALIDATE_EMAIL)) {
      $error.="<br />Please enter a valid email address.";
      $mssg .= "Invalid email address.";
    }
    else {
      $useremail = trim($_POST['email']);      
    }

    if($_POST['password1'] && $_POST['password2'] == FALSE) {
      $error .= "<br/>Please enter same password for both fields.";
      $mssg .= "Passwords do not match.";;
    }
    else {
      $userpwd_raw = trim($_POST['password1']);
      $userpwd = password_hash($userpwd_raw, PASSWORD_DEFAULT);
    }

    if ($error) {
      $mssg = 'There were errors in your submission';
    } 
    else {
      /* Database credentials */
      if ($local == True) {
        define('DB_SERVER', '');
        define('DB_USERNAME', '');
        define('DB_PASSWORD', '');
        define('DB_NAME', '');
        $mssg .= "Defining Database Variables.";
      }
      else {
        $db = parse_url(getenv("DATABASE_URL"));
        $mssg .= "Defining Database Variables.";
      }

      /* Attempt to connect to MySQL database */
      try {
        if($local == True) {
          $pdo = new PDO("pgsql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
          $mssg .= "Creating new PDO Object.";
        }
        else {
          $pdo = new PDO("pgsql:" . sprintf(
            "host=%s;port=%s;user=%s;password=%s;dbname=%s",
            $db["host"],
            $db["port"],
            $db["user"],
            $db["pass"],
            ltrim($db["path"], "/")
          ));
          $mssg .= "Creating new PDO Object.";
        }

        // Set the PDO to standard fetch mode
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $mssg .= 'Setting Registration Default Attributes.';
        // Set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $mssg .= 'Connection to Database Made.';
        
///////////////////////////////Check if the potential user is already registered
        $sql = "SELECT id, username, pwd FROM users WHERE username = :username";
        // Attemp to create a valid pdo statement
        if($stmt = $pdo->prepare($sql)) {
          // Bind variables to the prepared statement as parameters
          $stmt->bindParam(":username", $username, PDO::PARAM_STR);        
          // Attempt to execute the prepared statement
          if($stmt->execute()) {
            // Check if username exists
            //$_SESSION["debug_rowCount"] = $stmt->rowCount();
            if($stmt->rowCount() >= 1) {
              $mssg .= "User already registered in database.";
              $username_err = "User already registered in database.  Try another user name.";
              $_SESSION["debug3"] = $mssg;
              // Close connection
              unset($pdo);
              //header("location: debug_out.php");
              //header("location: registration.php");
            } 
            else {
              $mssg .= "No account found with that username, you can register";
///////////////////////////////SAVE THE REGISTRATION INFORMATION IN THE DATABASE
              $sql = "INSERT INTO users(username, email, pwd) VALUES (:username, :useremail, :userpwd)";
              if($stmt = $pdo->prepare($sql)) {
                if($stmt->execute(['username'=>$username,'useremail'=>$useremail,'userpwd'=>$userpwd])) {
                  $mssg .= "User successfully inserted into database"; 
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
              //Redirect to Next Page
              $_SESSION["debug3"] = $mssg;
              header("location: login.php");
//////////////////////////////Error Messages
            }
          } 
          else {
            $mssg .= "stmt did not execute.";
          }
        }
        else {
          $mssg .= "stmt did not open.";
        }        
      } catch(PDOException $e) {
          die("ERROR: Could not connect. " . $e->getMessage());
          $mssg .= 'Error, could not connect to database';
          $connection = FALSE;
      }
      $_SESSION["debug3"] = $mssg;
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
  <meta name="description" content="" >

  <!-- Meta Description -->
  <title>Registration - Family Favorites</title>

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
    console_log("Registration Page Loading");
    console_log($_SESSION);    
  ?>

  <div class="topbanner" id="nav-sect">
    <nav class="navbar navbar-default navbar-static navbar-transparent" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="/index.php">
            <img alt="Brand" src="images/coffee-shop-logo.png" class="logo">
          </a>
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
              if($_SESSION["username"] == "juaneric") {
                echo '<li id="admin" align="center"><a href="/registration.php">Registration</a></li>';
                echo '<li id="admin" align="center"><a href="/login.php">Login</a></li>';
                echo '<li id="admin" align="center"><a href="/dashboard.php">Dashboard</a></li>';
                echo '<li id="admin" align="center"><a href="/contact.php">Contact</a></li>';
                echo '<li id="admin" align="center"><a href="/test_pages/debug_out.php">Debug Out</a></li>';
                echo '<li id="admin" align="center"><a href="/logout.php">Logout</a></li>';
              } else {
                echo '<li align="center"> <a href="/dashboard.php">Dashboard</a></li>';
                echo '<li align="center"> <a href="/contact.php">Contact Me</a></li>';
              }
            ?>
          </ul>
        </div><!--end navbar-collapse-->
      </div><!--end container-->
    </nav><!--end nav-->
    <h1 align="center">Johnson-Thomas Estate<br /><span class="cursive">Favorite Things Project</span></h1>
  </div><!--end contact-banner-->

  <div id="registration-form-sect" class="container">
    <h2>Register</h2>		
    <p>Please fill in your credentials to Register.</p>
    <form method="post">
      <div class="form-group">
        <input type="text" name="name" class="form-control" placeholder="User Name"/>
        <span class="help-block"><?php echo $username_err; ?></span>
      </div><!--end form-group-->
      <div class="form-group">
        <input type="email" name="email" class="form-control" placeholder="Your Email" value="<?php echo $_POST['email']; ?>" />
      </div><!--end form-group-->
      <div class="form-group">
        <input type="password" name="password1" class="form-control" placeholder="Password" value="<?php echo $_POST['password1']; ?>" />
      </div><!--end form-group-->
      <div class="form-group">
        <input type="password" name="password2" class="form-control" placeholder="Repeat Password" value="<?php echo $_POST['password2']; ?>" />
      </div><!--end form-group-->   
      <div class="form-group">
          <input type="submit" name="submit" class="btn btn-lg" style="background-color: #ffb701" value="Register" />
          <input type="reset" name="reset" class="btn btn-default btn-lg" value="Reset">
      </div><!--end form-group-->
    </form>
  </div><!--end registration form-->

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