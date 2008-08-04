<?php

/**
* Category, add, edit and delete category objects
*
* @copyright	The SmartFactory http://www.smartfactory.ca
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @package		Icategory
* @author		marcan <marcan@smartfactory.ca>
* @version		$Id: category.php 1394 2008-05-22 16:21:43Z marcan $
*/

function editcategory($showmenu = false, $category_id = 0, $parentid =0)
{
	global $icategory_category_handler, $xoopsModule;

	$categoryObj = $icategory_category_handler->get($category_id);

	$category_listid = isset($_GET['category_listid']) ? intval($_GET['category_listid']) : 0;
	$categoryObj->setVar('category_listid', $category_listid);

	if (!$categoryObj->isNew()){

		if ($showmenu) {
			$xoopsModule->displayAdminMenu(2, _AM_IMTRANSLATING_CATEGORIES . " > " . _CO_ICMS_EDITING);
		}
		$sform = $categoryObj->getForm(_AM_IMTRANSLATING_CATEGORY_EDIT, 'addcategory');
		$sform->display();
	} else {
		if ($showmenu) {
			$xoopsModule->displayAdminMenu(2, _AM_IMTRANSLATING_CATEGORIES . " > " . _CO_ICMS_CREATINGNEW);
		}
		$sform = $categoryObj->getForm(_AM_IMTRANSLATING_CATEGORY_CREATE, 'addcategory');
		$sform->display();
	}
}

include_once("admin_header.php");

$icategory_category_handler = xoops_getModuleHandler('category');

$op = '';

if (isset($_GET['op'])) $op = $_GET['op'];
if (isset($_POST['op'])) $op = $_POST['op'];

$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0 ;

switch ($op) {
	case "mod":
	case "changedField":

		xoops_cp_header();

		editcategory(true, $category_id);
		break;
	case "addcategory":
        include_once ICMS_ROOT_PATH."/kernel/icmspersistablecontroller.php";
        $controller = new IcmsPersistableController($icategory_category_handler);
		$controller->storeFromDefaultForm(_AM_IMTRANSLATING_CATEGORY_CREATED, _AM_IMTRANSLATING_CATEGORY_MODIFIED);

		break;

	case "del":
	    include_once ICMS_ROOT_PATH."/kernel/icmspersistablecontroller.php";
        $controller = new IcmsPersistableController($icategory_category_handler);
		$controller->handleObjectDeletion();

		break;

	case "view" :
		$categoryObj = $icategory_category_handler->get($category_id);

		smart_xoops_cp_header();
		smart_adminMenu(1, _AM_IMTRANSLATING_CATEGORY_VIEW . ' > ' . $categoryObj->getVar('category_title'));

		smart_collapsableBar('categoryview', $categoryObj->getVar('category_title') . $categoryObj->getEditCategoryLink(), _AM_IMTRANSLATING_CATEGORY_VIEW_DSC);

		$categoryObj->displaySingleObject();

		smart_close_collapsable('categoryview');

		smart_collapsableBar('categoryview_categorys', _AM_IMTRANSLATING_CATEGORIES, _AM_IMTRANSLATING_CATEGORIES_IN_CATEGORY_DSC);

		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('category_id', $category_id));

		$objectTable = new SmartObjectTable($icategory_category_handler, $criteria);
		$objectTable->addColumn(new SmartObjectColumn('category_date', 'left', 150));
		$objectTable->addColumn(new SmartObjectColumn('category_message'));
		$objectTable->addColumn(new SmartObjectColumn('category_uid', 'left', 150));

		$objectTable->addIntroButton('addcategory', 'category.php?op=mod&category_id=' . $category_id, _AM_IMTRANSLATING_CATEGORY_CREATE);

		$objectTable->render();

		smart_close_collapsable('categoryview_categorys');

		break;

	default:

		xoops_cp_header();

		$xoopsModule->displayAdminMenu(2, _AM_IMTRANSLATING_CATEGORIES);

		//smart_collapsableBar('createdcategorys', _AM_IMTRANSLATING_CATEGORIES, _AM_IMTRANSLATING_CATEGORIES_DSC);

		include_once ICMS_ROOT_PATH."/kernel/icmspersistabletable.php";
		$objectTable = new IcmsPersistableTable($icategory_category_handler);
		$objectTable->addColumn(new IcmsPersistableColumn('category_name', 'left', false, 'getCategory_nameAdminLink'));

		$objectTable->addIntroButton('addcategory', 'category.php?op=mod', _AM_IMTRANSLATING_CATEGORY_CREATE);

		$objectTable->addQuickSearch(array('category_name', 'category_description_small'));

		$objectTable->render();

		//smart_close_collapsable('createdcategorys');

		break;
}

xoops_cp_footer();

?>