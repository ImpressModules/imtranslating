<?php


if (!defined("XOOPS_ROOT_PATH")) {
die("XOOPS root path not defined");
}

define("_IMTRANSLATING_UPLOAD_PATH", ICMS_ROOT_PATH."/uploads/imtranslating");

class ImtranslatingJob
{
	var $_errors = array();
	var $_from_lang;
	var $_to_lang;
	var $_step;
	var $_module;
	var $_from_path;
	var $_to_path;
	var $_current_file;
	var $_ref_lang_array;
	var $_missing_const = array();
	var $_fileset;


	/*var $_from_lang_array = array();
	var $_to_lang_array = array();
	var $_need_translation_array = array();*/

	/**
	* constructor
	*/
	function ImtranslatingJob($from_lang = '', $to_lang = '', $module = 'core', $step = 0, $fileset = 'default')
	{
		$this->_from_lang = $from_lang;
		$this->_to_lang = $to_lang;
		$this->_module = $module;
		$this->_step = $step;
		$this->_fileset = $fileset;
		$this->setPath();

	}

	function getInitialForm(){
		$form = new XoopsThemeForm(_AM_IMTRANSL_JOB, "job_form", xoops_getenv('PHP_SELF'));

		$lang_from_select = new XoopsFormSelect(_AM_IMTRANSL_FROM_LANG, "from_lang", $this->_from_lang);
		$lang_from_select->addOptionArray($this->getLangArray());
		$form->addElement($lang_from_select);

		$lang_to_select = new XoopsFormSelect(_AM_IMTRANSL_TO_LANG, "to_lang", $this->_to_lang);
		$lang_to_select->addOptionArray($this->getLangArray());
		$form->addElement($lang_to_select);

		$module_select = new XoopsFormSelect(_AM_IMTRANSL_MODULE, "module", $this->_module);
		$module_select->addOptionArray($this->getModuleArray());
		$form->addElement($module_select);

		$form->addElement(new XoopsFormHidden('step', 0));
		$form->addElement(new XoopsFormHidden('fileset', 'default'));

		$button_tray = new XoopsFormElementTray('', '');

		$butt_create = new XoopsFormButton('', '', _AM_IMTRANSL_GO, 'submit');
		$button_tray->addElement($butt_create);

		$butt_cancel = new XoopsFormButton('', '', _AM_IMTRANSL_CANCEL, 'button');
		$butt_cancel->setExtra('onclick="history.go(-1)"');
		$button_tray->addElement($butt_cancel);

		$form->addElement($button_tray);

		return $form;
	}

	private function  getModuleArray(){
		$module_handler =& xoops_gethandler('module');
        $list = $module_handler->getList(null, true);
        $ret['core'] = _AM_IMTRANSL_COREFILES;
        foreach($list as $key => $mod){
        	if($mod != 'System'){
        		$ret[$key] = $mod ;
        	}
        }

        return $ret;
	}

	private function getLangArray(){
		$dirs = scandir(ICMS_ROOT_PATH.'/language/');
		$lang_array = array();
		foreach($dirs as $dir){
			if($dir != '.' && $dir != '..' && $dir != '.svn'&& $dir != 'CVS' && $dir != 'index.html'){
				$lang_array[$dir] = $dir;
			}
		}
		return $lang_array;
	}

	function getForm(){
		//get the name of the file to translate
		$this->_current_file = $this->getFileName($this->_step);
		if(!$this->_current_file){
			if($this->_module == 'core'){
				if($this->_fileset == 'default'){
					$this->_fileset = 'install';
					$this->_step = 0;
					$this->setPath();
					return $this->getForm();
				}elseif($this->_fileset == 'install'){
					$this->_fileset = 'system';
					$this->_step = 0;
					$this->setPath();
					return $this->getForm();
				}elseif($this->_fileset == 'system'){
					$this->_fileset = 'system/admin';
					$this->_step = 0;
					$this->setPath();
					return $this->getForm();
				}else{
				return $this->getFinishForm();
			}
			}else{
				return $this->getFinishForm();
			}
		}


		//get the parsed language constants
		$this->_ref_lang_array = $this->parse_from_lang_files();
		if(!empty($this->_ref_lang_array)){
			$this->_missing_const = $this->find_missing_const();
		}
		if(empty($this->_missing_const)){
			$this->_step ++;
			return $this->getForm();
		}else{
			return $this->getTranslationForm();
		}
	}

