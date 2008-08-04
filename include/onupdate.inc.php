<?php

if (!defined("XOOPS_ROOT_PATH")) {
 	die("XOOPS root path not defined");
}

global $modversion;
if( ! empty( $_POST['fct'] ) && ! empty( $_POST['op'] ) && $_POST['fct'] == 'modulesadmin' && $_POST['op'] == 'update_ok' && $_POST['dirname'] == $modversion['dirname'] ) {
	// referer check
	$ref = xoops_getenv('HTTP_REFERER');
	if( $ref == '' || strpos( $ref , XOOPS_URL.'/modules/system/admin.php' ) === 0 ) {
		/* module specific part */



		/* General part */

		// Keep the values of block's options when module is updated (by nobunobu)
		include dirname( __FILE__ ) . "/updateblock.inc.php" ;

	}
}

// this needs to be the latest db version
define('IMTRANSLATING_DB_VERSION', 1);

/*function imtranslating_db_upgrade_1() {
}
function imtranslating_db_upgrade_2() {
}*/

function xoops_module_update_imtranslating($module) {

	$icmsDatabaseUpdater = XoopsDatabaseFactory::getDatabaseUpdater();
	$icmsDatabaseUpdater->moduleUpgrade($module);
    return true;
}

function xoops_module_install_imtranslating($module) {

	return true;
}


?>