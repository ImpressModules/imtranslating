<?php


if (!defined("XOOPS_ROOT_PATH")) {
die("XOOPS root path not defined");
}

define("_IMTRANSLATING_UPLOAD_PATH", ICMS_ROOT_PATH."/uploads/imtranslating/");

class ImtranslatingJob
{
	var $_errors = array();
	var $_from_lang;
	var $_to_lang;
	var $_step;
	var $_module;
	var $_path;
	var $_current_file;
	var $_ref_lang_array;
	var $_missing_const;


	/*var $_from_lang_array = array();
	var $_to_lang_array = array();
	var $_need_translation_array = array();*/

	/**
	* constructor
	*/
	function ImtranslatingJob($from_lang = '', $to_lang = '', $module = 'core', $step = 0)
	{
		$this->_from_lang = $from_lang;
		$this->_to_lang = $to_lang;
		$this->_module = $module;
		$this->_step = $step;
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

		$form->addElement(new XoopsFormHidden('step', $this->_step));

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
        $ret = $module_handler->getList(null, true);
        $ret['core'] = _AM_IMTRANSL_COREFILES;
        $ret['install'] = _AM_IMTRANSL_INSTALLFILES;

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
		//var_dump($this->_current_file);echo"<br>";
		if(!$this->_current_file){
			return $this->getFinishForm();
		}

		//get the parsed language constants
		$this->_ref_lang_array = $this->parse_lang_files($this->_from_lang);

		$this->_missing_const = $this->find_missing_const();
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
			$this->_path = ICMS_ROOT_PATH.'/language/';
		}elseif($this->_module == 'install'){
			$this->_path = ICMS_ROOT_PATH.'install/language/';
		}else{
			$this->_path = ICMS_ROOT_PATH.'/modules/'.$this->_module.'/language/';
		}

	}

	function getFileName($step){
		//get the content of the reference folder and clean it
		$all_files =  scandir($this->_path.$this->_from_lang.'/');
		foreach ($all_files as $the_file){
			if(substr(strrev($the_file), 0, 3) == 'php'){
				$ref_files[] = $the_file;
			}
		}
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


	private function parse_lang_files($lang){
		$myts =& MyTextSanitizer::getInstance();
		$raw_langfile = file($this->_path.$lang.'/'.$this->_current_file);
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
				/*//vars for parsing loop
				$temp_line = $line;
				$ok = false;
				$cut = 0;

				//parse a "" quoted const value
				if(substr($temp_line, 0, 1) == '"'){
					while(!$ok){
						$end = stripos( substr($temp_line, 1), '"');
						if(substr($temp_line, $end, 2) != '\"'){
							$ok = true;
						}else{
							$cut += $end+1;
							$temp_line = substr($temp_line, $end+1);
						}
					}
					$const_val = substr($line, 1, $end+$cut);
				}

				//parse a '' quoted const value
				if(substr($temp_line, 0, 1) == "'"){
					while(!$ok){
						$end = stripos( substr($temp_line, 1), "'");
						if(substr($temp_line, $end, 2) != "\'"){
							$ok = true;
						}else{
							$cut += $end+1;
							$temp_line = substr($temp_line, $end+1);
						}
					}
					$const_val = substr($line, 1, $end+$cut);
				}
				*/
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
		$already_defined = $this->get_const_names($this->_to_lang);
		return array_diff(array_keys($this->_ref_lang_array), $already_defined);

	}

	private function get_const_names($lang){
		$raw_langfile = file($this->_path.$lang.'/'.$this->_current_file);
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
		$old_version_path = $this->_path.$this->_to_lang."/";
		$new_version_path = _IMTRANSLATING_UPLOAD_PATH.$this->_to_lang."/";
		$filename = $this->getFileName($_POST['step']-1);
		if(!is_dir($new_version_path)){
			//TODO :create dest path
		}
		if($newfile = fopen($new_version_path.$filename, 'w')){
			fwrite($newfile,  str_replace('?>', _AM_IMTRANSL_COMMENT , file_get_contents($old_version_path.$filename))."\r\n");
			foreach($_POST as $def => $value){
				if($def != 'step' && $def != 'module' && $def != 'to_lang' && $def != 'from_lang'){
					fwrite($newfile, 'define("'.$def.'", "'.str_replace(array('"', "'"), array('\"', "\'"), $value).'");'."\r\n");
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
		$zipper->addDir(_IMTRANSLATING_UPLOAD_PATH.$this->_to_lang."/");
		$zipper->render($this->_to_lang.'.zip');
	}

}



?>
