<?php

require_once 'config.php';
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_Oauth2Service.php';

// Returns an unauthenticated service
function get_google_api_client() {
  global $api_client_id, $api_client_secret, $api_simple_key, $base_url;
  session_start();

  $client = new Google_Client();

  $client->setUseObjects(true);
  $client->setApplicationName('IrssiGlass');

  // These are set in config.php
  $client->setClientId($api_client_id);
  $client->setClientSecret($api_client_secret);
  $client->setRedirectUri($base_url."/oauth2callback.php");

  $client->setScopes(array(
    'https://www.googleapis.com/auth/glass.timeline',
    'https://www.googleapis.com/auth/glass.location',
    'https://www.googleapis.com/auth/userinfo.profile'));

  return $client;
}

/*
 * Verify the credentials. If they're broken, attempt to re-auth
 * This will only work if you haven't printed anything yet (since
 * it uses an HTTP header for the redirect)
 */
function verify_credentials($credentials) {
  global $base_url;

  $client = get_google_api_client();
  $client->setAccessToken($credentials);

  $token_checker = new Google_Oauth2Service($client);
  try {
    $token_checker->userinfo->get();
  } catch (Google_ServiceException $e) {
    if ($e->getCode() == 401) {
      // This user may have disabled the Glassware on MyGlass.
      // Clean up the mess and attempt to re-auth.
      unset($_SESSION['userid']);
      header('Location: ' . $base_url . '/oauth2callback.php');
      exit;
    } else {
      // Let it go...
      throw $e;
    }
  }
}

function insert_timeline_item($service, $timeline_item, $content_type, $attachment) {
  try {
    $opt_params = array();
    if ($content_type != null && $attachment != null) {
      $opt_params['data'] = $attachment;
      $opt_params['mimeType'] = $content_type;
    }
    return $service->timeline->insert($timeline_item, $opt_params);
  } catch (Exception $e) {
    print 'An error ocurred: ' . $e->getMessage();
    return null;
  }
}

/**
 * Delete a timeline item for the current user.
 *
 * @param Google_MirrorService $service Authorized Mirror service.
 * @param string $item_id ID of the Timeline Item to delete.
 */
function delete_timeline_item($service, $item_id) {
  try {
    $service->timeline->delete($item_id);
  } catch (Exception $e) {
    print 'An error occurred: ' . $e->getMessage();
  }
}

