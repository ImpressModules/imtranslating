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

	public function compare(){
		//read the reference files
		if(!$this->read_from_files()){
			$this->setError(_IMTRANSL_READ_ERR);
			return false;
		}

		//parse the content and put it in an array of language constants
		if(!$this->parse_from_files()){
			$this->setError(_IMTRANSL_PARSE_ERR);
			return false;
		}

		//determine wich constant is missing in the 'to' language
		if(!$this->find_missing_const()){
			$this->setError(_IMTRANSL_NO_TRANS_NEEDED_ERR);
			return false;
		}

		//build the form to enter constant definitions
		if(!$this->getForm()){
			$this->setError(_IMTRANSL_NO_TRANS_NEEDED_ERR);
			return false;
		}


	}

	public function write_files(){
		//create new set of files


		//copy the content of the existing files

		//put the new translation at the end of it


	}

	private function read_from_files(){
		$file_array = scandir(ICMS_ROOT_PATH.'/language/'.$this->_from_lang.'/');

		if($this->_type == 'core'){
			$this->_from_lang_array = file(ICMS_ROOT_PATH.'/language/'.$this->_from_lang.'/admin.php');
		}
	}

	private function parse_from_files(){

	}

	private function find_missing_const(){

	}

	private function getForm(){

	}

	private function setError($err_msg){
		$this->_error[] = $err_msg;
	}


}



?>
