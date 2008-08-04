<?php

/**
* $Id: footer.php 1383 2008-05-21 17:17:43Z marcan $
* Module: Imtranslating
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/
if (!defined("XOOPS_ROOT_PATH")) {
 	die("XOOPS root path not defined");
}

$xoopsTpl->assign("imtranslating_adminpage", smart_getModuleAdminLink());
$xoopsTpl->assign("isAdmin", $imtranslating_isAdmin);
$xoopsTpl->assign('imtranslating_url', IMTRANSLATING_URL);
$xoopsTpl->assign('imtranslating_images_url', IMTRANSLATING_IMAGES_URL);

$xoTheme->addStylesheet(IMTRANSLATING_URL . 'module.css');

$xoopsTpl->assign("ref_smartfactory", "Imtranslating is developed by The SmartFactory (http://smartfactory.ca), a division of INBOX International (http://inboxinternational.com)");

include_once(XOOPS_ROOT_PATH . '/footer.php');

?>