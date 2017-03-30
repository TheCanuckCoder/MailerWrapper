<?php
/**
 *
 * Mailer Class
 * PHP Version 5.3.1
 *
 * A new mailer wrapper for created in March 2017 to replace old legacy SMTP mailers withing HC/PHAC
 * For more information see: 
 * {@link http://gitlab.ssc.etg.gc.ca/sustaining-applications/CSB-PHPMailerWrapper-SMTP-MAIL-SENDMAIL-2017 Gitlab}
 *
 * @package 	HCMailWrapper
 * @subpackage	Class PHPMailer() and Class SMTP()
 * @author	 	Steven Scharf (steven.scharf@canada.ca)
 * @since		2017/04/24
 * @access		public
 * @link 		http://gitlab.ssc.etg.gc.ca/sustaining-applications/CSB-PHPMailerWrapper-SMTP-MAIL-SENDMAIL-2017
 * @license 	http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 *
 * @version: 	Beta (1.0RC)
 * @todo 		None
 *
 * These application defaults are:
 * host: 				See class variables
 * port: 				See class variables
 * username: 			See class variables
 * password: 			See class variables
 * from: 				NULL
 * to: 					NULL
 * replyto: 			NULL
 * send_reply_message: 	true
 * reply_subject: 		NULL
 * subject: 			NULL
 * html_body: 			NULL
 * nonhtml_body: 		NULL
 * attachment: 			NULL
 * mail_method: 		smtp
 * encryption: 			tls
 * authorization: 		true
 * ipv6compat: 			false
 * timezone: 			America/Toronto
 * language: 			en
 * openList: 			<ul>
 * closeList: 			</ul>
 * prefixListItem: 		<li>
 * postListItem: 		</li>
 * reply_message: 		(see mail-main.lang-fr.php)
 * redirect: 			NULL
 * max_file_size: 		2000000
 * all_fields: 			NULL
 * required_fields: 	NULL
 * isHtml: 				true
 * debug: 				false
 * testConnection: 		false
 * SMTPDebugLevel: 		2
 * logType: 			file
 *
 */
