<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

class vtigerLogger {
	var $debug_status; 
	var $log1;

	function logthis($msg,$loglevel) {
		if($this->debug_status)	{
                	require_once('include/logging.php');


                	if(is_array($msg))
				$this->log1->$loglevel("Message".print_r($msg,true));
                	else
				$this->log1->$loglevel("Message ->".$msg);

                	return $msg;
		}
        }
	
	function vtigerLogger() {
		$this->debug_status= true;
		$this->log1 =& LoggerManager::getLogger('VT');
	}
}

?>