	function setPath(){
		//determines the path of the both laguage file set
		if($this->_module == 'core'){
			if($this->_fileset == 'default'){
				$this->_from_path = ICMS_ROOT_PATH.'/language/'.$this->_from_lang.'/';
				$this->_to_path = ICMS_ROOT_PATH.'/language/'.$this->_to_lang.'/';
			}elseif($this->_fileset == 'install'){
				$this->_from_path = ICMS_ROOT_PATH.'/install/language/'.$this->_from_lang.'/';
				$this->_to_path = ICMS_ROOT_PATH.'/install/language/'.$this->_to_lang.'/';

			}elseif($this->_fileset == 'system'){
				$this->_from_path = ICMS_ROOT_PATH.'/modules/system/language/'.$this->_from_lang.'/';
				$this->_to_path = ICMS_ROOT_PATH.'/modules/system/language/'.$this->_to_lang.'/';

			}else{
				$this->_from_path = ICMS_ROOT_PATH.'/modules/system/language/'.$this->_from_lang.'/admin/';
				$this->_to_path = ICMS_ROOT_PATH.'/modules/system/language/'.$this->_to_lang.'/admin/';
			}

		}else{
			$this->_to_path = ICMS_ROOT_PATH.'/modules/'.$this->_module.'/language/'.$this->_to_lang.'/';
			$this->_from_path = ICMS_ROOT_PATH.'/modules/'.$this->_module.'/language/'.$this->_from_lang.'/';
		}

	}

	function getFileName($step){
		//get the content of the reference folder and clean it
		$all_files =  scandir($this->_from_path);

		var_dump($this->_from_path);echo"<br>";
		var_dump($all_files);echo"<br>";
		foreach ($all_files as $the_file){
			if(substr(strrev($the_file), 0, 3) == 'php'){
				$ref_files[] = $the_file;
			}
		}
		echo $step."<br>";
		var_dump($ref_files[$step]);echo"<br>";echo"<br>";
		if(isset($ref_files[$step])){
			return $ref_files[$step];
		}else{
			return false;
		}
	}

	private function getTranslationForm(){
		$form = new XoopsThemeForm(sprintf(_AM_IMTRANSL_JOB, $this->_current_file), "translation_form", xoops_getenv('PHP_SELF'));

		foreach($this->_missing_const as $const){
			$form->addElement(new XoopsFormLabel(sprintf(_AM_IMTRANSL_ORIGINAL, ucfirst($this->_from_lang),$const), $this->_ref_lang_array[$const]));
			$form->addElement(new XoopsFormTextArea(_AM_IMTRANSL_TRANSLATION, $const));
		}

		$form->addElement(new XoopsFormHidden('from_lang', $this->_from_lang));
		$form->addElement(new XoopsFormHidden('to_lang', $this->_to_lang));
		$form->addElement(new XoopsFormHidden('module', $this->_module));
		$form->addElement(new XoopsFormHidden('step', $this->_step+1));
		$form->addElement(new XoopsFormHidden('fileset', $this->_fileset));
		$form->addElement(new XoopsFormHidden('write', 1));
		$button_tray = new XoopsFormElementTray('', '');

		$butt_create = new XoopsFormButton('', '', _AM_IMTRANSL_GO, 'submit');
		$button_tray->addElement($butt_create);

		$butt_cancel = new XoopsFormButton('', '', _AM_IMTRANSL_CANCEL, 'button');
		$butt_cancel->setExtra('onclick="location=\'index.php\'"');
		$button_tray->addElement($butt_cancel);

		$form->addElement($button_tray);

		return $form;
	}

	function getFinishForm(){
		$form = new XoopsThemeForm(_AM_IMTRANSL_DONE, "finish_form", xoops_getenv('PHP_SELF'));

		$button_tray = new XoopsFormElementTray('', '');
		$form->addElement(new XoopsFormHidden('from_lang', $this->_from_lang));
		$form->addElement(new XoopsFormHidden('to_lang', $this->_to_lang));
		$form->addElement(new XoopsFormHidden('module', $this->_module));

		$form->addElement(new XoopsFormHidden('step', 'zip'));
		$butt_create = new XoopsFormButton('', '', _AM_IMTRANSL_ZIP, 'submit');
		$button_tray->addElement($butt_create);

		$butt_cancel = new XoopsFormButton('', '', _AM_IMTRANSL_CANCEL, 'button');
		$butt_cancel->setExtra('onclick="location=\'index.php\'"');
		$button_tray->addElement($butt_cancel);

		$form->addElement($button_tray);

		return $form;
	}


