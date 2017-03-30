<?php
/**
 * PHPMailer SPL autoloader.
 * PHP Version 5
 * @package PHPMailer
 * @link https://github.com/PHPMailer/PHPMailer/ The PHPMailer GitHub project
 * @author Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @author Jim Jagielski (jimjag) <jimjag@gmail.com>
 * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 * @author Brent R. Matzelle (original founder)
 * @copyright 2012 - 2014 Marcus Bointon
 * @copyright 2010 - 2012 Jim Jagielski
 * @copyright 2004 - 2009 Andy Prevost
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * PHPMailer SPL autoloader.
 * @param string $classname The name of the class to load
 */
namespace HCMailer2017;
// Autoload function PSR-4
function PHPMailerAutoload($classname) {
	$file = explode('\\',$classname);
    // require all files in classes folder
    $filename = dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.'.$file[count($file) - 1].'.php';
    if (is_readable($filename)) {
        require $filename;
    }
	// Require all files in Psr\Log folder
	$filename = dirname(__FILE__).DIRECTORY_SEPARATOR.'Psr'.DIRECTORY_SEPARATOR.'Log'.DIRECTORY_SEPARATOR.$file[count($file) - 1].'.php';
    if (is_readable($filename)) {
        require $filename;
    }
}
// Check PHP Version, load accordingling
if (version_compare(PHP_VERSION, '5.1.2', '>=')) {
    //SPL autoloading was introduced in PHP 5.1.2
    if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
		// This is what will load in the new servers
        spl_autoload_register('HCMailer2017\PHPMailerAutoload', true, true); 
    } else {
		// Old server load method
        spl_autoload_register('HCMailer2017\PHPMailerAutoload');
    }
} else { // So old it doesn't matter
    /**
     * Fall back to traditional autoload for old PHP versions
     * @param string $classname The name of the class to load
     */
    function __autoload($classname) {
        HCMailer2017\PHPMailerAutoload($classname);
    }
}