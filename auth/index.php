<?php

include_once "../class/instagram-feed.php";
$cafe5_instagram_feed = new cafe5_instagram_feed();

echo "<a href='" . $cafe5_instagram_feed->getCode() . "' >" . $cafe5_instagram_feed->getCode() . "</a>";

if (isset($_REQUEST["code"])) {

	echo $cafe5_instagram_feed->getNewAccessToken($_REQUEST["code"]);

}
?>