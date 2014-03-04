<?php
/*
* Copyright (C) 2013 Google Inc.
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
*      http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/
//  Author: Jenny Murphy - http://google.com/+JennyMurphy

require_once 'config.php';
require_once 'mirror-client.php';
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_MirrorService.php';
require_once 'util.php';

$client = get_google_api_client();

if(isset($_POST['irssikey'])) {
  $_SESSION['userid'] = $_POST['irssikey'];
}
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

$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
$message = $_POST['message'];
// Check if there are URLs in the message
$menu_items = array();
if(preg_match($reg_exUrl, $message, $url)) {
  $menu_value = new Google_MenuValue();
  $menu_value->setDisplayName("Open URL");
  $menu_item = new Google_MenuItem();
  $menu_item->setAction("OPEN_URI");
  $menu_item->setValues(array($menu_value));
  $menu_item->setPayload($url[0]);
  array_push($menu_items, $menu_item);
}

$message_to_send = "<article class='auto-paginate'>"
                      ."<p class='text-small' style='border-bottom: 1px solid #666'>"
                        ."<span>irssi</span>"
                        ."<span class='yellow'>".$_POST['channel']."</span>"
                        ."<span class='blue'>&lt;".$_POST['nick']."&gt;</span>"
                      ."</p>"
                      ."<p class='section text-large'>".$_POST['message']."</p>"
                    ."</article>";
$new_timeline_item = new Google_TimelineItem();
$new_timeline_item->setHtml($message_to_send);

$notification = new Google_NotificationConfig();
$notification->setLevel("DEFAULT");
$new_timeline_item->setNotification($notification);

if(count($menu_items) > 0) {
  $new_timeline_item->setMenuItems($menu_items);
}
if (isset($_POST['imageUrl']) && isset($_POST['contentType'])) {
  insert_timeline_item($mirror_service, $new_timeline_item,
      $_POST['contentType'], file_get_contents($_POST['imageUrl']));
} else {
  insert_timeline_item($mirror_service, $new_timeline_item, null, null);
}

header('Content-type: application/json');
echo json_encode(array('status' => 'OK'));
?>

