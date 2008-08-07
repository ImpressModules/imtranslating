<?php
/**
* English language constants used in admin section of the module
*
* @copyright	The SmartFactory http://www.smartfactory.ca
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @package		Imtranslating
* @author		marcan <marcan@smartfactory.ca>
* @version		$Id: admin.php 1394 2008-05-22 16:21:43Z marcan $
*/

if (!defined("XOOPS_ROOT_PATH")) {
 	die("XOOPS root path not defined");
}


define("_AM_IMTRANSL_FROM_LANG", "Reference language");
define("_AM_IMTRANSL_TO_LANG", "Language of the translation");
define("_AM_IMTRANSL_MODULE", "Module");
define("_AM_IMTRANSL_COREFILES", "Core Files");
define("_AM_IMTRANSL_INSTALLFILES", "Installation Files");
define("_AM_IMTRANSL_GO", "Continue");
define("_AM_IMTRANSL_CANCEL", "Cancel");
define("_AM_IMTRANSL_ORIGINAL", "%s definition for %s");
define("_AM_IMTRANSL_JOB", "Missing constant in %s");
define("_AM_IMTRANSL_TRANSLATION", "Translation");
define("_AM_IMTRANSL_COMMENT", "//New constants created via IMtranslating");
define("_AM_IMTRANSL_ZIP", "Zip and download");
define("_AM_IMTRANSL_DONE", "You're Done!");
// Log messages
define("_AM_IMTRANSL_READ_ERR", "Cannot find the reference files.");
define("_AM_IMTRANSL_PARSE_ERR", "Reference folder doesn't contains language files.");
define("_AM_IMTRANSL_NO_TRANS_NEEDED_ERR", "All language constants are already defined.");
define("_AM_IMTRANSL_WRITE_ERR", "Cannot write in fille %s.");

?>