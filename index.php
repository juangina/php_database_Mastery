<?php
  require 'startup.php';
  $_SESSION["loggedin"] = false;
  $_SESSION["username"] = "";
?>

<!DOCTYPE html>

<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Family Application to increase communication and bonding - Home Page"/>

  <title>PHP-PostgreSQL</title>

    <!-- CSS Stylesheets -->
  <link href="css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Great+Vibes|Lato|Raleway" rel="stylesheet">

  <!-- Favicon -->
  <link rel="icon" href="images/coffee-website-favicon.jpg">
</head>

<body>

<?php 
  //Testing Console_Log
  //console_log("Index Page Loading");
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

  <div id="login-stat-sect" class="exclusive-group" align="center">
    <p>
      <?php
        if ($_SESSION["loggedin"]) {
          echo "You are Logged In as: " . $_SESSION["username"];
        }
        else {
          echo "Log in to use the PHP-PostgreSQL App";
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
