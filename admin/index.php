<?php

/**
* $Id: index.php 1394 2008-05-22 16:21:43Z marcan $
* Module: Imtranslating
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/
include_once("admin_header.php");
$translator = new ImtranslatingTranslator('french');
$translator->check_folders();
exit;
?>