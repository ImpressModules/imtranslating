<?php

function editlog($log_logid = 0)
{
	global $imtranslating_log_handler, $xoopsTpl, $xoopsUser;

	if (!is_object($xoopsUser)) {
		redirect_header('index.php', 3, _NOPERM);
	}

	$logObj = $imtranslating_log_handler->get($log_logid);
	$logObj->setVar('log_uid', $xoopsUser->uid());
	$logObj->setVar('log_date', time());
	$logObj->hideFieldFromForm(array('log_uid', 'log_date'));

	$logObj->makeFieldReadOnly('log_itemid');

	if (!$logObj->isNew()){
		$sform = $logObj->getForm(_MD_IMTRANSLATING_LOG_EDIT, 'addlog');
		$sform->assign($xoopsTpl, 'imtranslating_log');
		$xoopsTpl->assign('categoryPath', _MD_IMTRANSLATING_LOG_EDIT);
	} else {
		$log_itemid = isset($_GET['log_itemid']) ? intval($_GET['log_itemid']) : 0;
		$imtranslating_item_handler = xoops_getModuleHandler('item');
		$itemObj = $imtranslating_item_handler->get($log_itemid);
		if ($itemObj->isNew()) {
			redirect_header('index.php', 3, _NOPERM);
		}
		$logObj->setVar('log_itemid', $log_itemid);
		$sform = $logObj->getForm(_MD_IMTRANSLATING_LOG_CREATE, 'addlog');
		$sform->assign($xoopsTpl, 'imtranslating_log');
		$xoopsTpl->assign('categoryPath', _MD_IMTRANSLATING_LOG_CREATE);
	}
}


include_once('header.php');

$xoopsOption['template_main'] = 'imtranslating_log.html';
include_once(XOOPS_ROOT_PATH . "/header.php");
include_once SMARTOBJECT_ROOT_PATH."class/smartobjecttable.php";

$imtranslating_log_handler = xoops_getModuleHandler('log');
$imtranslating_item_handler = xoops_getModuleHandler('item');

$op = '';

if (isset($_GET['op'])) $op = $_GET['op'];
if (isset($_POST['op'])) $op = $_POST['op'];

$log_logid = isset($_GET['log_logid']) ? intval($_GET['log_logid']) : 0 ;

if (!$op && $log_logid > 0) {
	$op = 'view';
}

switch ($op) {
	case "mod":
	case "changedField":

		imtranslating_checkPermission('log_add', 'list.php', _CO_IMTRANSLATING_LOG_ADD_NOPERM);
		editlog($log_logid);
		$xoopsTpl->assign('module_home', smart_getModuleName(true, true));
		break;

	case "addlog":
        include_once XOOPS_ROOT_PATH."/modules/smartobject/class/smartobjectcontroller.php";
        $controller = new SmartObjectController($imtranslating_log_handler);
		$controller->storeFromDefaultForm(_MD_IMTRANSLATING_LOG_CREATED, _MD_IMTRANSLATING_LOG_MODIFIED);

		break;

	case "del":
		imtranslating_checkPermission('log_delete', 'list.php', _CO_IMTRANSLATING_LOG_DELETE_NOPERM);
	    include_once XOOPS_ROOT_PATH."/modules/smartobject/class/smartobjectcontroller.php";
        $controller = new SmartObjectController($imtranslating_log_handler);
		$controller->handleObjectDeletionFromUserSide();
		$xoopsTpl->assign('module_home', smart_getModuleName(true, true));
		$xoopsTpl->assign('categoryPath', _MD_IMTRANSLATING_LOG_DELETE);
		break;

	case "view" :
		$logObj = $imtranslating_log_handler->get($log_logid);

		$view_actions_col = array();
		if (imtranslating_checkPermission('log_add')) {
			$view_actions_col[] = 'edit';
		}
		if (imtranslating_checkPermission('log_delete')) {
			$view_actions_col[] = 'delete';
		}

		$xoopsTpl->assign('imtranslating_log_view', $logObj->displaySingleObject(true, true, $view_actions_col, false));

		$xoopsTpl->assign('module_home', smart_getModuleName(true, true));

		$xoopsTpl->assign('categoryPath', $logObj->getVar('log_itemid'));

		break;

	default:
		redirect_header(IMTRANSLATING_URL, 3, _NOPERM);
		break;
}

include_once("footer.php");
?>