<?php

/**
* $Id: common.php 1383 2008-05-21 17:17:43Z marcan $
* Module: Imtranslating
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/
if (!defined("XOOPS_ROOT_PATH")) {
 	die("XOOPS root path not defined");
}

if( !defined("IMTRANSLATING_DIRNAME") ){
	define("IMTRANSLATING_DIRNAME", 'imtranslating');
}

if( !defined("IMTRANSLATING_URL") ){
	define("IMTRANSLATING_URL", IMPRESSCMS_URL.'/modules/'.IMTRANSLATING_DIRNAME.'/');
}
if( !defined("IMTRANSLATING_ROOT_PATH") ){
	define("IMTRANSLATING_ROOT_PATH", ICMS_ROOT_PATH.'/modules/'.IMTRANSLATING_DIRNAME.'/');
}

if( !defined("IMTRANSLATING_IMAGES_URL") ){
	define("IMTRANSLATING_IMAGES_URL", IMTRANSLATING_URL.'images/');
}

if( !defined("IMTRANSLATING_ADMIN_URL") ){
	define("IMTRANSLATING_ADMIN_URL", IMTRANSLATING_URL.'admin/');
}

/*
 * Including the common language file of the module
 */
$fileName = IMTRANSLATING_ROOT_PATH . 'language/' . $GLOBALS['xoopsConfig']['language'] . '/common.php';
if (!file_exists($fileName)) {
	$fileName = IMTRANSLATING_ROOT_PATH . 'language/english/common.php';
}

include_once($fileName);

include_once(IMTRANSLATING_ROOT_PATH . "include/functions.php");

// Creating the SmartModule object
$imtranslatingModule = icms_getModuleInfo(IMTRANSLATING_DIRNAME);

// Find if the user is admin of the module
$imtranslating_isAdmin = icms_userIsAdmin(IMTRANSLATING_DIRNAME);

$myts = MyTextSanitizer::getInstance();
if(is_object($imtranslatingModule)){
	$imtranslating_moduleName = $imtranslatingModule->getVar('name');
}

// Creating the SmartModule config Object
$imtranslatingConfig = icms_getModuleConfig(IMTRANSLATING_DIRNAME);

include_once ICMS_ROOT_PATH."/kernel/icmspersistableobject.php";

include_once(IMTRANSLATING_ROOT_PATH . 'class/log.php');
include_once(IMTRANSLATING_ROOT_PATH . 'class/type.php');
include_once(IMTRANSLATING_ROOT_PATH . 'class/category.php');

global $icmsPersistableRegistry;
$icmsPersistableRegistry = IcmsPersistableRegistry::getInstance();
// check of this is the first use of the module
if (is_object($xoopsModule) && $xoopsModule->dirname() == IMTRANSLATING_DIRNAME) {
	// We are in the module
	if (defined('XOOPS_CPFUNC_LOADED') && !defined('IMTRANSLATING_FIRST_USE_PAGE')) {
		// We are in the admin side of the module
		if (!$xoopsModule->getDBVersion()) {
			redirect_header(ICMS_URL . '/modules/system/admin.php?fct=modulesadmin&op=update&module=imtranslating', 4, _AM_IMTRANSLATING_FIRST_USE);
			exit;
		}
	}
}
?>