<?php
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
namespace HCMailer2017;
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
		switch ($type) {
			case 'email':
				if ($log == 'file') {
					$logFile = @fopen('logs/email_log_' . date('m-d-Y') . '.log', 'a+');
					@fwrite($logFile, strtr(strip_tags($message), $context));
				} else if ($log == 'both') {
					$logFile = @fopen('logs/email_log_' . date('m-d-Y') . '.log', 'a+');
					@fwrite($logFile, strtr(strip_tags($message), $context));
					echo strtr(nl2br($message), $context);
				} else {
					echo strtr(nl2br($message), $context);
				}
				break;
			case 'connection':
				if ($log == 'file') {
					$logFile = @fopen('logs/connection_log_' . date('m-d-Y') . '.log', 'a+');
					@fwrite($logFile, strtr(strip_tags($message), $context));
				} else if ($log == 'both') {
					$logFile = @fopen('logs/connection_log_' . date('m-d-Y') . '.log', 'a+');
					@fwrite($logFile, strtr(strip_tags($message), $context));
					echo strtr(nl2br($message), $context);
				} else {
					echo strtr(nl2br($message), $context);
				}
				break;
			default:
				if ($log == 'file') {
					$logFile = @fopen('logs/misc_log_' . date('m-d-Y') . '.log', 'a+');
					@fwrite($logFile, strtr(strip_tags($message), $context));
				} else if ($log == 'both') {
					$logFile = @fopen('logs/misc_log_' . date('m-d-Y') . '.log', 'a+');
					@fwrite($logFile, strtr(strip_tags($message), $context));
					echo strtr(nl2br($message), $context);
				} else {
					echo strtr(nl2br($message), $context);
				}
				break;
		}
	}
}