Author: Steven Scharf

Project: PHP Mailer Extension Wrapper

State: Beta v1.20 RC (Release Candidate)

Information: This is a self-contained script requiring no support or other plugins.
Included in this is the PHPMailer, the SMTP Class it comes with and PSR Logging.

Contact: steven.scharf@canada.ca

Debugging: There are debugging and logging options available.
There is a file available indicating a connectivity issue if encountered
named 'conn1failure.log' and 'conn2failure.log'. They provide no real context
of the issue, it's there to let you know a connection problem exists
likely due to a credential issue.
Although a file indicates a connectivity problem it will 
try to connect to both connections regardless in an effort 
to check if the connection was down temporarily.

Logs: All other log information is in the 'logs' folder.

Configuration File (config/class.ConfigClass.php):
Copy this exactly as displayed below or edit the 'class.ConfigClass.php.dist' 
file and remove the '.dist' extension and change the values to relate to your 
connection credentials. More options are set when initializing the Class.

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