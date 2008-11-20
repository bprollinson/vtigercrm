<?php

include_once('vtlib/Vtiger/Utils.php');

@include_once('include/events/include.inc');

class Vtiger_Event {

	var $eventname;
	var $classname;
	var $filename;

	static function log($message, $delim=true) {
		Vtiger_Utils::Log($message, $delim);
	}

	/**
	 * Check if vtiger CRM support Events.
	 */
	static function hasSupport() {
		return Vtiger_Utils::checkTable('vtiger_eventhandlers');
	}

	/**
	 * Handle event registration
	 */
	static function register($moduleInstance, $eventname, $classname, $filename) {
		// Security check on fileaccess, don't die if it fails
		if(Vtiger_Utils::checkFileAccess($filename, false)) {
			global $adb;
			$eventsManager = new VTEventsManager($adb);
			// TODO Update the call when API is fixed
			// $eventsManager->registerHandler($eventname, $classname, $filename,$moduleInstance->name);
			$eventsManager->registerHandler($eventname, $filename, $classname);

			self::log("Registering Event $eventname with [$filename] $classname ... DONE");
		}
	}

	/**
	 * Get events registered for a module.
	 */
	static function getAll($moduleInstance) {
		global $adb;
		$events = false;
		if(self::hasSupport()) {
			// TODO VTEventManager should provide API to get list of registered events on module
			$records = $adb->pquery("SELECT * FROM vtiger_eventhandlers"); 
			$reccount = $adb->num_rows($records);
			if($reccount) {
				for($index = 0; $index < $reccount; ++$index) {
					$event = new Vtiger_Event();
					$event->eventname = $adb->query_result($records, $index, 'event_name');
					$event->classname = $adb->query_result($records, $index, 'handler_class');
					$event->filename = $adb->query_result($records, $index, 'handler_path');
					$events[] = $event;
				}
			}
		}
		return $events;
	}		
}

?>
