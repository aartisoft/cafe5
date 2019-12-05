<?php
/**
 * Logger
 *
 * @author 		LiborMatějka
 * @category 	Logger
 * @package 	cafe5/Logger
 * @version     0.1
 */

if (!class_exists('cafe5_logger')):

	class cafe5_logger {

		private $log_filename;

		public function __construct($message, $status = null) {

			if ($message != NULL) {
				$this->message = $message;

				$t = microtime(true);

				$micro = sprintf("%06d", ($t - floor($t)) * 1000000);
				$d = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));

				if ($status != NULL) {
					$status = strtoupper($status);
					$file_data = "[" . $status . "] " . $d->format("Y-m-d H:i:s.u") . " - " . $this->message;

				} else {
					$file_data = $d->format("Y-m-d H:i:s.u") . " - " . $this->message;
				}

				$final_data = mb_convert_encoding($file_data, 'UTF-8', 'auto');
				$log_filename = "/hosting/www/cafe5.cz/www/log/log.log";

				file_put_contents($log_filename, $final_data . "\n", FILE_APPEND);

			}

		}

		public function __toString() {

			return $log_file_data;

		}

	}

endif;