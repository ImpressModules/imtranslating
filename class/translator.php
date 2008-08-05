<?php


if (!defined("XOOPS_ROOT_PATH")) {
die("XOOPS root path not defined");
}



class ImtranslatingTranslator
{
	var $_from_lang;
	var $_to_lang;
	var $_translation_type;
	var $_errors = array();
	var $_from_lang_array = array();
	var $_to_lang_array = array();
	var $_need_translation_array = array();

	/**
	* constructor
	*/
	function ImtranslatingTranslator($to, $from = 'english', $type='core')
	{
		$this->_to_lang = $to;
		$this->_from_lang = $from;
		$this->_type = $type;
	}

	/*
	 * This method check if
	 * 		- the reference files exists
	 * 		- they are language definition files
	 * 		- constants are undefined in destination language
	 */
	public function check_folders(){
		//read the reference files
		if(!$this->read_from_files()){
			$this->setError(_AM_IMTRANSL_READ_ERR);
			return false;
		}

		//parse the content and put it in an array of language constants
		if(!$this->parse_from_files()){
			$this->setError(_AM_IMTRANSL_PARSE_ERR);
			return false;
		}

		//determine wich constant is missing in the 'to' language
		if(!$this->find_missing_const()){
			$this->setError(_AM_IMTRANSL_NO_TRANS_NEEDED_ERR);
			return false;
		}

		//files are correct and some constants need to be defined
		return true;
	}

	public function getForm(){

	}

	public function write_files(){
		//create new set of files


		//copy the content of the existing files

		//put the new translation at the end of it


	}

	private function read_from_files(){
		if($this->_type == 'core'){
			$file_array['core'] = scandir(ICMS_ROOT_PATH.'/language/'.$this->_from_lang.'/');
			$file_array['system'] = scandir(ICMS_ROOT_PATH.'/modules/system/language/'.$this->_from_lang.'/');

			foreach($file_array['core'] as $langfile){
				if(substr(strrev($langfile), 0, 3) == 'php'){
					$this->_from_lang_array['core'][$langfile] = file(ICMS_ROOT_PATH.'/language/'.$this->_from_lang.'/'.$langfile);
				}
			}
			foreach($file_array['system'] as $langfile){
				if(substr(strrev($langfile), 0, 3) == 'php'){
					$this->_from_lang_array['system'][$langfile] = file(ICMS_ROOT_PATH.'/modules/system/language/'.$this->_from_lang.'/'.$langfile);
				}
			}
			return true;

		}else{
			//TODO
			return false;
		}
	}


	private function parse_from_files(){
		if($this->_type == 'core'){
			foreach($this->_from_lang_array['core'] as $filename => $langfile_array){
				foreach($langfile_array as $key =>$line){
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
						//vars for parsing loop
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

						$langfile_array[$key] = array('const_name' => $const_name,
														'const_val' => $const_val);
					}//exit if not a define line

				}
				$this->_from_lang_array['core'][$filename] = $langfile_array;
			}
			return true;

		}else{
			//TODO
			return false;
		}
	}

	private function find_missing_const(){
		read_to_files();
	}



	private function setError($err_msg){
		$this->_error[] = $err_msg;
	}


}



?>
