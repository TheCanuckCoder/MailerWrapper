<?php
namespace HCMailer2017;
/**
 * PSR-3 Logger Interface Implementation
 *
 * @package HCMailWrapper
 * @subpackage Class PHPMailer() and Class SMTP()
 *
 * This is a simple Logger implementation that other Loggers can inherit from.
 *
 * It simply delegates all log-level-specific methods to the `log` method to
 * reduce boilerplate code that a simple Logger that does the same thing with
 * messages regardless of the error level has to implement.
 *
 */
use Psr\Log\AbstractLogger;
/**
 * MailLogger Class for logging information from the HCMailWrapper
 */
class MailLogger extends AbstractLogger {
   /**
    * log Method
	*
	* @see \Psr\Log\AbstractLogger()
	* @param string \Psr\Log\LogLevel::<LEVEL>
	*   For <LEVEL> Constants see \Psr\Log\LogLevel.php
	* @param string $message
	*   The message to send the user
	* @param array $context
	*   Replacements for message variables 
	* (See \Psr\Log\LoggerInterface.php for more information)
	*
	* @return void
	*/
	public function log($level, $message, array $context = array(), $type = 'email', $log = 'file') {
		$connection_log = 'logs/connection_log_' . date('m-d-Y') . '.log';
		$email_log = 'logs/email_log_' . date('m-d-Y') . '.log';
		$misc_log = 'logs/misc_log_' . date('m-d-Y') . '.log';
		switch ($type) {
			case 'email':
				if ($log == 'file') {
					$logFile = @fopen($email_log, 'a+');
					@fwrite($logFile, strtr(strip_tags($message), $context));
				} else if ($log == 'both') {
					$logFile = @fopen($email_log, 'a+');
					@fwrite($logFile, strtr(strip_tags($message), $context));
					echo strtr(nl2br($message), $context);
				} else if ($log == 'none') {
					return null;
				} else {
					echo strtr(nl2br($message), $context);
				}
				break;
			case 'connection':
				if ($log == 'file') {
					if (file_exists($connection_log) && filesize($connection_log) > 42000000) { // greater than 42MB
						unlink($connection_log); // delete log and restart it (could send notification or archive log here if needed)
					}
					$logFile = @fopen($connection_log, 'a+');
					@fwrite($logFile, strtr(strip_tags($message), $context));
				} else if ($log == 'both') {
					if (file_exists($connection_log) && filesize($connection_log) > 42000000) { // greater than 42MB
						unlink($connection_log); // delete log and restart it (could send notification or archive log here if needed)
					}
					$logFile = @fopen($connection_log, 'a+');
					@fwrite($logFile, strtr(strip_tags($message), $context));
					echo strtr(nl2br($message), $context);
				} else if ($log == 'none') {
					return null;
				} else {
					echo strtr(nl2br($message), $context);
				}
				break;
			default:
				if ($log == 'file') {
					$logFile = @fopen($misc_log, 'a+');
					@fwrite($logFile, strtr(strip_tags($message), $context));
				} else if ($log == 'both') {
					$logFile = @fopen($misc_log, 'a+');
					@fwrite($logFile, strtr(strip_tags($message), $context));
					echo strtr(nl2br($message), $context);
				} else if ($log == 'none') {
					return null;
				} else {
					echo strtr(nl2br($message), $context);
				}
				break;
		}
	}
	/*
	 * Email received, reply will be sent method
	 *
	 * @access protected
	 * @description Logs actions if the user chooses to
	 *
	 * @see self::\Psr\Log\AbstractLogger();
	 *
	 * @return void
	 *   writes a log to the log file in the logs folder.
	 */
	public static function _logActions($log = false, $logLevel = 'info', $message = '', $data = array(), $type = 'email', $logType = 'file') {
		if (!isset($logLevel) || $logLevel == '') {
			$logLevel = \Psr\Log\LogLevel::INFO;
		} else {
			switch ($logLevel) {
				case 'emergency':
					$logLevel = \Psr\Log\LogLevel::EMERGENCY;
					break;
				case 'alert':
					$logLevel = \Psr\Log\LogLevel::ALERT;
					break;
				case 'critical':
					$logLevel = \Psr\Log\LogLevel::CRITICAL;
					break;
				case 'error':
					$logLevel = \Psr\Log\LogLevel::ERROR;
					break;
				case 'warning':
					$logLevel = \Psr\Log\LogLevel::WARNING;
					break;
				case 'notice':
					$logLevel = \Psr\Log\LogLevel::NOTICE;
					break;
				case 'info':
					$logLevel = \Psr\Log\LogLevel::DEBUG;
					break;
				default:
					$logLevel = \Psr\Log\LogLevel::INFO;
					break;
			}
		}
		if (isset($log) && is_bool($log) && $log && isset($message) && trim($message) > '' && isset($data) && is_array($data)) {
			$logger = new MailLogger();
			$logger->log($logLevel, $message, $data, $type, $logType);
		}
	}
}