<?php
  require 'startup.php';
  $mssg = "";
  // Check if the user is not logged in, redirect him to login page
  if($_SESSION["loggedin"] === NULL) {
    header("location: login.php");
    exit;
  }

  /* Database credentials */
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'postgres');
    define('DB_PASSWORD', 'mySQLdb03');
    define('DB_NAME', 'postgres');

  /* Attempt to connect to MySQL database */
  try {
    $pdo = new PDO("pgsql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);

    $mssg .= "Defining Variables.";
    $mssg .= "New Login Object Created.";
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $mssg .= "Setting Login Default Attributes.";
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $mssg .= "Connection to Database Made.";
  }
  catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
    $mssg .= "Error, could not connect.";
  }

  //Query the db for favorite topics for the dropdown list
    $sql = "SELECT * FROM favorite_topics";
    if($stmt = $pdo->prepare($sql)) {    
        if($stmt->execute()) {  
            if($quoteCount = $stmt->rowCount()) { 
                while ($row = $stmt->fetch()) {
                    $opts[] = [
                        'topic'=>$row['topic'],
                        'description'=>$row['description'],
                        'recommended_user'=>$row['recommended_user'],
                        'comments'=>$row['comments']
                    ];
                }
                //print_r($opts);
            }else{$debug2 = "stmt->rowCount error";}
        }else{$debug2 = "stmt->execute error";}
    }else{$debug2 = "pdo->prepare error";}
    $randOpt = rand(0, $quoteCount-1);
  //end Query the db for favorite topics for the dropdown list

  function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  /*Post Submission Test*/  
  if($_SERVER["REQUEST_METHOD"] == "POST") {
      //$_SESSION["debug4"] = $_POST;

      if (!$_POST['topic']) {
        $error="<br />Please select a topic.";
      }

      if (!$_POST['favorite']) {
        $error.="<br />Please enter your favorite for the topic.";
      }
      if (!$_POST['comment']) {
        $error.="<br />Please add a simple comment.";
      }    

      if ($error) {
        //Error in form
        $mssg = "Tranaction completed";
        $_SESSION["debug3"] = $mssg;
      } 
      else {
        /* Form Data has been validated */
        //Save Form Data for Processing
        $topic = clean_input($_POST['topic']);
        $favorite = clean_input($_POST['favorite']);
        $comment = clean_input($_POST['comment']);
        //Clear Data from Form
        $_POST['topic'] = "";
        $_POST['favorite'] = "";
        $_POST['comment'] = "";


        /* Save the favorite quote in the Database */
        $sql = "INSERT INTO favorites(username, topic, favorite, comments) VALUES (:username, :topic, :favorite, :comment)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username'=> $_SESSION["username"],'topic'=>$topic,'favorite'=>$favorite,'comment'=>$comment]);
        $mssg .= "Favorite Added";
        
        // Close connection
        unset($pdo);
        $mssg = "Tranaction completed";
        $_SESSION["debug3"] = $mssg;
        header("location: dashboard.php");
      }
    } else {
        //Clear Data from Form
        $_POST['topic'] = "";
        $_POST['favorite'] = "";
        $_POST['comment'] = "";
    }
  /*end Post Submission Test*/  
 
?>

<!DOCTYPE html>

<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Family Application to increase communication and bonding - Dashboard"/>

  <title>Family Favorites</title>

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
    //console_log("Dashboard Page Loading");
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

  <div class="contactusform" id="favorite-form-sect">
    <div class="row contact-info">

    <!--Email Form Column-->
      <div class="col-md-6 emailForm" align="center">
        <h4 style="margin-top: 20px"> Enter your Favorite for the Week!</h4>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
          <div class="form-group">
            <label for="sel1">Select Topic from Dropdown List</label>
            <select class="form-control" id="sel1" name="topic">
              <?php foreach ($opts as $opt) : ?>
                  <option value=<?php echo $opt['topic']?>>
                    <?php 
                        echo htmlspecialchars($opt['topic']);
                    ?>
                  </option>
              <?php endforeach; ?>                    
            </select>
          </div><!--end form-group-->
          <div class="form-group">
            <input type="text" name="favorite" class="form-control" placeholder="Your Favorite" value="<?php echo $_POST['favorite']; ?>" />
          </div><!--end form-group-->
          <div class="form-group">
            <textarea class="form-control" id="message" name="comment" placeholder="Comment"><?php echo $_POST['comment']; ?></textarea>
          </div><!--end form-group-->
          <div class="form-group">
            <input type="submit" name="submit" class="btn btn-lg" style="background-color: #ffb701" value="Submit Favorite" />
          </div><!--end form-group-->
        </form>
      </div>
    <!--End Email Form Column-->  
    </div><!--end row contact info-->
  </div><!--end contactusform-->



  <div class="exclusive-group" id="login-stat-sect" align="center">
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
  <!-- end jQuery (necessary for Bootstrap's JavaScript plugins) -->

</body>
</html>
