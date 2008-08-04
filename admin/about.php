<?php

/**
* About page of the module
*
* @copyright	The SmartFactory http://www.smartfactory.ca
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @package		Imtranslating
* @author		marcan <marcan@smartfactory.ca>
* @version		$Id: about.php 1383 2008-05-21 17:17:43Z marcan $
*/

include_once("admin_header.php");

include_once(SMARTOBJECT_ROOT_PATH . "class/smartobjectabout.php");
$aboutObj = new SmartobjectAbout();
$aboutObj->render();

?>