Author: Steven Scharf

Project: PHP Mailer Extension Wrapper for E-mail Tranformation Initiative (ETI).

State: Beta v1.10 RC (Release Candidate)

Contact: steven.scharf@canada.ca

Configuration File (classes/class.ConfigClass.php):

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