namespace HCMailer2017;
class HCMailWrapper extends PHPMailer {
	/*
	 * Public Variables
	 */
	public $language = 'en'; // default 'en', accepts 'en' or 'fr'
	public $MAIL_TIMEZONE_SET = 'America/Toronto'; // current timezone, needed for PHPMailer
	/*
	 * Language based variables, see languages/mail-main.lang-fr.php
	 */
	public $MAIL_UNKNOWN_ERROR = 'There was an unknown error, please contact the site administrator.';
	public $MAIL_TECH_ERROR = 'There was a technical error, please contact the site administrator.';
	public $MAIL_ERROR = 'Mail error, please contact the site administrator.';
	public $MAIL_ERROR_PREFIX = 'Mailer error: ';
	public $MAIL_SENT = 'Your message has been sent.';
	public $FORM_ERROR_MESSAGE = 'There was an error with your form, see the errors below and correct them to proceed.';
	public $EMAIL_RECIEVED_REPLY = "This is an auto-generated e-mail; please do not reply.\r\n Your message has been received by the Web site administrator and is being forwarded to a subject-matter expert for consideration and a timely response.\r\n Thank you for your interest in Health Canada Online.";
	public $ATTACHMENT_ERROR = "Invalid file extension, please select a file that matches one of the following extensions: ";
	public $CONNECTION_TEST = "Connection Test is Active, no e-mails will be sent (SMTP Only).";
	// Custom Message return (used only if $returnType is set to 'message')
	public $customReturnMessage = 'Thanks for contacting us!';
	public $reply_subject = NULL;
	public $reply_message = NULL;
	/*
	 * Protected Variables
	 */
	// Debug...can be set outside of this class no need to change this
	protected $debug = false; // turn debugging on or off (true|false)
	protected $testConnection = false; // Test connection only (no e-mails sent)
	/**
     * SMTP Debug output level:
     * * 0 = No debug output, default
     * * 1 = Client commands
     * * 2 = Client commands and server responses (default)
     * * 3 = As DEBUG_SERVER plus connection status
     * * 4 = Low-level data output, all messages
     */
	protected $SMTPDebugLevel = 2;
	// Debug output type Options: html|echo|error_log
	protected $debugOutputType = 'html';
	// Mailer object
	protected $mailer;
	// From
	protected $from;
	// Reply-to
	protected $replyto;
	// To Address
	protected $toaddress;
	// Mail Subject
	protected $subject = NULL;
	protected $isHtml = true;
	// Mail HTML Body
	protected $html_body = NULL;
	// Mail Non-HTML Plain Text Body
	protected $nonhtml_body = '';
	// Mail Attachment
	protected $attachment = NULL; // for testing, remove prior to production release
	// Class Return type
	protected $returnType = 'message'; // Options: message|redirect|debug
	// Redirect page (used only if $returnType is set to 'redirect')
	// must be relative (can redirect to page with 
	// header for external domain redirection)
	protected $redirectPage = '/somedir/somepage.php'; 
	// Error Type variable
	protected $errorType = NULL;
	// Confirmation e-mail settings
	protected $sendReplyMessage = true;
	protected $sendFormData = true;
	// Fields Submitted/Validation
	protected $required_fields;
	protected $allFields;
	// Upload file size maximum
	protected $maxFileSize = 2000000;
	// HTML Print outs for errors
	protected $openList = '<ul>';
	protected $closeList = '</ul>';
	protected $prefixListItem = '<li>';
	protected $postListItem = '</li>';
	/*
	 * Private Variables
	 */
	// Credentials for SMTP
	private $host = '';
	private $mailMethod = 'smtp';
	private $MailAuth = true; // SMTP Requires Authentication
	private $IPV6Compat = false; // if your network does not support SMTP over IPv6
	private $port = 587; // Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
	private $MailSecure = 'tls'; //Set the encryption system to use - ssl (deprecated) or tls
	private $numberOfArgs;
	private $getArgs;
	// allowed extensions for HTML Body File E.g.: content.html
	private $allowed =  array('html', 'phtml');
	private $replySent = false;
	private $validation_success = true;
	private $fields;
	private $requiredFields;
	private $message;
	private $fileContents;
	private $uploads_dir = './../uploads/';
	private $allowed_extensions = array('gif','png','jpg','jpeg','doc','docx','pdf','xls','xlsx');
	private $sendErrorReport = NULL;
	private $fileError = true;
	private $filesUploaded = array();
	private $refererEmail = array('canada.ca', 'hc-sc.gc.ca', 'list.hc-sc.gc.ca', 'chemicalsubstanceschimiques.gc.ca'); // list of email domain names
	private $refererSite = array('mailer.dev', 'canada.ca', 'web.hc-sc.gc.ca', 'www.hc-sc.gc.ca', 'hc-sc.gc.ca','www.sc-hc.gc.ca/','sc-hc.gc.ca/', '205.193.190.11'); // list of site domain names
	private $versionInfo = 'Version 1.0 (Beta)';
	private $logger = NULL;
	private $logActions = false;
	private $logType = 'file';
	private $connIncrementer = 0;
	/*
	 * Construct Method, all that's needed to invoke the mailer
	 *
	 * @description This does all the work to send emails
	 *
	 * @return string
	 */
	public function __construct() {
		// Check if config.php exists prior to running anything
		if (!file_exists('config.php')) {
			echo "You must have a configuration file set up for this service. Please visit the respository where you got this code for more details.<br>http://gitlab.ssc.etg.gc.ca/sustaining-applications/CSB-PHPMailerWrapper-SMTP-MAIL-SENDMAIL";
			exit;
		} else {
			require_once "config.php";
		}
		// Logger
		$this->logger = new MailLogger();
		// Calling PHPMailer Construct
		parent::__construct();
		// Checking arguments submitted
		$this->numberOfArgs = func_num_args();
		$this->getArgs = func_get_args();
		$arguments = $this->getArgs;
		if (isset($arguments[0])) {
			$arguments = (object)$arguments[0]; // getting the first and only required argument
			// SMTP needs accurate times, and the PHP time zone MUST be set
			// This should be done in your php.ini, but this is how to do it if you don't have access to that
			if (isset($arguments->MAIL_TIMEZONE_SET) && trim($arguments->MAIL_TIMEZONE_SET) > '') {
				$this->MAIL_TIMEZONE_SET = $arguments->MAIL_TIMEZONE_SET;
			}
			// Check for log variables submitted
			if (isset($arguments->logActions) && trim($arguments->logActions) > '') {
				$this->logActions = $arguments->logActions;
			}
			if (isset($arguments->logType) && trim($arguments->logType) > '') {
				$this->logType = $arguments->logType;
			}
			// Uploaded/Images directory setting
			$this->uploads_dir = str_replace("//", "/", dirname(__FILE__) . '/' . $this->uploads_dir);
			// Switch to plain-text if user requests it
			if (isset($arguments->isHtml) && $arguments->isHtml === false) {
				$this->isHTML = false;
				$this->isHtml = false;
			} else {
				// Check for sendFormData
				if (isset($arguments->sendFormData) && $arguments->sendFormData === false) {
					$this->sendFormData = false;
				}
			}
			// Check for reply subject
			if (isset($arguments->reply_subject)) {
				$this->reply_subject = $arguments->reply_subject;
			} else {
				$this->reply_subject = false;
			}
			// Check if the user submitted a personal reply_message
			if (isset($arguments->reply_message)) {
				$this->reply_message = $arguments->reply_message;
			} else {
				$this->reply_message = false;
			}
			// Check for redirect
			if (isset($arguments->redirect) && trim($arguments->redirect) > '') {
				$this->redirectPage = $arguments->redirect;
				$this->returnType = 'redirect';
			}
			// Check required fields entry
			if (isset($arguments->required_fields) && trim($arguments->required_fields) > '') {
				$this->required_fields = $arguments->required_fields;
			}
			// Check all fields allowed for output to e-mail
			if (isset($arguments->all_fields) && trim($arguments->all_fields) > '') {
				$this->allFields = $arguments->all_fields;
			}
			// Check for max file size
			if (isset($arguments->max_file_size) && trim($arguments->max_file_size) > '') {
				$this->maxFileSize = $arguments->max_file_size;
			}
			// Check for max file size
			if (isset($arguments->allowed_extensions) && !empty($arguments->allowed_extensions)) {
				$this->allowed_extensions = $arguments->allowed_extensions;
			}
			// Check if user set open list item var
			if (isset($arguments->openList) && trim($arguments->openList) > '') {
				$this->openList = $arguments->openList;
			}
			// Check if user set close list item var
			if (isset($arguments->closeList) && trim($arguments->closeList) > '') {
				$this->closeList = $arguments->closeList;
			}
			// Check if user set prefix list item var
			if (isset($arguments->prefixListItem) && trim($arguments->prefixListItem) > '') {
				$this->prefixListItem = $arguments->prefixListItem;
			}
			// Check if user set post list item var
			if (isset($arguments->postListItem) && trim($arguments->postListItem) > '') {
				$this->postListItem = $arguments->postListItem;
			}
			if (isset($arguments->testConnection) && is_bool($arguments->testConnection)) {
				$this->testConnection = $arguments->testConnection;
			}
			// Check if debug level was part of config
			if (isset($arguments->SMTPDebugLevel) && is_bool($arguments->SMTPDebugLevel)) {
				$this->SMTPDebugLevel = $arguments->SMTPDebugLevel;
			}
			// Seeing if the user submitted refererEmail
			if (isset($arguments->refererEmail) && is_array($arguments->refererEmail)) {
				$this->refererEmail = $arguments->refererEmail;
			}
			// Seeing if the user submitted refererSite
			if (isset($arguments->refererSite) && is_array($arguments->refererSite)) {
				$this->refererSite = $arguments->refererSite;
			}
			// Timezone Default setting
			date_default_timezone_set($this->MAIL_TIMEZONE_SET);
			// Validation method (returns true of false)
			if ($this->_validateArgs() && $this->_refererDomain()) { // arguments are valid
				// Validate PHPMailer settings
				if ($validateEmailSettings = $this->_validateEmailSettings($arguments) && !$this->testConnection) {
					// Setting the body
					$this->setBody($arguments);
					// Check if we are just testing the connection
					// Calling output method
					$this->output();
					// Check if we are sending a custom reply message
					if (isset($arguments->send_reply_message) && $arguments->send_reply_message === true) {
						$this->sendReplyMessage = $arguments->send_reply_message;
					}
					// Sending custom reply message, emailRecievedReply
					// will carry the custom message or the stock one
					if (isset($this->sendReplyMessage) && $this->sendReplyMessage === true && $this->replySent === false) {
						$this->emailRecievedReply();
					}
				} else if ($validateEmailSettings && $this->testConnection) {
					echo '<p>' . $this->CONNECTION_TEST . '</p>';
				} else if (!$validateEmailSettings && $this->testConnection) {
					// Invoke the tech error method (see method for details)
					echo '<p>' . $this->CONNECTION_TEST . '</p>';
					$this->_techError();
				} else if (!$validateEmailSettings) {
					// Invoke the tech error method (see method for details)
					$this->_techError(); 
				}
			} else { // invalid arguments
				// Invoke the tech error method (see method for details)
				$this->_techError(); 
			}
		} else { // invalid arguments
			// Invoke the tech error method (see method for details)
			$this->_techError(); 
		}
	}
	/*
	 * Connection switching depending on 
	 * what is returned from the SMTP
	 *
	 * @access public
	 * @description This checks the SMTP for a
	 * valid connection, failure will lead to 
	 * a connection switch with a txt file written
	 *
	 * @see self::connectionSMTPTest();
	 * @param $arguments
	 *   Object of configuration options sent by the implementer
	 * @param $attempt
	 *   Which attempt is taking place. Can be 1 or 2 (2 total attempts)
	 * @return boolean
	 *   True if we had a successful connection, false otherwise
	 */
	private function connectionDetermination($arguments, $attempt = 1) {
		// Incrementer
		$this->connIncrementer++;
		if ($attempt == 2) {
			$this->Username = MAIL_USER2;
			$this->Password = MAIL_PASS2;
			$message = '<strong>Connection Attempt (User 2) #' . $this->connIncrementer . ' (' . date('F d, Y h:ia') . '):</strong>' . PHP_EOL . '<strong>Host:</strong> ' . $this->Host . PHP_EOL . PHP_EOL;
			$data = array();
			self::_logActions($this->logActions, 'info', $message, $data, 'connection', $this->logType);
			if ($this->connectionSMTPTest($attempt)) {
				return true;
			}
			return false;
		} else if ($attempt == 1) {
			$message = '<strong>Connection Attempt (User 1) #' . $this->connIncrementer . '(' . date('F d, Y h:ia') . '):</strong>' . PHP_EOL . '<strong>Host:</strong> ' . $this->Host . PHP_EOL . PHP_EOL;
			$data = array();
			self::_logActions($this->logActions, 'info', $message, $data, 'connection', $this->logType);
			if ($this->connectionSMTPTest($attempt)) {
				return true;
			}
		}
		return false;
	}
	/*
	 * Connection testing SMTP
	 *
	 * @access private
	 * @description This checks the SMTP for a valid connection
	 *
	 * @see SMTP Class (class.smtp.php)
	 * @param $attempt
	 *   Which attempt is taking place. Can be 1 or 2 (2 total attempts)
	 *
	 * @return boolean
	 *   True if we had a successful connection, false otherwise
	 */
	private function connectionSMTPTest($attempt = 1) {
		// Create a new SMTP instance
		$smtp = new SMTP;
		// Enable connection-level debug output
		// If debugging is on 
		if ($this->debug) {
			$smtp->do_debug = $this->SMTPDebugLevel;
			$smtp->Debugoutput = function($str, $level) {echo "debug level $level;<br>message: $str<br>";};
		}
		// Try/Catch the rest
		try {
			// Connect to an SMTP server
			if (!$smtp->connect($this->Host, $this->Port)) {
				return false;
			}
			// Say hello
			if (!$smtp->hello(gethostname())) {
				return false;
			}
			// Get the list of ESMTP services the server offers
			$e = $smtp->getServerExtList();
			// If server can do TLS encryption, use it
			if (is_array($e) && array_key_exists('STARTTLS', $e)) {
				$tlsok = $smtp->startTLS();
				if (!$tlsok) {
					return false;
				}
				// Repeat EHLO after STARTTLS
				if (!$smtp->hello(gethostname())) {
					return false;
				}
				// Get new capabilities list, which will usually now include AUTH if it didn't before
				$e = $smtp->getServerExtList();
			}
			// If server supports authentication, do it (even if no encryption)
			if (is_array($e) && array_key_exists('AUTH', $e)) {
				if ($attempt == 2) {
					if (!$smtp->authenticate(MAIL_USER2, MAIL_PASS2)) {
						return false;
					}
				} else {
					if (!$smtp->authenticate(MAIL_USER, MAIL_PASS)) {
						return false;
					}
				}
				
			}
		} catch (\Exception $e) {
			$e->getMessages();
			return false;
		}
		// Whatever happened, close the connection.
		$smtp->quit(true);
		return true;
	}
	/*
	 * setBody Method
	 *
	 * @access private
	 * @description Sets the body whether it's nonhtml or html
	 *
	 * @see self::__construct();
	 *
	 * @return void
	 *   Sets the body of the E-mail we are sending (HTML or Non-HTML)
	 */
	private function setBody($arguments) {
		// Setting the html_body
		if (isset($arguments->html_body) && trim($arguments->html_body) > '') {
			$this->html_body = $arguments->html_body;
			// Validating the extension (html or phtml), or treat it as an HTML string
			$ext = pathinfo($this->html_body, PATHINFO_EXTENSION);
			$isHTML = false;
			$this->fileContents = $this->html_body;
			// Check extension 
			// File contents are retrieved here only
			// msgHTML() for PHPMailer is populated in sendMail() method
			if(in_array($ext, $this->allowed)) {
				$this->fileContents = file_get_contents($this->html_body);
			}
		} else {
			// Replace the plain text body with one created manually
			if (isset($arguments->nonhtml_body) && trim($arguments->nonhtml_body) > '') { // set nonhtml_body if html_body doesn't exist
				$this->nonhtml_body = $arguments->nonhtml_body;
			} else if (isset($this->nonhtml_body) && trim($this->nonhtml_body) > '') {
				$this->AltBody = $this->nonhtml_body;
				$this->fileContents = $this->nonhtml_body;
			} else { // nullify everything
				$this->AltBody = NULL;
				$this->fileContents = NULL;
			}
		}
		// Check fields submitted via POST
		if (isset($this->allFields) && $this->_validateFormFields() === true) {
			// Split by comma delimitation
			$fields = explode(",", $this->allFields);
			// Initialize message
			$message = '';
			// Loop through all fields
			foreach ($fields as $values) {
				$tmp = explode("|", $values);
				// check if the value is an array
				if (isset($_POST[$tmp[0]]) && is_array($_POST[$tmp[0]])) {
					$message .= PHP_EOL . "<div style=\"font-family:Arial, Helvetica, sans-serif;font-size:12px;\"><strong>" . $tmp[1] . ":</strong><br>";
					foreach ($_POST[$tmp[0]] as $c) {
						$message .= $c . "<br>";
					}
					$message .= "</div>";
				} elseif (isset($_POST[$tmp[0]]))
					$message .= PHP_EOL . "<div style=\"font-family:Arial, Helvetica, sans-serif;font-size:12px;\"><strong>" . $tmp[1] . ": </strong><br>" . $_POST[$tmp[0]] . "</div>";
			}
			// Check message and send to PHPMailer
			if (isset($message) && trim($message) > '') {
				$this->message = nl2br(stripslashes($message));
			}
		}
		// Read an HTML message body from an external file 
		// file has <!--PRE-CONTENT--> and <!--POST-CONTENT--> 
		// for replacement strings...in this case we replace 
		// POST-CONTENT with the form's submitted fields/values
		if ((isset($this->html_body) && trim($this->html_body) > '') && $this->isHtml === true) {
			// Check for replacement strings and if they don't exist, create them
			if (strpos($this->fileContents, '<!--POST-CONTENT-->') === false) {
				$this->fileContents = $this->fileContents . '<!--POST-CONTENT-->';
			}
			// Check for replacement strings and if they don't exist, create them
			if (strpos($this->fileContents, '<!--PRE-CONTENT-->') === false) {
				$this->fileContents = $this->fileContents . '<!--PRE-CONTENT-->';
			}
			// Check if we should send all form data with e-mail
			if ($this->sendFormData === true) { // send form data
				$this->msgHTML(str_replace('<!--POST-CONTENT-->', $this->message, $this->fileContents), dirname(__FILE__));
			} else { // do not send form data
				$this->msgHTML($this->fileContents, dirname(__FILE__));
			}
		} else {
			// Set to non-html email
			// No form data is ever sent in plain-text
			$this->IsHTML(false);
			$this->Body = $this->nonhtml_body;
		}
	}
	/*
	 * outPut method
	 *
	 * @access private
	 * @description Sends mail and outputs information to the screen
	 *
	 * @see self::__construct();
	 *
	 * @return mixed
	    Returns either a string, redirect, form errors, debug info or technical error string.
	 */
	private function outPut() {
		if ($this->sendMail()) {
			// Check return type requested
			if (isset($this->returnType) && $this->returnType == 'redirect' && isset($this->redirectPage) && $this->redirectPage !== false && trim($this->redirectPage) != '' && $this->_is_filepath($this->redirectPage)) {
				header('Location: ' . $this->redirectPage);
				exit; // stop processing after redirect
			} else if (isset($this->returnType) && $this->returnType == 'message') {
				// return message (user will need to echo this)
				$this->errorType = 'message';
				// calls the __toStrng() function 
				// which turns the object set 
				// into a string to echo out
				// messages, errors and warnings
				$this->__toString();
			} else if (isset($this->returnType) && $this->returnType == 'form_error') {
				// return message (user will need to echo this)
				$this->errorType = 'form_error';
				// calls the __toStrng() function 
				// which turns the object set 
				// into a string to echo out
				// messages, errors and warnings
				$this->__toString();
			} else if (isset($this->returnType) && $this->returnType == 'debug') {
				// Debug data
				$html = '<pre>';
				$html .= var_dump($this);
				$html .= '</pre>';
				return $html;
			} else {
				// Invoke the tech error method (see method for details)
				$this->_techError();
			}
		} else {
			// Invoke the send failure method (see method for details)
			$this->_sendFailure();
		}
	}
	/*
	 * sendMail Method
	 *
	 * @access private
	 * @description Sends the email using PHPMailers send() method
	 *
	 * @see self::outPut();
	 *
	 * @return boolean
	 *   Returns true if mail sent, false otherwise
	 */
	private function sendMail() {
		// Check for success from above
		if ($this->validation_success) {
			// See if message can be sent
			if (!$this->send()) { // failed
				$message = 'Mail failed to send on host {host} and port {port}' . PHP_EOL . 'Logged in with the username {user} and password {pass}';
				$data = array(
				   '{host}'    => $this->Host,
				   '{port}' => $this->Port,
				   '{user}'    => $this->Username,
				   '{pass}' => $this->Password
				);
				self::_logActions($this->logActions, 'info', $message, $data, 'email', $this->logType);
				return false;
			} else {
				if (isset($this->toaddress) && is_array($this->toaddress)) {
					foreach ($this->toaddress as $k =>$v) {
						$name = 'No Name';
						$email = '<noemail@none.com>';
						$val = $k;
						if (isset($v)) {
							$name = $v;
						}
						if (isset($val)) {
							$email = $val;
						}
						$message = 'Mail successfully sent to {name} ({email}) ' . date('F d, Y @ h:ia') . PHP_EOL;
						$data = array(
						   '{email}' => $email,
						   '{name}' => $name
						);
						self::_logActions($this->logActions, 'error', $message, $data, 'email', $this->logType);
					}
				}
				if (isset($this->from) && is_array($this->from)) {
					foreach ($this->from as $k => $v) {
						$name = 'No Name';
						$email = '<noemail@none.com>';
						$val = $k;
						if (isset($val)) {
							$name = $v;
						}
						if (isset($val)) {
							$email = $val;
						}
						$message = 'Mail successfully sent from {name} ({email}) ' . date('F d, Y @ h:ia') . PHP_EOL;
						$data = array(
						   '{email}' => $email,
						   '{name}' => $name
						);
						self::_logActions($this->logActions, 'error', $message, $data, 'email', $this->logType);
					}
				}
			}
			// message succeeded
			return true;
		}
		// Usually we return false unless
		// We have a form error, then we 
		// return true to keep processing going
		// in the output method, which captures
		// the errorType and processes it correctly
		if ($this->errorType == 'form_error') {
			// Form error, return true so we can 
			// proceed to show user the errors
			// in the output method
			return true;
		}
		// Default return
		return false;
	}
	/*
	 * Email received, reply will be sent method
	 *
	 * @access private
	 * @description Sends mail to the user  confirming their submission
	 *
	 * @see self::__construct();
	 *
	 * @return boolean
	 *  Sends a automatic reply e-mail after the first e-mail has been sent
	 */
	private function emailRecievedReply() {
		// Send automatic reply
		$objectOfInfo = new \stdClass(); // initialize
		// To and From Address (switched)
		$objectOfInfo->from 			= $this->toaddress;
		$objectOfInfo->to				= $this->from;
		// Check if the user submitted a reply_subject
		if ($this->reply_subject !== false) {
			$objectOfInfo->subject 		= $this->reply_subject;
		} else {
			$objectOfInfo->subject 		= $this->subject;
		}
		$objectOfInfo->isHtml 			= false; // auto replies are non-html
		$objectOfInfo->sendFormData 	= false; // no form data is sent with auto replies
		// Check for reply message
		if (isset($this->reply_message) && $this->reply_message !== false && trim($this->reply_message) > '') {
			$objectOfInfo->nonhtml_body = $this->reply_message; // custom message
		} else {
			$objectOfInfo->nonhtml_body = $this->EMAIL_RECIEVED_REPLY; // stock message
		}
		// Set remaining mailer settings
		$this->replySent = true; // must be set to avoid infinite loops
		$mail = $this->__construct($objectOfInfo); // send e-mail
		// Return message (if any exist)
		return $mail;
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
	/*
	 * _techError() method
	 *
	 * @access private
	 * @description preparation method for the method __toString()
	 *
	 * @see self::__toString();
	 *
	 * @return string
	 *   Returns a technical error string
	 */
	protected function _techError() {
		$message = 'There was a technical error encountered likely due to a configuration issue.' . PHP_EOL;
		$data = array();
		self::_logActions($this->logActions, 'info', $message, $data, 'misc', $this->logType);
		$this->errorType = 'tech'; // error type encountered
		// calls the __toStrng() function 
		// which turns the object set 
		// into a string to echo out
		// messages, errors and warnings
		$this->__toString(); 
	}
	/*
	 * _attachmentError() method
	 *
	 * @access protected
	 * @description preparation method for the method __toString()
	 *
	 * @return string
	 *   Returns errors related to attachments
	 */
	protected function _attachmentError() {
		$message = 'There was an unknown error attaching a file to the e-mail. Make sure the folder where files are uploaded has the proper permissions.' . PHP_EOL;
		$data = array();
		self::_logActions($this->logActions, 'info', $message, $data, 'misc', $this->logType);
		$this->errorType = 'attachment'; // error type encountered
		// calls the __toStrng() function 
		// which turns the object set 
		// into a string to echo out
		// messages, errors and warnings
		$this->__toString(); 
	}
	/*
	 * _attachmentError() method
	 *
	 * @access protected
	 * @description preparation method for the method __toString()
	 *
	 * @see self::__toString();
	 *
	 * @return string
	 *   Returns form field errors if any were encountered
	 */
	protected function _formFieldsError() {
		$this->errorType = 'form_error'; // error type encountered
		// calls the __toStrng() function 
		// which turns the object set 
		// into a string to echo out
		// messages, errors and warnings
		$this->__toString(); 
	}
	/*
	 * _sendFailure() method
	 *
	 * @access protected
	 * @description preparation method for the method __toString()
	 *
	 * @see self::__toString();
	 *
	 * @return string
	 *   Returns a sendmail failure notice, if mail did not send
	 */
	protected function _sendFailure() {
		$this->errorType = 'send_failure'; // error type encountered
		// calls the __toStrng() function 
		// which turns the object set 
		// into a string to echo out
		// messages, errors and warnings
		$this->__toString();
	}
	/*
	 * _is_filepath Method 
	 *
	 * @access protected
	 * @description Validate a path either with a domain or without
	 *
	 * @see self::output();
	 *
	 * @param {string} $path
	 *   A file path to validate
	 *
	 * @return boolean
	 *   Returns true if path is valid, false otherwise
	 */
	protected function _is_filepath($path) {
		$path = trim($path);
		$path = str_replace("\\", '/', $path);
		if(preg_match('/^[^*?"<>|:]*$/',$path)) {
			return true; // good to go
		}
		if(!defined('WINDOWS_SERVER')) {
			$tmp = dirname(__FILE__);
			if (strpos($tmp, '/', 0)!==false) {
				define('WINDOWS_SERVER', false);
			} else {
				define('WINDOWS_SERVER', true);
			}
		}
		/* First, we need to check if the system is windows */
		if(WINDOWS_SERVER) {
			if(strpos($path, ":") == 1 && preg_match('/[a-zA-Z]/', $path[0])) { // Check if it's something like C:\ {
				$tmp = substr($path,2);
				$bool = preg_match('/^[^*?"<>|:]*$/',$tmp);
				return ($bool == 1); // so that it will return only true and false
			}
			return false;
		}
		return false;
	}
	/*
	 * _validateArgs Method
	 *
	 * @access private
	 * @description Ensures we get only one argument (object or array)
	 *
	 * @see self::__construct();
	 *
	 * @return boolean
	 *   Validates arguments submitted, we want a single object or array, anything more invokes errors
	 */
	private function _validateArgs() {
		// should not exceed 1 parameter (an array or an object)
		if (isset($this->numberOfArgs) && is_int($this->numberOfArgs) && $this->numberOfArgs == 1) { // returns one argument
			return true;
		} else { // too many arguments found
			$message = 'Bad argument count, e-mail has not been sent.' . PHP_EOL;
			$data = array();
			self::_logActions($this->logActions, 'error', $message, $data, 'misc', $this->logType);
			return false;
		}
	}
	/*
	 * Initialize Email Settings Validator
	 *
	 * @description This validates most settings related information for the PHPMailer
	 *
	 * @access private
	 * @param {string} $arguments
	 *   Object of configuration options sent by the implementer
	 * 
	 * @return void
	 *   Sets up the PHPMailer Class Variables
	 */
	private function _validateEmailSettings($arguments) {
		// Debug
		if (isset($arguments->debug) && $arguments->debug) {
			$this->debug = true;
		}
		// Create a new PHPMailer instance
		//$this->mailer = new PHPMailer();
		$this->XMailer = 'Health Canada PHP Mailer Version 5';
		$this->CharSet = 'UTF-8'; // Needed for French
		$this->Encoding = "base64"; // Absolutely needed for French
		// Clearing all information (just in case)
		$this->clearAllRecipients();
		$this->ClearReplyTos();
		$this->ClearAttachments();
		$this->ClearCustomHeaders();
		if (isset($arguments->language) && trim($arguments->language) > '') {
			$this->language = $arguments->language;
		}
		// Check the language if one is being set and only use 'fr' or 'en'
		if (isset($this->language) && !is_array($this->language) && trim($this->language) > '' && $this->language != NULL && ($this->language == 'en' || $this->language == 'fr')) {
			// Set language in PHP Mailer (errors only)
			$this->setLanguage($this->language);
			// Set up the error messages to be french
			if ($this->language == 'fr') {
				// include our french language file and suppress potential warnings/errors
				@include 'language/mail-main.lang-fr.php';
				// Recasting language variables
				$this->MAIL_UNKNOWN_ERROR = $HC_MAIL_WRAPPER['MAIL_UNKNOWN_ERROR'];
				$this->MAIL_TECH_ERROR = $HC_MAIL_WRAPPER['MAIL_TECH_ERROR'];
				$this->MAIL_ERROR = $HC_MAIL_WRAPPER['MAIL_ERROR'];
				$this->MAIL_ERROR_PREFIX = $HC_MAIL_WRAPPER['MAIL_ERROR_PREFIX'];
				$this->MAIL_SENT = $HC_MAIL_WRAPPER['MAIL_SENT'];
				$this->FORM_ERROR_MESSAGE = $HC_MAIL_WRAPPER['FORM_ERROR_MESSAGE'];
				$this->EMAIL_RECIEVED_REPLY = $HC_MAIL_WRAPPER['EMAIL_RECIEVED_REPLY'];
				$this->ATTACHMENT_ERROR = $HC_MAIL_WRAPPER['ATTACHMENT_ERROR'];
				$this->CONNECTION_TEST = $HC_MAIL_WRAPPER['CONNECTION_TEST'];
				if (trim($this->customReturnMessage) == 'Thanks for contacting us!') {
					$this->customReturnMessage = $HC_MAIL_WRAPPER['customReturnMessage'];
				}
			}
		}
		// Check if host was supplied
		$this->Host = MAIL_HOST;
		if (isset($arguments->host) && trim($arguments->host) > '') {
			$this->Host = $arguments->host;
		}
		// Check if we need to be IPv4 compatible only
		if (isset($arguments->ipv6compat) && trim($arguments->ipv6compat) > '') {
			$this->IPV6Compat = $arguments->ipv6compat;
		}
		if (isset($this->IPV6Compat) && $this->IPV6Compat) {
			// Gets IPv4 address of host
			$this->Host = gethostbyname($this->Host); 
		}
		// Set port
		$this->Port = MAIL_PORT;
		if (isset($arguments->port) && trim($arguments->port) > '' && is_int($arguments->port) && $arguments->port != -1) {
			$this->Port = $arguments->port;
		}
		// Encryption system to use
		$this->SMTPSecure = false;
		if (isset($arguments->encryption) && is_bool($arguments->encryption) && $arguments->encryption) {
			$this->SMTPSecure = true;
		}
		// Whether to use SMTP authentication
		$this->SMTPAuth = false;
		if (isset($arguments->authorization) && is_bool($arguments->authorization) && $arguments->authorization) {
			$this->MailAuth = true;
		}
		$this->SMTPAuth = $this->MailAuth;
		// Set who the message is to be sent from
		if (isset($arguments->from)) {
			$this->from = $arguments->from;
		}
		// Check the from and see if it's an array
		if (is_array($this->from)) { // toaddress is an array
			$i = 1;
			// Looping through the toaddresses and names
			foreach ($this->from as $email => $name) {
				// Check if name exists in the array
				if (isset($name) && trim($name) > '') {
					if ($i == 1) {
						$this->MessageID = '<' . md5(uniqid(rand(), true)) . '@canada.ca>';
					}
					$this->setFrom($email, $name);
				} else {
					$this->setFrom($email);
				}
				$i++; // increment
				if ($i == 2) { // check if we have 1 address (limit 1 for the from address)
					break; // break loop
				}
			}
		} else if (isset($this->from) && trim($this->from) > '') { // toaddress is a string
			$email = explode('@', $this->from); // split email at the @ symbol
			$nameParts = explode('.', $email[0]); // split the name part of the email by the dot
			if (isset($nameParts[0], $nameParts[1])) { // check if name parts exist (should always be true
				$fullName = ucfirst($nameParts[0]) . ' ' . ucfirst($nameParts[1]); // Setting the full name
				$this->setFrom($this->from, $fullName);
				$this->MessageID = '<' . md5(uniqid(rand(), true)) . '@canada.ca>';
			}
		}
		// Set who the message is to be sent from
		if (isset($arguments->replyto)) {
			$this->replyto = $arguments->replyto;
		}
		// Check the from and see if it's an array
		if (is_array($this->replyto)) { // toaddress is an array
			$i = 1;
			// Looping through the toaddresses and names
			foreach ($this->replyto as $email => $name) {
				// Check if name is in the array
				if (isset($name) && trim($name) > '') {
					$this->addReplyTo($email, $name);
				} else {
					$this->addReplyTo($email);
				}
				$i++; // increment
				if ($i == 2) { // check if we have 1 address (limit 1 for the from address)
					break; // break loop
				}
			}
		} else if (isset($this->replyto) && trim($this->replyto) > '') { // toaddress is a string
			$email = explode('@', $this->replyto); // split email at the @ symbol
			$nameParts = explode('.', $email[0]); // split the name part of the email by the dot
			if (isset($nameParts[0], $nameParts[1])) { // check if name parts exist (should always be true
				$fullName = ucfirst($nameParts[0]) . ' ' . ucfirst($nameParts[1]); // Setting the full name
				$this->setFrom($this->from, $fullName);
			}
		}
		// Set who the message is to be sent to
		if (isset($arguments->to)) {
			$this->toaddress = $arguments->to;
		}
		// Check the toaddress and see if it's an array
		if (is_array($this->toaddress)) { // toaddress is an array
			$i = 1; // set incrementer
			// Looping through the toaddresses and names
			foreach ($this->toaddress as $email => $name) {
				if ($this->_allowEmailTo($email))  {
					// Check if name is in the array
					if (isset($name) && trim($name) > '') {
						$this->addAddress($email, $name); // adding to email to phpmailer
					} else {
						$this->addAddress($email); // adding to email to phpmailer
					}
					$i++; // increment
					if ($i == 10) { // check if we have 10 addresses
						break; // break loop
					}
				}
			}
		} else if (isset($this->toaddress) && trim($this->toaddress) > '') { // toaddress is a string
			$email = explode('@', $this->toaddress); // split email at the @ symbol
			$nameParts = explode('.', $email[0]); // split the name part of the email by the dot
			if (isset($nameParts[0], $nameParts[1])) { // check if name parts exist (should always be true)
				$fullName = ucfirst($nameParts[0]) . ' ' . ucfirst($nameParts[1]); // Setting the full name from email part
				$this->addAddress($this->toaddress, $fullName); // add to address to phpmailer
			}
		}
		// Check argument for the subject line
		if (isset($arguments->subject) && trim($arguments->subject) > '') {
			$this->subject = $arguments->subject;
		}
		// Set the subject line
		$this->Subject = $this->subject;
		// Checking for uploaded files
		if (isset($_FILES)) {
			// Loop through uploaded files
			foreach ($_FILES as $usefile) {
				$filetype = $usefile["type"];
				$filesize = $usefile["size"];
				$filename = $usefile["name"];
				$ext = pathinfo($filename, PATHINFO_EXTENSION);
				$filetemp = $usefile["tmp_name"];
				// If it does not exceed file size
				// Attach file
				if ($this->_fileSize($filesize) && in_array($ext, $this->allowed_extensions)) {
					// Moving uploaded file to the upload directory
					if (move_uploaded_file($filetemp, "$this->uploads_dir/$filename")) {
						$this->fileError = false;
						$this->filesUploaded[] = $this->uploads_dir . '/' . $filename;
						$this->addAttachment("$this->uploads_dir/$filename"); // adding attachment to e-mail
					}
				} else {
					// since we have a return here
					// we need to validate form fields
					// since we caught an attachment error
					// validation would not proceed on fields
					// if this is not invoked here.
					$this->_validateFormFields(); 
					$this->_attachmentError();
					return false;
				}
			}
		}		
		// Attach an image file
		if (isset($arguments->attachment) && !is_array($arguments->attachment) && trim($arguments->attachment) > '') {
			$this->attachment = $arguments->attachment;
			$this->addAttachment($this->attachment);
		} else if (isset($arguments->attachment) && is_array($arguments->attachment) && !empty($arguments->attachment)) {
			foreach ($arguments->attachment as $attachment) {
				$this->addAttachment($attachment);
			}
		}
		// Type of mail method choosen by the implementor
		if (isset($arguments->mail_method) && trim($arguments->mail_method) > '') {
			$this->mailMethod = $arguments->mail_method;
		}
		// Username to use for SMTP authentication - use full email address for gmail
		if (isset($arguments->username) && trim($arguments->username) > '') {
			$this->Username = $arguments->username;
		} else {
			$this->Username = MAIL_USER;
		}
		// Password to use for SMTP authentication
		if (isset($arguments->password) && trim($arguments->password) > '') {
			$this->Password = $arguments->password;
		} else {
			$this->Password = MAIL_PASS;
		}
		// Test/Check/Get connection
		if ($this->MailAuth && isset($this->mailMethod) && trim($this->mailMethod) == 'smtp') {
			$connOne = false;
			$connTwo = false;
			if (!file_exists('./tmp/conn1failure.log')) {
				$connOne = $this->connectionDetermination($arguments, 1);
			}
			if ($connOne) { // first connection text
				$this->mailerUseType();
				return true;
			} else {
				$logFile = fopen('./tmp/conn1failure.log', 'w');
				@fwrite($logFile, 'Connection 1 is not available. Delete this file when the credentials have changed and authorization is granted.');
				if (!file_exists('./tmp/conn2failure.log')) {
					$connTwo = $this->connectionDetermination($arguments, 2);
					if ($connTwo) { // second/final connection test
						$this->mailerUseType();
						return true;
					} else {
						$logFile = fopen('./tmp/conn2failure.log', 'w');
						@fwrite($logFile, 'Connection 2 is not available. Delete this file when the credentials have changed and authorization is granted.');
						$this->validation_success = false;
						return false;
					}
				}
				$this->validation_success = false;
				return false;
			}
			$this->validation_success = false;
			return false;
		} else {
			return true;
		}
		// Return true if nothing was wrong
		return false;
	}
	/*
	 * Initilize the type of mail
	 * service we are using 
	 * (usually SMTP)
	 *
	 * @access private
	 * @description This validates form fields that were set in the configuration
	 *
	 * @see self::_validateEmailSettings();
	 *
	 * @return boolean
	 *   Finds the type of mail service we will use, usually SMTP
	 */
	private function mailerUseType() {
		// Tell PHPMailer which function to use for sending -email
		if ($this->mailMethod == 'mail') { // mail method
			// Mail
			$this->isMail();
		} else if ($this->mailMethod == 'sendmail') { // sendmail method
			// Sendmail Mailer
			$this->isSendmail();
		} else { // SMTP (default)
			// SMTP Mailer
			$this->SMTPKeepAlive = true;
			$this->isSMTP();
			// Set debugging
			$this->SMTPDebug = 0;
			// Is debugging on or off?
			if ($this->debug) {
				$message = "debug level {level};" . PHP_EOL . "message: {message}" . PHP_EOL;
				$logActions = $this->logActions;
				$logType = $this->logType;
				// Enable SMTP debugging
				$this->SMTPDebug = $this->SMTPDebugLevel;
				// Ask for HTML-friendly debug output
				$this->Debugoutput = function($str, $level) {
					global $message, $logActions, $logType;
					$data = array(
						'{level}' => $level,
						'{message}' => $str
					);
					HCMailWrapper::_logActions($logActions, 'info', $message, $data, 'connection', $logType);
					echo nl2br($message);
				};
				
			}
		}
	}
	/*
	 * Initialize Form Fields Validator
	 *
	 * @access private
	 * @description This validates form fields that were set in the configuration
	 *
	 * @see self::__toString();
	 *
	 * @return boolean
	 *   Form field validation if required fields were set
	 */
	private function _validateFormFields() {
		// Look for empty fields
		$tmpReq = array();
		$sendErrorReport = '';
		// Checking which fields are required (if any are required)
		if (isset($this->required_fields)) {
			$requiredFields = explode(",", $this->required_fields);
			// Begin unordered list for errors
			$sendErrorReport .= $this->openList;
			// Looping through required fields
			$this->validation_success = true;
			foreach ($requiredFields as $value) {
				$tmpReq = explode("|", $value); // split out field entry
				if (empty($_POST[$tmpReq[0]])) { // make sure the post exists prior to validating
					$this->returnType = $this->errorType = 'form_error'; 
					if (isset($tmpReq[1]) && trim($tmpReq[1]) > '') {
						$sendErrorReport .= $this->prefixListItem . $tmpReq[1] . $this->postListItem;
					}
					$this->validation_success = false;
				}
			}
			// Ending our unordered list
			$sendErrorReport .= $this->closeList;
			// Nullify re-used variable
			$this->sendErrorReport = NULL;
			// Check if anything wasn't valid
			if (!$this->validation_success) {
				$this->sendErrorReport = $sendErrorReport; // set up error report string
				return false; // return false, stop processing show errors
			}
		}
		// Return true...all is well
		return true;
	}
	/*
	 * Filesize method
	 *
	 * @access private
	 * @description Checks filesize
	 *
	 * @see self::_validateEmailSettings() and self::setBody()
	 *
	 * @return boolean
	 *   Checks file size and file size limit
	 */
	private function _fileSize($filesize) {
		// Check if we have a valid file size entry, if not default
		if (!isset($this->maxFileSize) || trim($this->maxFileSize) == '' || !is_int($this->maxFileSize)) {
			$this->maxFileSize = 2000000;
		}
		// Check file size
		if ($filesize > $this->maxFileSize) {
			return false;
		} else {
			return true;
		}
	}
	/*
	 * allowEmailTo method
	 *
	 * @access private
	 * @description Checks list of e-mail domains and returns true if allowed
	 *
	 * @see self::_validateEmailSettings()
	 *
	 * @return boolean
	 *   Checking the to e-mail and ensuring it's allowed based on our whitelist
	 */
	private function _allowEmailTo($email) {
		foreach ($this->refererEmail as $val) {
			if (strrpos($email, $val) !== false) {
				return true;
			}
		}
		$message = 'The e-mail you are sending to is not a valid domain.' . PHP_EOL;
		$data = array();
		self::_logActions($this->logActions, 'info', $message, $data, 'misc', $this->logType);
		return false;
	}
	/*
	 * Referrer
	 *
	 * @access private
	 * @description Checks referrer
	 *
	 * @see self::__construct()
	 *
	 * @return boolean
	 *   Checks the referer domain and ensures it's valid before sending e-mail
	 */
	private function _refererDomain() {
		$serverName = $_SERVER['SERVER_NAME'];
		foreach ($this->refererSite as $val) {
			if ($serverName == $val) {
				return true;
				break;
			}
		}
		$message = 'Bad referrer, e-mail has not been sent.' . PHP_EOL;
		$data = array();
		self::_logActions($this->logActions, 'info', $message, $data, 'misc', $this->logType);
		return false;
	}
	/*
	 * __toString() magic method
	 *
	 * @access public
	 * @description Used to turn the mailer object into a string upon error
	 *
	 * @see self::outPut(), self::_techError(), self::_attachmentError(), self::_formFieldsError(), self::_sendFailure()
	 *
	 * @return string
	 *   Returns a string, since we normally return an object this is required for error strings if an error is encountered
	 */
	public function __toString() {
		// Checking the errorType given
		if (isset($this->errorType) && $this->errorType != NULL && $this->errorType == 'tech') {
			// Return
			return $this->MAIL_TECH_ERROR;
		} else if (isset($this->errorType) && $this->errorType != NULL && $this->errorType == 'send_failure') {
			$theReturn = $this->MAIL_ERROR;
			// Debug info (needs work)
			if ($this->debug) {
				$theReturn = $this->_techError();
			}
			// Return
			return $theReturn;
		} else if (isset($this->errorType) && $this->errorType != NULL && ($this->errorType == 'form_error' || $this->errorType == 'attachment')) {
			// Start extensions var
			$extensions = '';
			// Check array and print extension list if an error was encountered
			// To help the user understand what they must submit
			if (isset($this->allowed_extensions) && !empty($this->allowed_extensions) && is_array($this->allowed_extensions)) {
				$extensions .= $this->openList;
				foreach ($this->allowed_extensions as $ext) {
					$extensions .= $this->prefixListItem . strtoupper($ext) . $this->postListItem;
				}
				$extensions .= $this->closeList;
			}
			// File error encountered in attachment
			if ($this->fileError) {
				// Return
				return $this->FORM_ERROR_MESSAGE . $this->sendErrorReport . '<br>' . $this->ATTACHMENT_ERROR . $extensions;
			}
			// Return
			return $this->FORM_ERROR_MESSAGE . $this->sendErrorReport;
		} else if (isset($this->errorType) && $this->errorType != NULL && $this->errorType == 'message') {
			// Return
			return $this->customReturnMessage;
		} else if (isset($this->errorType) && $this->errorType != NULL && $this->errorType > '') {
			// Return
			return $this->MAIL_UNKNOWN_ERROR;
		} else {
			// Return
			return $this->MAIL_SENT;
		}
	}
	/*
	 * Destructor function
	 *
	 * @access public
	 *
	 * @see SMTP::smtpClose(), self::__construct() and self::_validateEmailSettings()
	 *
	 * @return mixed
	 *   Closes the SMTP if it's in use, nullifies sensitive parameters and shows mailer version if debugging is on
	 */
	public function __destruct() {
		// Call PHPMailers Destruct
		parent::__destruct();
		// Nullifying sensitive variables
		$this->numberOfArgs = NULL; // nullifying argument counter
		$this->getArgs = NULL; // nulifying all submitted arguments
		$this->mailer = NULL; // nullifying the mailer object
		$this->host = NULL; // nullifying the host
		$this->port = NULL; // nullifying the port
		$this->username = NULL; // nullifying the username
		$this->password = NULL; // nullifying the password
		// Removing the attachments sent from the form
		if (isset($this->filesUploaded) && is_array($this->filesUploaded)) {
			foreach ($this->filesUploaded as $file) {
				unlink($file);
			}
		}
		// Show version info if debug is on
		if ($this->debug) {
			echo '<p>Mailer Information: ' . $this->versionInfo . '</p>';
		}
	}
}