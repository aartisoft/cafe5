<?php

/**
 * Get Instagram Feed.
 *
 * @author 		LiborMatějka
 * @category 	CSV
 * @package 	cafe5/parseCSV
 * @version     0.1
 */
if (!class_exists('cafe5_parse_csv')):

	class cafe5_parse_csv {

		public $breakfestUrl;

		function __construct($url) {

			$this->breakfestUrl = $url;

		}

		public function showMenu() {

			$rows = explode("\n", $this->curl($this->breakfestUrl));
			$s = array();
			$returnString = NULL;

			foreach ($rows as $i => $row) {
				$s[] = str_getcsv($row);

				if ($i > 0) {

					$menuExplode = explode(",", $row);

					$returnString .= '<div class="pricing-entry d-flex ftco-animate fadeInUp ftco-animated">';

					//$returnString .= '<div class="img" style="background-image: url(' . $s[$i][4] . ');"></div>';

					//$returnString .= '<div class="desc pl-3">';

					$returnString .= '<div class="desc" style="width: 100%;">';

					$returnString .= '<div class="d-flex text align-items-center">';
					$returnString .= '<h3><span>' . $s[$i][0] . '</span></h3>';
					$returnString .= '<span class="price">' . $s[$i][3] . ' CZK</span>';
					$returnString .= '</div>';

					$returnString .= '<div class="d-block">';
					$returnString .= '<p>' . $s[$i][1] . '</p>';
					$returnString .= '</div>';
					$returnString .= '</div>';
					$returnString .= '</div>';

				}
			}

			return $returnString;

		}

		public function curl($url) {

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
			$response = curl_exec($curl);
			curl_close($curl);

			return $response;

		}

		public function __toString() {

			return "Vypisujes tridu";

		}

	}

endif;
?>