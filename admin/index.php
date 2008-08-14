<?php

/**
* $Id: index.php 1394 2008-05-22 16:21:43Z marcan $
* Module: Imtranslating
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/
include_once("admin_header.php");
xoops_cp_header();


if(empty($_POST)){
	$job = new ImtranslatingJob();
	$form = $job->getInitialForm();
	$form->display();
	exit;
}else{
	$job = new ImtranslatingJob($_POST['from_lang'], $_POST['to_lang'], $_POST['module'], $_POST['step'], $_POST['fileset']);
	if($_POST['step'] == 'zip'){
		$job->makeZip();
		exit;
	}else{
		if($_POST['write'] == 1){
			$job->write();
		}
		$form = $job->getForm();
		$form->display();
	}
}

xoops_cp_footer();

exit;
?>