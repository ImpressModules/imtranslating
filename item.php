<?php

function edititem($item_itemid = 0)
{
	global $imtranslating_item_handler, $imtranslating_list_handler, $xoopsTpl;

	$itemObj = $imtranslating_item_handler->get($item_itemid);

	if (!$itemObj->isNew()){
		$sform = $itemObj->getForm(_MD_IMTRANSLATING_ITEM_EDIT, 'additem');
		$sform->assign($xoopsTpl, 'imtranslating_item');
		$xoopsTpl->assign('categoryPath', _MD_IMTRANSLATING_ITEM_EDIT);
	} else {
		$item_listid = isset($_GET['item_listid']) ? intval($_GET['item_listid']) : 0;
		$listObj = $imtranslating_list_handler->get($item_listid);

		$itemObj->setVar('item_listid', $item_listid);
		$itemObj->hideFieldFromForm(array('item_date', 'item_uid'));

		$sform = $itemObj->getForm(_MD_IMTRANSLATING_ITEM_CREATE, 'additem');
		$sform->assign($xoopsTpl, 'imtranslating_item');
		$xoopsTpl->assign('categoryPath', _MD_IMTRANSLATING_ITEM_CREATE);
	}
}

include_once('header.php');

$xoopsOption['template_main'] = 'imtranslating_item.html';
include_once(XOOPS_ROOT_PATH . "/header.php");
include_once SMARTOBJECT_ROOT_PATH."class/smartobjecttable.php";

$imtranslating_item_handler = xoops_getModuleHandler('item');
$imtranslating_log_handler = xoops_getModuleHandler('log');
$imtranslating_list_handler = xoops_getModuleHandler('list');

$op = '';

if (isset($_GET['op'])) $op = $_GET['op'];
if (isset($_POST['op'])) $op = $_POST['op'];

$item_itemid = isset($_GET['item_itemid']) ? intval($_GET['item_itemid']) : 0 ;

if (!$op && $item_itemid > 0) {
	$op = 'view';
}

switch ($op) {
	case "mod":
	case "changedField":

		imtranslating_checkPermission('item_add', 'list.php', _CO_IMTRANSLATING_ITEM_ADD_NOPERM);
		edititem($item_itemid);
		$xoopsTpl->assign('module_home', smart_getModuleName(true, true));
		break;

	case "additem":
        include_once XOOPS_ROOT_PATH."/modules/smartobject/class/smartobjectcontroller.php";
        $controller = new SmartObjectController($imtranslating_item_handler);
		$controller->storeFromDefaultForm(_MD_IMTRANSLATING_ITEM_CREATED, _MD_IMTRANSLATING_ITEM_MODIFIED);

		break;

	case "del":
		imtranslating_checkPermission('item_delete', 'list.php', _CO_IMTRANSLATING_ITEM_DELETE_NOPERM);
	    include_once XOOPS_ROOT_PATH."/modules/smartobject/class/smartobjectcontroller.php";
        $controller = new SmartObjectController($imtranslating_item_handler);
		$controller->handleObjectDeletionFromUserSide();
		$xoopsTpl->assign('module_home', smart_getModuleName(true, true));
		$xoopsTpl->assign('categoryPath', _MD_IMTRANSLATING_ITEM_DELETE);
		break;

	case "view" :
		$itemObj = $imtranslating_item_handler->get($item_itemid);

		$view_actions_col = array();
		if (imtranslating_checkPermission('item_add')) {
			$view_actions_col[] = 'edit';
		}
		if (imtranslating_checkPermission('item_delete')) {
			$view_actions_col[] = 'delete';
		}
		$xoopsTpl->assign('imtranslating_item_view', $itemObj->displaySingleObject(true, true, $view_actions_col, false));

		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('log_itemid', $item_itemid));

		$table_actions_col = array();
		if (imtranslating_checkPermission('log_add')) {
			$table_actions_col[] = 'edit';
		}
		if (imtranslating_checkPermission('log_delete')) {
			$table_actions_col[] = 'delete';
		}

		$objectTable = new SmartObjectTable($imtranslating_log_handler, $criteria, $table_actions_col);
		$objectTable->isForUserSide();

		$objectTable->addColumn(new SmartObjectColumn('log_date', 'left', 150));
		$objectTable->addColumn(new SmartObjectColumn('log_message'));
		$objectTable->addColumn(new SmartObjectColumn('log_uid', 'left', 150));

		if (imtranslating_checkPermission('log_add')) {
			$objectTable->addIntroButton('addlog', 'log.php?op=mod&log_itemid=' . $item_itemid, _MD_IMTRANSLATING_LOG_CREATE);
		}

		$xoopsTpl->assign('imtranslating_item_logs', $objectTable->fetch());

		$xoopsTpl->assign('module_home', smart_getModuleName(true, true));

		$xoopsTpl->assign('categoryPath', $itemObj->getVar('item_listid') . ' > ' . $itemObj->getVar('item_title'));

		break;

	default:
		$table_actions_col = array();

		$objectTable = new SmartObjectTable($imtranslating_item_handler, false, $table_actions_col);
		$objectTable->isForUserSide();

		$objectTable->addColumn(new SmartObjectColumn('item_name', 'left'));
		$objectTable->addColumn(new SmartObjectColumn('item_city', 'left', 150));
		$objectTable->addColumn(new SmartObjectColumn('item_phone', 'center', 150));

		$xoopsTpl->assign('imtranslating_items', $objectTable->fetch());
		$xoopsTpl->assign('module_home', smart_getModuleName(false, true));

		break;
}

include_once("footer.php");
?>