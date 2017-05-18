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

Other Configuration options:
The following fields are required:
1. from
2. to
3. subject
4. nonhtml_body (only if html_body is NOT filled in).

The rest of the options are optional/defaulted (all options shown below). 
These application defaults are:
host: smtps.ctst.email-courriel.canada.ca
port: 587
username: See class.HCMailWrapper.php (if accessible)
password: See class.HCMailWrapper.php (if accessible)
from:
to: 
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