<?php

/**
* $Id: first_use.php 1383 2008-05-21 17:17:43Z marcan $
* Module: SmartShop
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/

define('IMTRANSLATING_FIRST_USE_PAGE', true);

include_once("admin_header.php");

if (isset($_POST['op']) && ($_POST['op'] == 'domoduleusage')) {
	// Set the preferences accoridng to module usage selected
	smart_SetMeta('module_usage', $_POST['module_use']);
	/**
	 * @todo now we need to edit the xoops_version and configure config array accordingly to smart_getMeta('module_usage')
	 */
	redirect_header(XOOPS_URL . '/modules/system/admin.php?fct=modulesadmin&op=update&module=' . $xoopsModule->dirname(), 4, _AM_SSHOP_SELECT_MODULE_DONE_UPDATE_MODULE);
	exit;

} else {
	smart_xoops_cp_header();

	smart_adminMenu(0, _AM_SSHOP_FIRST_USE);

	smart_collapsableBar('createdcategories', _AM_SSHOP_SELECT_MODULE_USE, _AM_SSHOP_SELECT_MODULE_USE_DSC);

	$form = new XoopsThemeForm(_AM_SSHOP_SELECT_MODULE_USE_FORM, "form", xoops_getenv('PHP_SELF'));
	$form->setExtra( "enctype='multipart/form-data'" ) ;

	$module_use_select = new XoopsFormSelect(_AM_SSHOP_SELECT_MODULE_USE, 'module_use', '', 1, false);

	$module_use_array = array(
							'adds' => 'Adds Module',
							'boutique' => 'Boutique Module',
							'directory' => 'Directory Module',
							'dynamic_directory' => 'Dynamic Directory Module'
							);
	$module_use_select->addOptionArray($module_use_array);

	$form->addElement($module_use_select, true);

	$form_button_tray = new XoopsFormElementTray('', '');
	$form_hidden = new XoopsFormHidden('op', '');
	$form_button_tray->addElement($form_hidden);

	$form_butt_create = new XoopsFormButton('', '', _SUBMIT, 'submit');
	$form_butt_create->setExtra('onclick="this.form.elements.op.value=\'domoduleusage\'"');
	$form_button_tray->addElement($form_butt_create);

	$butt_cancel = new XoopsFormButton('', '', _CANCEL, 'button');
	$butt_cancel->setExtra('onclick="history.go(-1)"');
	$form_button_tray->addElement($butt_cancel);

	$form->addElement($form_button_tray);

	$form->display();

	echo "<br />";
	smart_close_collapsable('createdcategories');
	echo "<br>";

	smartshop_modFooter();
	xoops_cp_footer();

}
?>