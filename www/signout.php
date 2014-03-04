<?php
require_once 'config.php';
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IrssiGlass</title>
    <link rel="icon" type="image/png" href="./static/images/favicon.png">
    <link href="./static/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="./static/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
    <link href="./static/main.css" rel="stylesheet" media="screen">
    <link href="./static/base_style.css" rel="stylesheet" media="screen">
  </head>
  <body>
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="brand" href="#">IrssiGlass Manager</a>
        </div>
      </div>
    </div>
    <div class="container">
      <div>
        You have been signed out. <a href="<?php echo $base_url ?>">Sign back in</a>
      </div>
      <div>
        If you want to completely disable IrssiGlass, then you need to remove your user entry from the database.
      </div>
    </div>
  </body>
</html>