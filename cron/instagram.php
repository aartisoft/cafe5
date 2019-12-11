<?php

require "../autoload.php";

$instagramFeedCron = new cafe5_instagram_feed();
$getJson = $instagramFeedCron->cron();

$instagramFeedAccessToken = new cafe5_instagram_feed();
$accessToken = $instagramFeedAccessToken->cronExtendedToken($instagramFeedAccessToken->getAccessToken());

?>