	private function parse_from_lang_files(){
		$myts =& MyTextSanitizer::getInstance();
		$raw_langfile = file($this->_from_path.$this->_current_file);
		$langfile_array = array();
		foreach($raw_langfile as $key =>$line){
			//erease spaces at the begining
			while(substr($line, 0, 1) == ' '){
				$line = substr($line, 1);
			}
			//look for 'define' instruction
			if(substr($line, 0, 6) == 'define'){
				//Isolate constant name
				$line = substr($line, 6);
				//erease spaces and parenthesis after 'define'
				while(substr($line, 0, 1) == ' ' || substr($line, 0, 1) == '('){
					$line = substr($line, 1);
				}
				if(substr($line, 0, 1) == '"'){
					$const_name = substr($line, 1, stripos( substr($line, 1), '"'));
					$line = substr($line, stripos( substr($line, 1), '"')+2);
				}
				if(substr($line, 0, 1) =="'"){
					$const_name = substr($line, 1, stripos( substr($line, 1), "'"));
					$line = substr($line, stripos( substr($line, 1), "'")+2);
				}

				//Isolate constant definition
				//erease spaces and comma before constant value
				while(substr($line, 0, 1) == ' ' || substr($line, 0, 1) == ','){
					$line = substr($line, 1);
				}

				$line = substr($line, 1);
				$line = substr(strrev($line), 1);
				$line = substr($line, 1);
				while(substr($line, 0, 1) == ' ' ){
					$line = substr($line, 1);
				}
				$line = substr($line, 3);
				$langfile_array[$const_name] = $myts->htmlSpecialChars(strrev($line));
			}
		}
		return $langfile_array;
	}

	private function find_missing_const(){
		$already_defined = $this->get_defined_const_names();
		if(is_array($already_defined)){
			return array_diff(array_keys($this->_ref_lang_array), $already_defined);
		}else{
			return array_keys($this->_ref_lang_array);
		}

	}

	private function get_defined_const_names(){
		$raw_langfile = file($this->_to_path.$this->_current_file);
		foreach($raw_langfile as $key =>$line){
			//erease spaces at the begining
			while(substr($line, 0, 1) == ' '){
				$line = substr($line, 1);
			}
			//look for 'define' instruction
			if(substr($line, 0, 6) == 'define'){
				//Isolate constant name
				$line = substr($line, 6);
				//erease spaces and parenthesis after 'define'
				while(substr($line, 0, 1) == ' ' || substr($line, 0, 1) == '('){
					$line = substr($line, 1);
				}
				if(substr($line, 0, 1) == '"'){
					$const_name = substr($line, 1, stripos( substr($line, 1), '"'));
					$line = substr($line, stripos( substr($line, 1), '"')+2);
				}
				if(substr($line, 0, 1) =="'"){
					$const_name = substr($line, 1, stripos( substr($line, 1), "'"));
					$line = substr($line, stripos( substr($line, 1), "'")+2);
				}

				//Isolate constant definition
				//erease spaces and comma before constant value
				while(substr($line, 0, 1) == ' ' || substr($line, 0, 1) == ','){
					$line = substr($line, 1);
				}


				$const_array[] = $const_name;
			}
		}
		return $const_array;
	}

