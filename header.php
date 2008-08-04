<?php

/**
* $Id: header.php 1383 2008-05-21 17:17:43Z marcan $
* Module: Imtranslating
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/

include_once "../../mainfile.php";

if( !defined("IMTRANSLATING_DIRNAME") ){
	define("IMTRANSLATING_DIRNAME", 'imtranslating');
}

include_once XOOPS_ROOT_PATH.'/modules/' . IMTRANSLATING_DIRNAME . '/include/common.php';
smart_loadCommonLanguageFile();

include_once IMTRANSLATING_ROOT_PATH . "include/functions.php";

?>