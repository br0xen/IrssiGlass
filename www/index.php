<?php
/* IrssiGlass Manager
 * A Quick and Dirty manager for the IrssiGlass Timeline
 * 
 * This uses the Google API PHP Quick-Start Client
 */
require_once 'config.php';
require_once 'mirror-client.php';
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_MirrorService.php';
require_once 'util.php';

$client = get_google_api_client();

// Authenticate if we're not already
if (!isset($_SESSION['userid']) || get_credentials($_SESSION['userid']) == null) {
  header('Location: ' . $base_url . '/oauth2callback.php');
  exit;
} else {
  verify_credentials(get_credentials($_SESSION['userid']));
  $client->setAccessToken(get_credentials($_SESSION['userid']));
}

// A glass service for interacting with the Mirror API
$mirror_service = new Google_MirrorService($client);
// Check if we're deleting something
if(isset($_POST['operation']) && ($_POST['operation'] == "deleteTimelineItem") && isset($_POST['itemId'])) {
  delete_timeline_item($mirror_service, $_POST['itemId']);
}

//Load cool stuff to show them.
$timeline = $mirror_service->timeline->listTimeline(array('maxResults'=>'45'));

?>
<!doctype html>
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
      <a style="padding-left:10px; padding-right:10px; float:right;" href="/signout.php"><button class="btn">Sign out</button></a>
      <a style="padding-left:10px; padding-right:10px; float:right;" href="javascript:toggle_show_irssi_setup();"><button class="btn btn-primary">Irssi Setup</button></a>
    </div>
  </div>
</div>

<div class="container">

  <?php if ($message != "") { ?>
  <div class="alert alert-info"><?php echo $message; ?> </div>
  <?php } ?>

  <h1>Your Recent Timeline</h1>
  <div class="row">
    <div style="margin-top: 5px;">
      <?php if ($timeline->getItems()) { ?>
        <?php foreach ($timeline->getItems() as $timeline_item) { ?>
        <div class="span4" style="height:175px; width:300px;">
          <div>
            <?php if(strlen($timeline_item->getHtml()) > 0) { ?>
              <?php echo $timeline_item->getHtml(); ?>
            <?php } else { ?>
              <?php echo htmlspecialchars($timeline_item->getText()); ?>
            <?php } ?>
          </div>
          <div style="margin-top:5px;">
            <form class="form-inline" method="post">
              <input type="hidden" name="itemId" value="<?php echo $timeline_item->getId(); ?>">
              <input type="hidden" name="operation" value="deleteTimelineItem">
              <button class="btn btn-danger btn-block" type="submit">Delete Item</button>
            </form>
          </div>
        </div>
        <?php 
        }
      } else { ?>
      <div class="span12">
        <div class="alert alert-info">
          You haven't added any items to your timeline yet. Use the controls
          below to add something!
        </div>
      </div>
      <?php
      } ?>
    </div>
  </div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="/static/bootstrap/js/bootstrap.min.js"></script>
<script>
function toggle_show_irssi_setup() {
  if($('#irssi_setup_div').length > 0) {
    $('#irssi_setup_div').remove();
  } else {
    show_irssi_setup();
  }  
}
function show_irssi_setup() {
  var help_string = '<div id="irssi_setup_div" class="alert alert-info">'
                      +'<div>Load the irssi.pl script like any other irssi script.</div>'
                      +'<div>Once loaded you must set up these parameters in irssi:'
                        +'<ul>'
                          +'<li style="border-bottom:none;">irssiglass_api_url: The URL of directory that irssi.php is in.</li>'
                          +'<li style="border-bottom:none;">irssiglass_api_token: Your user ID token (<?php echo $_SESSION['userid'];?>)</li>'
                        +'</ul>'
                      +'</div>'
                      +'<div>So, best case, just do this:'
                        +'<ul>'
                          +'<li style="border-bottom:none;">/set irssiglass_api_url <?php echo (($_SERVER['HTTPS']=='')?"http://":"https://").$_SERVER['HTTP_HOST'].str_replace('index.php','',$_SERVER['REQUEST_URI']).'irssi.php';?></li>'
                          +'<li style="border-bottom:none;">/set irssiglass_api_token <?php echo $_SESSION['userid'];?></li>'
                        +'</ul>'
                      +'</div>'
                    +'</div>';
  $("body").prepend($(help_string));
}
</script>
</body>
</html>