	function write(){
		$myts =& MyTextSanitizer::getInstance();
		$old_version_path = $this->_to_path."/";
		$new_version_path = _IMTRANSLATING_UPLOAD_PATH.str_replace(ICMS_ROOT_PATH, '', $this->_to_path);
		$filename = $this->getFileName($_POST['step']-1);
		if(!is_dir($new_version_path)){
			if(!is_dir(_IMTRANSLATING_UPLOAD_PATH)){
				mkdir(_IMTRANSLATING_UPLOAD_PATH);
			}
			if($this->_module == 'core'){
					switch($this->_fileset){
					case 'default';
						mkdir(_IMTRANSLATING_UPLOAD_PATH."/language/");
						mkdir(_IMTRANSLATING_UPLOAD_PATH."/language/".$this->_to_lang);
					break;
					case 'install';
						mkdir(_IMTRANSLATING_UPLOAD_PATH."/install/");
						mkdir(_IMTRANSLATING_UPLOAD_PATH."/install/language/");
						mkdir(_IMTRANSLATING_UPLOAD_PATH."/install/language/".$this->_to_lang);
					break;
					case 'system';
						mkdir(_IMTRANSLATING_UPLOAD_PATH."/modules/");
						mkdir(_IMTRANSLATING_UPLOAD_PATH."/modules/system/");
						mkdir(_IMTRANSLATING_UPLOAD_PATH."/modules/system/language/");
						mkdir(_IMTRANSLATING_UPLOAD_PATH."/modules/system/language/".$this->_to_lang);
					break;
					case 'system/admin';
						if(!is_dir(_IMTRANSLATING_UPLOAD_PATH."/modules/system/language/")){
							mkdir(_IMTRANSLATING_UPLOAD_PATH."/modules/");
							mkdir(_IMTRANSLATING_UPLOAD_PATH."/modules/system/");
							mkdir(_IMTRANSLATING_UPLOAD_PATH."/modules/system/language/");
							mkdir(_IMTRANSLATING_UPLOAD_PATH."/modules/system/language/".$this->_to_lang);
						}
						mkdir(_IMTRANSLATING_UPLOAD_PATH."/modules/system/language/".$this->_to_lang."/admin/");
					break;
				}
			}else{
				if(!is_dir(_IMTRANSLATING_UPLOAD_PATH."/modules/")){
					mkdir(_IMTRANSLATING_UPLOAD_PATH."/modules/");
				}
				if(!is_dir(_IMTRANSLATING_UPLOAD_PATH."/modules/".$this->_module)){
					mkdir(_IMTRANSLATING_UPLOAD_PATH."/modules/".$this->_module);
				}
				if(!is_dir(_IMTRANSLATING_UPLOAD_PATH."/modules/".$this->_module."/language/")){
					mkdir(_IMTRANSLATING_UPLOAD_PATH."/modules/".$this->_module."/language/");
				}
				if(!is_dir(_IMTRANSLATING_UPLOAD_PATH."/modules/".$this->_module."/language/".$this->_to_lang)){
					mkdir(_IMTRANSLATING_UPLOAD_PATH."/modules/".$this->_module."/language/".$this->_to_lang);
				}

			}
		}
		if($newfile = fopen($new_version_path.$filename, 'w')){
			if(file_exists($old_version_path.$filename)){
				fwrite($newfile,  str_replace('?>', _AM_IMTRANSL_COMMENT , file_get_contents($old_version_path.$filename))."\r\n");
			}else{
				fwrite($newfile, "<?\r\n"._AM_IMTRANSL_COMMENT ."\r\n");
			}
			foreach($_POST as $def => $value){
				if(!in_array($def, array('step', 'module', 'to_lang', 'from_lang', 'fileset', 'write')) && $value != ''){
					fwrite($newfile, 'define("'.$def.'", "'.$myts->undoHtmlSpecialChars(utf8_decode($value)).'");'."\r\n");

				}
			}
			fwrite($newfile,  '?>');
			fclose($newfile);

			return true;
		}else{
			$this->setError(sprintf(_AM_IMTRANSL_WRITE_ERR, $new_version_path.$filename));
			return false;
		}

	}
	private function setError($err_msg){
		$this->_error[] = $err_msg;
	}

	private function getErrors($err_msg){
		foreach($this->_error[] as $error){
			echo "<h4>".$error."</h4>";
		}
	}

	function makeZip(){
		$zipper = new spectreZip();
		/*foreach(scandir(_IMTRANSLATING_UPLOAD_PATH.$this->_to_lang."/") as $file){
			$zipper->addFile(_IMTRANSLATING_UPLOAD_PATH.$this->_to_lang."/".$file);
		}*/
		$zipper->addDir(_IMTRANSLATING_UPLOAD_PATH."/");
		$zipper->render('imtranslating.zip');
	}

}



?>
