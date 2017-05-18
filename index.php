<?php
// Require Autoloader
require_once 'PHPMailerAutoload.php';
/**
 * Code Information:
 * This code is used as a wrapper for the PHPMailer Class 
 * to simplify sending emails and ensure it's as secure as 
 * possible.
 * The following fields are required:
 * 1. from
 * 2. to
 * 3. subject
 * 4. nonhtml_body (only if html_body is NOT filled in).
 *
 * The rest of the options are optional/defaulted (all options shown below). 
 * These application defaults are:
 *
 * host: smtps.ctst.email-courriel.canada.ca
 * port: 587
 * username: See class.HCMailWrapper.php (if accessible)
 * password: See class.HCMailWrapper.php (if accessible)
 * from:
 * to: 
 * replyto: NULL
 * send_reply_message: true
 * reply_subject: NULL
 * subject: 
 * html_body: NULL
 * nonhtml_body: 
 * attachment: NULL
 * mail_method: smtp
 * encryption: tls
 * authorization: true
 * ipv6compat: false
 * timezone: America/Toronto
 * language: en
 * openList: <ul>
 * closeList: </ul>
 * prefixListItem: <li>
 * postListItem: </li>
 * reply_message: (see mail-main.lang-fr.php)
 * redirect: NULL
 * max_file_size: 2000000
 * all_fields: NULL
 * required_fields: NULL
 * isHtml: true
 * refererEmail: array('canada.ca', 'hc-sc.gc.ca', 'list.hc-sc.gc.ca', 'chemicalsubstanceschimiques.gc.ca');
 * refererSite: array('canada.ca', 'web.hc-sc.gc.ca', 'www.hc-sc.gc.ca', 'hc-sc.gc.ca','www.sc-hc.gc.ca/','sc-hc.gc.ca/', '205.193.190.11');
 * debug: false
 * testConnection: false
 * SMTPDebugLevel: 2
 * logActions: false
 * logType: file
 *
 */

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
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<!-- InstanceBegin template="/Templates/2col-eng.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<!-- CLF 2.0 TEMPLATE VERSION 1.0 | VERSION 1.0 DU GABARIT NSI 2.0 -->

<link rel="schema.dc" href="http://purl.org/dc/elements/1.1/" />
<link rel="schema.dcterms" href="http://purl.org/dc/terms/" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="dc.language" scheme="ISO639-2/T" content="eng" />
<!-- METADATA BEGINS | DEBUT DES METADONNEES -->
<!-- InstanceBeginEditable name="meta" -->

<meta name="robots" content="noindex,nofollow" />
<title>Publication Request - Health Canada</title>
<meta name="dc.title" content="Publication Request - Health Canada" />
<meta name="dc.creator" content="Government of Canada, Health Canada, Public Affairs Consultation and Communications Branch" />
<meta name="dc.type" scheme="gctype" content="form" />
<meta name="dcterms.issued" scheme="W3CDTF" content="2012-03-09" />
<meta name="dcterms.modified" scheme="W3CDTF" content="2015-10-30" />
<meta name="review_date" content="2014-03-09" />
<meta name="meta_date" content="2012-04-16" />
<style>
.red {
	color:#FF0000;
}
</style>
<?php
// Add JavaScript error checker from the Mail Wrapper (optional)
$js = HCMailer2017\HCMailWrapper::_writeJSFunctions($objectOfInfo);
echo $js;
?>
</head>
<body>
<?php
// Check for the submitted input field
if (isset($_POST['submit'])) {
	// Make the call to mailer
	$mailer = new HCMailer2017\HCMailWrapper($arrayOfInfo); // invoking this mailer and adding the object/array info to it
	$message = $mailer->init(); // show us what string the mailer returns (if any, won't work on redirects).
	echo $message;
}
?>
        <h1>Request Publication</h1>
        <p>Please complete the form below to obtain a Portable Document Format (<abbr>PDF</abbr>) copy of the publication.</p>
        <p>If an accessible version is needed please specify the desired format in the "Message" field below.</p>
        <div class="toggle-content collapse">
          <h2 class="toggle-link-text">Privacy Statement</h2>
        </div>
        <p class="fontSize80">Mandatory fields marked <span class="red">*</span></p>
        <form method="post" action="" id="emailform" enctype="multipart/form-data" >
          <div>
            <label for="realname"><strong><span class="red">*</span> Name:</strong></label>
            <br />
            <input type="text" name="realname" id="realname" size="30" />
          </div>
          <div>
            <label for="email"><strong><span class="red">*</span> E-mail address</strong>:</label>
            <br />
            <input type="text" name="email" id="email" size="30" />
          </div>
          <div>
            <label for="message"><strong>Message (up to 50 lines)</strong></label>
            <br />
            <textarea cols="45" name="message" id="message" rows="10">
 </textarea>
          </div>
          <div>
			File: <input type="file" name="fileAttach" id="fileAttach">
		  </div>
		  <div>
            <input type="submit" value="Submit" name="submit" />
          </div>
        </form>
		
</body>
</html>