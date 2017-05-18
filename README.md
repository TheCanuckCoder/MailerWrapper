# PHP Mailer Extension Wrapper
## Author: Steven Scharf
### State: Beta v1.20 RC (Release Candidate)
#### Information
This is a self-contained script requiring no support or other plugins.
Included in this is the PHPMailer, the SMTP Class and PSR Logging Class.

Contact: [Steven Scharf](mailto:steven.scharf@canada.ca)
#### Debugging
There are debugging and logging options available.

[See logging for more information.](#logs)
#### Connectivity Testing
There is a file available indicating a connectivity issue if encountered
named 'conn1failure.log' and 'conn2failure.log'. They provide no real context
of the issue, it's there to let you know a connection problem exists
likely due to a credential issue.

Although a file indicates a connectivity problem it will 
try to connect to both connections regardless in an effort 
to check if the connection was down temporarily.
#### Logs
All other log information is in the 'logs' folder.
#### Configuration File
Location: ```/config/class.ConfigClass.php```

Copy this exactly as displayed below or edit the 'class.ConfigClass.php.dist' 
file and remove the '.dist' extension and change the values to relate to your 
connection credentials. More options are set when initializing the Class.
```php
<?php
namespace HCMailer2017;
class ConfigClass extends HCMailWrapper {
	public $MAIL_HOST = '';	
	public $MAIL_PORT = 587;	
	public $MAIL_USER = '';	
	public $MAIL_PASS = '';	
	public $MAIL_USER2 = '';	
	public $MAIL_PASS2 ='';	
}
```

#### More Configuration options
The following fields are required:
```
1. from:
2. to:
3. subject:
4. nonhtml_body: (only if html_body is NOT filled in).
```
The rest of the options are optional/defaulted (all options shown below). 

These application defaults are:
```
host: See class.HCMailWrapper.php (if accessible)
port: See class.HCMailWrapper.php (if accessible)
username: See class.HCMailWrapper.php (if accessible)
password: See class.HCMailWrapper.php (if accessible)
from: NULL
to: NULL
replyto: NULL
send_reply_message: true
reply_subject: NULL
subject: 
html_body: NULL
nonhtml_body: 
attachment: NULL
mail_method: smtp
encryption: tls
authorization: true
ipv6compat: false
timezone: America/Toronto
language: en
openList: <ul>
closeList: </ul>
prefixListItem: <li>
postListItem: </li>
reply_message: (see mail-main.lang-fr.php)
redirect: NULL
max_file_size: 2000000
all_fields: NULL
required_fields: NULL
isHtml: true
refererEmail: array('canada.ca', 'hc-sc.gc.ca', 'list.hc-sc.gc.ca', 'chemicalsubstanceschimiques.gc.ca');
refererSite: array('canada.ca', 'web.hc-sc.gc.ca', 'www.hc-sc.gc.ca', 'hc-sc.gc.ca','www.sc-hc.gc.ca/','sc-hc.gc.ca/', '205.193.190.11');
debug: false
testConnection: false
SMTPDebugLevel: 2
logActions: false
logType: file
```

#### Examples
You can see in the examples below, you can give the constructor an array or an object.

The 2 examples below are a large sample and small sample so you can see how a configuration can be implemented minimally or fully.
##### Example 1
```php
// Set array of Info (Larg Sample)			
$arrayOfInfo = array(
		'from' 				 => 									   	// From E-mail (Limit 1)
								array('steven.scharf@canada.ca' => 'Steven Scharf'), 
		'to' 														   	// To E-mail (associative array with key as email, 
																		// value as name - limit 10)
							 => array('steven.scharf@canada.ca' => 'S. Scharf'),			
		'replyto' 			 =>  								   		// Reply-To E-mail
								array('steven.scharf@canada.ca' => 'Steven Scharf'),
		'send_reply_message' => true,							   		// Send reply (confirmation) e-mail when they successfully send one
		'reply_subject'		 => 'Thanks for contacting us!',			// Subject of the automated reply when submission is successful 
		'subject' 			 => 'PHPMailer test', 						// Subject of your e-mail
		'html_body' 		 => 'templates/contents.html', 						// html body (can be .html file or html markup)
		'nonhtml_body' 		 => 'This is a plain-text message body', 	// plain text body
		//'attachment' 													// e-mail attachment (relative path), must reside on same domain
							 //=> 'images/existingticket.png', 			// string or indexed array
		'allowed_extensions' => array('gif', 'png', 'jpg'),									// Allowed extensions for upload
		'mail_method' 		 => 'smtp', 								// can be mail, sendmail or smtp (default)
		'encryption' 		 => 'tls', 									// use - ssl (deprecated) or tls (default)
		'authorization' 	 => true, 									// should be set to true (true|false)
		'ipv6compat' 		 => false, 									// should be set to false (true|false)
		'timezone'			 => 'America/Toronto',						// timezones: http://php.net/manual/en/timezones.php
		'language'			 => 'en',									// Language: en|fr
		'openList'			 => '<ol>',									// When errors appear, the opening wrapper for the error list
		'closeList'			 => '</ol>',								// When errors appear, the closing wrapper for the error list
		'prefixListItem'	 => '<li>',									// When errors appear, the opening wrapper for the list item
		'postListItem'		 => '</li>',								// When errors appear, the closing wrapper for the list item
		'reply_message'												   	// confirmation of their email being sent to the subject matter expert.
							 => "This is an auto-generated e-mail; please do not reply." . PHP_EOL . 
							    "Your message has been received by the Web site administrator and is being forwarded to a subject-matter expert for consideration and a timely response." . PHP_EOL . 
							    "Thank you for your interest in Health Canada Online.",
		//'redirect'		 => 'somedir/somepage.php',
		'max_file_size'		 => 2000000,
		'all_fields'													// All fields we should look for and post in e-mail
							 => 'realname|Name,email|Email,message|Message',
		'required_fields'	 => 'realname|Name,email|Email,fileAttach|File',			// Required fields for validation
		'isHtml'			 => true,									// E-mails sent can be HTML or Plain-text
		'refererSite' 		 => 										// 
								array('canada.ca', 'hc-sc.gc.ca', 'mailer.dev', 'sad-lap-pub01', '1115.dev'),
		'refererEmail' 		 => 										//
								array('canada.ca', 'hc-sc.gc.ca', 'mailer.dev', 'sad-lap-pub01', '1115.dev'),
		'debug'				 => false, 									// Turn debugging on/off, prod should be false (true|false)
		'testConnection'	 => false,									// Determine if you're just testing the connection (SMTP Only)
		'SMTPDebugLevel'	 => 0,										// 0 = No debug output, 1 = Client commands, 
																		// 2 = Client commands and server responses (default), 
																		// 3 = As DEBUG_SERVER plus connection status, 
																		// 4 = Low-level data output, all messages
		'logActions'		 => true,									// 
		'logType' 			 => 'file'									// 
		);
```
##### Example 2
```php
// Set object of info (Smallest Sample)				
$objectOfInfo = new stdClass(); 	// initialize object (required), all the rest is self explanatory
$objectOfInfo->from																// From E-mail (limit 1)
									= array('steven.scharf@canada.ca' => 'Steven Scharf');
$objectOfInfo->to 																// To E-mail (associative array with key as email, 
																				// value as name - limit 10)
									= array('steven.scharf@hc-sc.gc.ca' => 'Site Admin'); 			
$objectOfInfo->send_reply_message	= true;										// Send reply (confirmation) e-mail when they successfully send one
$objectOfInfo->subject 				= 'PHPMailer test'; 						// Subject of your e-mail
$objectOfInfo->html_body 														// html body (can be .html file or html markup)
									= '<p>Someone has requested information.</p>'; 							
$objectOfInfo->language				= 'en';										// Language: en|fr
$objectOfInfo->all_fields														// All fields we should look for and post in e-mail
									= 'realname|Name,email|Email,message|Message';
$objectOfInfo->required_fields		= 'realname|Name,email|Email';				// Required fields for validation
```