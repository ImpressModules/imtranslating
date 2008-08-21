<?php

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

global $modversion;

// this needs to be the latest db version
define('IMTRANSLATING_DB_VERSION', 1);

/*function imtranslating_db_upgrade_1() {
}
function imtranslating_db_upgrade_2() {
}*/

function xoops_module_update_imtranslating($module) {
	/*
	include_once(XOOPS_ROOT_PATH . "/modules/smartobject/class/smartdbupdater.php");
	$dbupdater = new SmartobjectDbupdater();
	$dbupdater->moduleUpgrade($module);
	*/
    return true;
}

function xoops_module_install_imtranslating($module) {

	return true;
}


?>