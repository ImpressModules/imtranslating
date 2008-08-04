<?php

/**
* Type, add, edit and delete type objects
*
* @copyright	The SmartFactory http://www.smartfactory.ca
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @package		Itype
* @author		marcan <marcan@smartfactory.ca>
* @version		$Id: type.php 1394 2008-05-22 16:21:43Z marcan $
*/

function edittype($showmenu = false, $type_id = 0, $parentid =0)
{
	global $itype_type_handler, $xoopsModule;

	$typeObj = $itype_type_handler->get($type_id);

	$type_listid = isset($_GET['type_listid']) ? intval($_GET['type_listid']) : 0;
	$typeObj->setVar('type_listid', $type_listid);

	if (!$typeObj->isNew()){

		if ($showmenu) {
			$xoopsModule->displayAdminMenu(1, _AM_IMTRANSLATING_TYPES . " > " . _CO_ICMS_EDITING);
		}
		$sform = $typeObj->getForm(_AM_IMTRANSLATING_TYPE_EDIT, 'addtype');
		$sform->display();
	} else {
		if ($showmenu) {
			$xoopsModule->displayAdminMenu(1, _AM_IMTRANSLATING_TYPES . " > " . _CO_ICMS_CREATINGNEW);
		}
		$sform = $typeObj->getForm(_AM_IMTRANSLATING_TYPE_CREATE, 'addtype');
		$sform->display();
	}
}

include_once("admin_header.php");

$itype_type_handler = xoops_getModuleHandler('type');

$op = '';

if (isset($_GET['op'])) $op = $_GET['op'];
if (isset($_POST['op'])) $op = $_POST['op'];

$type_id = isset($_GET['type_id']) ? intval($_GET['type_id']) : 0 ;

switch ($op) {
	case "mod":
	case "changedField":

		xoops_cp_header();

		edittype(true, $type_id);
		break;
	case "addtype":
        include_once ICMS_ROOT_PATH."/kernel/icmspersistablecontroller.php";
        $controller = new IcmsPersistableController($itype_type_handler);
		$controller->storeFromDefaultForm(_AM_IMTRANSLATING_TYPE_CREATED, _AM_IMTRANSLATING_TYPE_MODIFIED);

		break;

	case "del":
	    include_once ICMS_ROOT_PATH."/kernel/icmspersistablecontroller.php";
        $controller = new IcmsPersistableController($itype_type_handler);
		$controller->handleObjectDeletion();

		break;

	case "view" :
		$typeObj = $itype_type_handler->get($type_id);

		smart_xoops_cp_header();
		smart_adminMenu(1, _AM_IMTRANSLATING_TYPE_VIEW . ' > ' . $typeObj->getVar('type_title'));

		smart_collapsableBar('typeview', $typeObj->getVar('type_title') . $typeObj->getEditTypeLink(), _AM_IMTRANSLATING_TYPE_VIEW_DSC);

		$typeObj->displaySingleObject();

		smart_close_collapsable('typeview');

		smart_collapsableBar('typeview_types', _AM_IMTRANSLATING_TYPES, _AM_IMTRANSLATING_TYPES_IN_TYPE_DSC);

		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('type_id', $type_id));

		$objectTable = new SmartObjectTable($itype_type_handler, $criteria);
		$objectTable->addColumn(new SmartObjectColumn('type_date', 'left', 150));
		$objectTable->addColumn(new SmartObjectColumn('type_message'));
		$objectTable->addColumn(new SmartObjectColumn('type_uid', 'left', 150));

		$objectTable->addIntroButton('addtype', 'type.php?op=mod&type_id=' . $type_id, _AM_IMTRANSLATING_TYPE_CREATE);

		$objectTable->render();

		smart_close_collapsable('typeview_types');

		break;

	default:

		xoops_cp_header();

		$xoopsModule->displayAdminMenu(1, _AM_IMTRANSLATING_TYPES);

		//smart_collapsableBar('createdtypes', _AM_IMTRANSLATING_TYPES, _AM_IMTRANSLATING_TYPES_DSC);

		include_once ICMS_ROOT_PATH."/kernel/icmspersistabletable.php";
		$objectTable = new IcmsPersistableTable($itype_type_handler);
		$objectTable->addColumn(new IcmsPersistableColumn('type_name', 'left', false, 'getType_nameAdminLink'));

		$objectTable->addIntroButton('addtype', 'type.php?op=mod', _AM_IMTRANSLATING_TYPE_CREATE);

		$objectTable->addQuickSearch(array('type_name', 'type_description_small'));

		$objectTable->render();

		//smart_close_collapsable('createdtypes');

		break;
}

xoops_cp_footer();

?>