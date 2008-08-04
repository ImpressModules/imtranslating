<?php
/**
* Classes responsible for managing imTranslating log objects
*
* @copyright	The SmartFactory http://www.smartfactory.ca
* @license	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since	1.0
* @package	imTranslating
* @author	marcan <marcan@smartfactory.ca>
* @version	$Id: log.php 1394 2008-05-22 16:21:43Z marcan $
*/

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

class ImtranslatingLog extends IcmsPersistableObject {

    function ImtranslatingLog(&$handler) {
    	$this->IcmsPersistableObject($handler);

        $this->quickInitVar('log_id', XOBJ_DTYPE_INT, true);
        $this->quickInitVar('log_type_id', XOBJ_DTYPE_INT, true);
        $this->quickInitVar('log_category_id', XOBJ_DTYPE_INT, true);
        $this->quickInitVar('log_title', XOBJ_DTYPE_TXTBOX);
        $this->quickInitVar('log_description', XOBJ_DTYPE_TXTAREA);
	$this->quickInitVar('log_date', XOBJ_DTYPE_LTIME);
	$this->quickInitVar('log_creator_uid', XOBJ_DTYPE_INT);
	$this->quickInitVar('log_related_uid', XOBJ_DTYPE_INT);
	$this->quickInitVar('log_archived', XOBJ_DTYPE_INT);
	
	$this->setControl('log_type_id', array('itemHandler' => 'log',
			  'method' => 'getLog_typeArray',
			  'module' => 'imtranslating'));		

	$this->setControl('log_category_id', array('itemHandler' => 'log',
			  'method' => 'getLog_categoryArray',
			  'module' => 'imtranslating'));
							  
	$this->setControl('log_description', array(
			    'name' => 'textarea',
			    'rows' => 3));
									
	$this->setControl('log_creator_uid', 'user');
	$this->setControl('log_related_uid', 'user');
	$this->setControl('log_archived', 'yesno');
    }

    function getVar($key, $format = 's') {
        if ($format == 's' && in_array($key, array('log_type_id', 'log_category_id', 'log_creator_uid', 'log_related_uid'))) {
            return call_user_func(array($this,$key));
        }
        return parent::getVar($key, $format);
    }

    function log_type_id() {
	global $icmsPersistableRegistry;
    	$ret = $this->getVar('log_type_id', 'e');
	$obj = $icmsPersistableRegistry->getSingleObject('type', $ret, 'imtranslating');

    	if (!$obj->isNew()) {
	    $ret = $obj->getVar('type_name');
    	}
    	return $ret;
    }   
    
    function log_category_id() {
	global $icmsPersistableRegistry;
    	$ret = $this->getVar('log_category_id', 'e');
	$obj = $icmsPersistableRegistry->getSingleObject('category', $ret, 'imtranslating');

    	if (!$obj->isNew()) {
	    $ret = $obj->getVar('category_name');
    	}
    	return $ret;
    }      

    function log_creator_uid() {
        return icms_getLinkedUnameFromId($this->getVar('log_uid', 'e'));
    }

    function log_related_uid() {
        return icms_getLinkedUnameFromId($this->getVar('log_uid', 'e'));
    }
    
}
class ImtranslatingLogHandler extends IcmsPersistableObjectHandler {
	
    var $_log_typeArray = array();
    var $_log_categoryArray = array();
	
    function ImtranslatingLogHandler($db) {
        $this->IcmsPersistableObjectHandler($db, 'log', 'log_id', 'log_title', 'log_description', 'imtranslating');
    }
    
    function getLog_typeArray() {
	    if (!$this->_log_typeArray) {
		    global $icmsPersistableRegistry;
		    $criteria = new CriteriaCompo();
		    $criteria->setSort('type_name');
		    $this->_log_typeArray = $icmsPersistableRegistry->addListFromItemName('type', 'imtranslating', $criteria);
	    }
	    return $this->_log_typeArray;
    }

    function getLog_categoryArray() {
	if (!$this->_log_categoryArray) {
	    global $icmsPersistableRegistry;
	    $criteria = new CriteriaCompo();
	    $criteria->setSort('category_name');
	    $this->_log_categoryArray = $icmsPersistableRegistry->addListFromItemName('category', 'imtranslating', $criteria);
	}
	return $this->_log_categoryArray;
    }
}
?>