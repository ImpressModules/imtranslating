<?php

/**
* Log, add, edit and delete log objects
*
* @copyright	The SmartFactory http://www.smartfactory.ca
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @package		Imtranslating
* @author		marcan <marcan@smartfactory.ca>
* @version		$Id: log.php 1394 2008-05-22 16:21:43Z marcan $
*/

function editlog($showmenu = false, $log_id = 0, $parentid =0)
{
	global $imtranslating_log_handler, $xoopsModule;

	$logObj = $imtranslating_log_handler->get($log_id);

	$log_listid = isset($_GET['log_listid']) ? intval($_GET['log_listid']) : 0;
	$logObj->setVar('log_listid', $log_listid);

	if (!$logObj->isNew()){

		if ($showmenu) {
			$xoopsModule->displayAdminMenu(0, _AM_IMTRANSLATING_LOGS . " > " . _CO_ICMS_EDITING);
		}
		$sform = $logObj->getForm(_AM_IMTRANSLATING_LOG_EDIT, 'addlog');
		$sform->display();
	} else {
		if ($showmenu) {
			$xoopsModule->displayAdminMenu(0, _AM_IMTRANSLATING_LOGS . " > " . _CO_ICMS_CREATINGNEW);
		}
		$sform = $logObj->getForm(_AM_IMTRANSLATING_LOG_CREATE, 'addlog');
		$sform->display();
	}
}

include_once("admin_header.php");

$imtranslating_log_handler = xoops_getModuleHandler('log');

$op = '';

if (isset($_GET['op'])) $op = $_GET['op'];
if (isset($_POST['op'])) $op = $_POST['op'];

$log_id = isset($_GET['log_id']) ? intval($_GET['log_id']) : 0 ;

switch ($op) {
	case "mod":
	case "changedField":

		xoops_cp_header();

		editlog(true, $log_id);
		break;
	case "addlog":
        include_once ICMS_ROOT_PATH."/kernel/icmspersistablecontroller.php";
        $controller = new IcmsPersistableController($imtranslating_log_handler);
		$controller->storeFromDefaultForm(_AM_IMTRANSLATING_LOG_CREATED, _AM_IMTRANSLATING_LOG_MODIFIED);

		break;

	case "del":
	    include_once ICMS_ROOT_PATH."/kernel/icmspersistablecontroller.php";
        $controller = new IcmsPersistableController($imtranslating_log_handler);
		$controller->handleObjectDeletion();

		break;

	case "view" :
		$logObj = $imtranslating_log_handler->get($log_id);

		smart_xoops_cp_header();
		smart_adminMenu(1, _AM_IMTRANSLATING_LOG_VIEW . ' > ' . $logObj->getVar('log_title'));

		smart_collapsableBar('logview', $logObj->getVar('log_title') . $logObj->getEditLogLink(), _AM_IMTRANSLATING_LOG_VIEW_DSC);

		$logObj->displaySingleObject();

		smart_close_collapsable('logview');

		smart_collapsableBar('logview_logs', _AM_IMTRANSLATING_LOGS, _AM_IMTRANSLATING_LOGS_IN_LOG_DSC);

		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('log_id', $log_id));

		$objectTable = new SmartObjectTable($imtranslating_log_handler, $criteria);
		$objectTable->addColumn(new SmartObjectColumn('log_date', 'left', 150));
		$objectTable->addColumn(new SmartObjectColumn('log_message'));
		$objectTable->addColumn(new SmartObjectColumn('log_uid', 'left', 150));

		$objectTable->addIntroButton('addlog', 'log.php?op=mod&log_id=' . $log_id, _AM_IMTRANSLATING_LOG_CREATE);

		$objectTable->render();

		smart_close_collapsable('logview_logs');

		break;

	default:

		xoops_cp_header();

		$xoopsModule->displayAdminMenu(0, _AM_IMTRANSLATING_LOGS);

		//smart_collapsableBar('createdlogs', _AM_IMTRANSLATING_LOGS, _AM_IMTRANSLATING_LOGS_DSC);

		include_once ICMS_ROOT_PATH."/kernel/icmspersistabletable.php";
		$objectTable = new IcmsPersistableTable($imtranslating_log_handler);
		$objectTable->addColumn(new IcmsPersistableColumn('log_date', 'left', 150));
		$objectTable->addColumn(new IcmsPersistableColumn('log_title', 'left'));
		$objectTable->addColumn(new IcmsPersistableColumn('log_type_id', 'left', 150));		
		$objectTable->addColumn(new IcmsPersistableColumn('log_category_id', 'left', 150));

		$objectTable->addIntroButton('addlog', 'log.php?op=mod', _AM_IMTRANSLATING_LOG_CREATE);

		$objectTable->addQuickSearch(array('log_name', 'log_description_small'));

		$objectTable->render();

		//smart_close_collapsable('createdlogs');

		break;
}

xoops_cp_footer();

?>