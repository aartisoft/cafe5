<?php

require "../autoload.php";

switch ($_GET["sekce"]) {

case 'getCode':

	break;

case 'getToken':

	break;

case 'extendedToken':

	$instagram_feed = new cafe5_instagram_feed();
	$cron = $instagram_feed->cronExtendedToken( $instagram_feed->getAccessToken() );

	break;

default:

	$instagramFeedCron = new cafe5_instagram_feed();
	$getJson = $instagramFeedCron->cron();

	$instagramFeedAccessToken = new cafe5_instagram_feed();
	$accessToken = $instagramFeedAccessToken->cronExtendedToken( $instagramFeedAccessToken->getAccessToken() );

}

?>