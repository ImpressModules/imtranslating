<?php
/**
* Classes responsible for managing imTranslating category objects
*
* @copyright	The SmartFactory http://www.smartfactory.ca
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @package		imTranslating
* @author		marcan <marcan@smartfactory.ca>
* @version		$Id: log.php 1394 2008-05-22 16:21:43Z marcan $
*/

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

class ImtranslatingCategory extends IcmsPersistableObject {

    function ImtranslatingCategory(&$handler) {
    	$this->IcmsPersistableObject($handler);

        $this->quickInitVar('category_id', XOBJ_DTYPE_INT, true);
        $this->quickInitVar('category_name', XOBJ_DTYPE_TXTBOX);
        $this->quickInitVar('category_description', XOBJ_DTYPE_TXTAREA);
    }

    function getVar($key, $format = 's') {
        if ($format == 's' && in_array($key, array())) {
            return call_user_func(array($this,$key));
        }
        return parent::getVar($key, $format);
    }
    
    function getCategory_nameAdminLink() {
		return $this->getVar('category_name', 'e');
	}
    
}
class ImtranslatingCategoryHandler extends IcmsPersistableObjectHandler {
	
    function ImtranslatingCategoryHandler($db) {
        $this->IcmsPersistableObjectHandler($db, 'category', 'category_id', 'category_name', 'category_description', 'imtranslating');
    }
}
?>