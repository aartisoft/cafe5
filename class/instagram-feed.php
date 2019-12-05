<?php

/**
 * Get Instagram Feed.
 *
 * @author 		LiborMatějka
 * @category 	Feed / Instagram
 * @package 	cafe5/InstagramFeed
 * @version     0.1
 */

if (!class_exists('cafe5_instagram_feed')):

	class cafe5_instagram_feed {

		public $filename = "/hosting/www/cafe5.cz/www/json/json.txt";
		public $clientID = "4b1bff5bd54a4986b7d8bb517604dc14";
		public $clientSecret = "d02b4f5ee74347219ca8bb9275caa2ec";

		public function __construct() {

		}

		public function getAccessToken() {

			$apiKey = "IGQVJWd3JSY3BVdW12LWpIZAHlwR2pQTGFZARXNGV2ZAQOVdfX2liSzd5b25qZATlSczNpZA1hGMFMwU25ULWpjQ0tIRjdyM0dzZAHdHUmNIQXBONWwzd0VsRndfZAVhFbE1aMWx3MmU5bG5IcWNkLTVndkMxdVRFYzhtTEFRZAzdN";
			//https://rudrastyh.com/tools/access-token
			//https://instagram.pixelunion.net/#access_token=10816490980.1677ed0.d38e0f31bab84cc7b4faa2d80144625a

			return $apiKey;

		}

		function instagram_api_curl_connect($api_url) {

			$connection_c = curl_init(); // initializing
			curl_setopt($connection_c, CURLOPT_URL, $api_url); // API URL to connect
			curl_setopt($connection_c, CURLOPT_RETURNTRANSFER, 1); // return the result, do not print
			curl_setopt($connection_c, CURLOPT_TIMEOUT, 20);
			$json_return = curl_exec($connection_c); // connect and get json data
			curl_close($connection_c); // close connection

			return json_decode($json_return); // decode and return

		}

		public function getinstagramId() {

			return "17841410889402061";

		}

		public function writeDataToTxt($values) {

			$filename = $this->filename;
			$file = fopen($filename, "w");

			if (is_resource($file)) {

				fwrite($file, json_encode($values));
				fclose($file);

			} else {

				$logger = new cafe5_logger("Nepodařilo se zapsat JSON z Instagramu do txt souboru", "Error");

			}

			return true;

		}

		public function getJson() {

			$resultsNumber = '10';
			$url = 'https://graph.instagram.com/' . $this->getinstagramId() . '/media/?access_token=' . $this->getAccessToken() . "&fields=id,username,caption,permalink,media_url,thumbnail_url";

			if (ini_get('allow_url_fopen')) {

				$response = file_get_contents($url);
				$json_response = json_decode($response, TRUE);

			} else {

				if (function_exists('curl_version')) {

					$curl = curl_init();
					curl_setopt($curl, CURLOPT_URL, $url);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
					$response = curl_exec($curl);
					curl_close($curl);
					$json_response = json_decode($response, TRUE);

				} else {

					$json_response = NULL;

				}

			}

			if (!$json_response['error']) {

				$logger = new cafe5_logger("Instagram JSON vrátil status 200, což je OK :-)");

				// tady bude funkce, ktera projde json a ulozi obrazky a url obrazku zameni na url na serveru

				return $json_response;

			} else {

				$logger = new cafe5_logger("Instagram JSON vrátil chybu" . $json_response['meta']["code"] . " s chybovou hláškou" . $json_response['meta']["error_message"], "Error");

				$json_response = NULL;

				return $json_response;

			}

		}

		public function loadJsonFromFile() {

			ini_set('allow_url_fopen', 1);
			$filename = $this->filename;

			$token = fopen($filename, 'r');
			$content = fread($token, filesize($filename));
			fclose($token);

			return $content;

		}

		public function parseFeed($json) {

			$json_response = json_decode($this->loadJsonFromFile(), true);
			$returnString = NULL;

			if ($json_response) {

				$i = 0;

				$returnString .= "<div id='youtubeFeed'>";

				$returnString .= '<div class="youtube-channel-videos"">';

				foreach ($json_response['data'] as $item) {

					$videoTitle = $item['caption'];
					$videoID = $item['id'];
					$text = $item['caption'];
					$videoThumbnail = $item['media_url'];
					$link = $item["permalink"];

					if (($videoTitle && $videoID) && $i < 16) {

						if ($i == 0 or $i == 4 or $i == 8 or $i == 12) {
							$returnString .= '<div class="row">';
						}

						$returnString .= '<div class="col-md-3">';

						$returnString .= '<div class="menu-entry">';

						$returnString .= "<a target='_blank' title='" . $videoTitle . "' href='" . $link . "' class='img' style='background-image: url(" . $videoThumbnail . ");'></a>";

						$returnString .= '</div>';

						$returnString .= '</div>';

						if ($i == 3 or $i == 7 or $i == 11 or $i == 15) {
							$returnString .= '</div>';
						}

						$i++;

					}

				}

				$returnString .= '</div><!-- .youtube-channel-videos -->';
				$returnString .= '</div><!-- #youtubeFeed -->';

			} else {

				$returnString .= '<div class="youtube-channel-videos error"><p>No videos are available at this time from the channel specified!</p></div>';

				$logger = new cafe5_logger("Žádny záznam v JSON Instagramu", "Error");

			}

			return $returnString;

		}

		public function getFeed() {

			$load_json = $this->loadJsonFromFile();

			return $this->parseFeed($load_json);

		}

		public function cron() {

			$logger = new cafe5_logger("Spuštěn Instagram cron ke stažení JSON z Instagramu");

			$get_json = $this->getJson();

			if ($get_json) {

				$writeData = $this->writeDataToTxt($get_json);
				$loggerOK = new cafe5_logger("Byl zapsán Instagram JSON do txt");

				echo "OK";

			} else {

				$loggerError = new cafe5_logger("Nebyl zapsán Instagram JSON do txt z důvodu chyby", "Error");

				echo "error";

			}

			$loggerEnd = new cafe5_logger("Ukončen Instagram cron");

		}

		public function saveImage($id, $url) {

			ini_set('allow_url_fopen', 1);

			$fullpath = CAFE5__SERVER_IMAGE_INSTAGRAM_DIR;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);

			$data = curl_exec($ch);
			curl_close($ch);

			if (is_writable(CAFE5__SERVER_IMAGE_INSTAGRAM_DIR)) {

				file_put_contents($fullpath . $id . ".jpg", $data);
				return true;

			} else {

				$loggerError = new cafe5_logger("Instagram složka není zapisovatelná.", "Error");
				return false;
			}

		}

		public function __toString() {

			return "Bacha, vypisuješ objekt...";

		}

		public function parseCSVTest() {

			$igThumbnails = [];

			try {
				$arrContextOptions = array(
					"ssl" => array(
						"verify_peer" => false,
						"verify_peer_name" => false,
					),
				);

				$ig = file_get_contents("https://www.instagram.com/instagram/", false,
					stream_context_create($arrContextOptions));
				$ig_json = [];

				preg_match('/">.+?[=].?({.+);\<\//', $ig, $ig_json);

				$edges = json_decode($ig_json[1],

					true)["entry_data"]["ProfilePage"][0]["graphql"]["user"]["edge_owner_to_timeline_media"]["edges"];

				foreach ($edges as $edge) {
					$igThumbnails[] = [
						"shortcode" => $edge["node"]["shortcode"],
						"thumbnail_src" => $edge["node"]["thumbnail_src"],
					];
					if (count($igThumbnails) == 12) {
						break;
					}
				}

			} catch (\Exception $e) {}

			var_dump($edges);

			return $edges;
		}

		public function parse_test2() {

			$url = "https://www.instagram.com/pasospardubice/?__a=1";
			$url = sprintf("https://www.instagram.com/pasospardubice/");
			ini_set("allow_url_fopen", 1);
			$username = "pasospardubice";

			$url = "https://www.instagram.com/' . $username . '/?__a=1";

			$connection_c = curl_init(); // initializing

			curl_setopt($connection_c, CURLOPT_URL, $url); // API URL to connect
			curl_setopt($connection_c, CURLOPT_POST, TRUE);
			curl_setopt($connection_c, CURLOPT_TIMEOUT, 20);
			curl_setopt($connection_c, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($connection_c, CURLOPT_RETURNTRANSFER, 1); // return the result, do not print

			$data = curl_exec($connection_c);
			curl_close($connection_c);

			echo "<pre>";
			echo $data;
			echo "</pre>";

			return $data;

		}

		public function getCode() {

			// https://developers.facebook.com/docs/instagram-basic-display-api/

			$instagramId = urlencode("530800384432056");
			$instagramAppSecret = urlencode("b5a6ef471345931fe850a48c3b130cd6");
			$redirectUri = urlencode("https://www.cafe5.cz/auth/");

			$api_url = "https://api.instagram.com/oauth/authorize?app_id=" . $instagramId . "&redirect_uri=" . $redirectUri . "&scope=user_profile,user_media&response_type=code";

			return $api_url;

		}

		public function getNewAccessToken($code = NULL) {

			$appId = htmlspecialchars("530800384432056");
			$instagramUsername = "cafe5_prague";
			$appSecret = htmlspecialchars("b5a6ef471345931fe850a48c3b130cd6");
			$redirectUri = urlencode("https://www.cafe5.cz/auth/");
			$codeTrimmed = str_replace('#_', '', urldecode($code));

			$api_url = "https://api.instagram.com/oauth/access_token";

			$dataCurl = array(

				'app_id' => $appId,
				'app_secret' => $appSecret,
				'grant_type' => 'authorization_code',
				'redirect_uri' => $redirectUri,
				'code' => $codeTrimmed,

			);

			//$headers = array('Content-Type: multipart/form-data');

			$connection_c = curl_init(); // initializing
			curl_setopt($connection_c, CURLOPT_URL, $api_url); // API URL to connect
			curl_setopt($connection_c, CURLOPT_POST, TRUE);
			curl_setopt($connection_c, CURLOPT_TIMEOUT, 20);
			curl_setopt($connection_c, CURLOPT_POSTFIELDS, $dataCurl);
			curl_setopt($connection_c, CURLOPT_SSL_VERIFYPEER, FALSE);
			//curl_setopt($connection_c, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($connection_c, CURLOPT_RETURNTRANSFER, 1); // return the result, do not print
			$json_return = curl_exec($connection_c); // connect and get json data
			$http_code = curl_getinfo($connection_c, CURLINFO_HTTP_CODE);
			curl_close($connection_c); // close connection

			return $json_return . "<br><br>" . $codeTrimmed;

		}

	} // end class

endif;

?>