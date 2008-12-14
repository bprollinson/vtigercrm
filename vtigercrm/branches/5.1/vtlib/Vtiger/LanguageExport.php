<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once('vtlib/Vtiger/Package.php');

/**
 * Provides API to package vtiger CRM language files.
 * @package vtlib
 */
class Vtiger_LanguageExport extends Vtiger_Package {

	const TABLENAME = 'vtiger_language';

	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 * Initialize Language Schema
	 * @access private
	 */
	static function __initSchema() {
		$hastable = Vtiger_Utils::CheckTable(self::TABLENAME);
		if(!$hastable) {
			Vtiger_Utils::CreateTable(
				self::TABLENAME,
				'(id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name VARCHAR(50), prefix VARCHAR(10), label VARCHAR(30), lastupdated DATETIME, sequence INT, isdefault INT(1), active INT(1))'
			);
			global $languages, $adb;
			foreach($languages as $langkey=>$langlabel) {
				$adb->pquery('INSERT INTO '.self::TABLENAME.'(name,prefix,label,lastupdated,active) VALUES(?,?,?,?,?)',
					Array($langlabel,$langkey,$langlabel,date('Y-m-d H:i:s',time()), 1));
			}
		}
	}

	/**
	 * Register language pack information.
	 */
	static function register($prefix, $label, $isdefault=0, $active=1, $name='') {
		self::__initSchema();

		global $adb;
		$checkres = $adb->pquery('SELECT * FROM '.self::TABLENAME.' WHERE prefix=?', Array($prefix));
		$datetime = date('Y-m-d H:i:s');
		if($adb->num_rows($checkres)) {
			$id = $adb->query_result($checkres, 0, 'id');
			$adb->pquery('UPDATE '.self::TABLENAME.' set label=?, name=?, lastupdated=?, isdefault=?, active=? WHERE id=?',
				Array($label, $name, $datetime, $isdefault, $active, $id));
		} else {
			$seqres = $adb->query('SELECT max(sequence) as lastseq FROM '.self::TABLENAME);
			$lastseq = $adb->query_result($seqres, 0, 'lastseq');
			$nextseq = 1;
			if (isset($lastseq) && $lastseq != '') {
				$nextseq = $lastseq + 1;
			}
			$adb->pquery('INSERT INTO '.self::TABLENAME.' (name,prefix,label,lastupdated,isdefault,active,sequence) VALUES(?,?,?,?,?,?,?)',
				Array($name, $prefix, $label, $datetime, $isdefault, $active, $nextseq));
		}
		self::log("Registering Language $label [$prefix] ... DONE");		
	}

	/**
	 * De-Register language pack information
	 * @param String Language prefix like (de_de) etc
	 */
	static function deregister($prefix) {
		$prefix = trim($prefix);
		// We will not allow deregistering core language
		if(strtolower($prefix) == 'en_us') return;

		self::__initSchema();

		global $adb;
		$checkres = $adb->pquery('DELETE FROM '.self::TABLENAME.' WHERE prefix=?', Array($prefix));
		self::log("Deregistering Language $prefix ... DONE");
	}

	/**
	 * Get all the language information
	 * @param Boolean true to include in-active languages also, false (default)
	 */
	static function getAll($includeInActive=false) {
		global $adb;
		$hastable = Vtiger_Utils::CheckTable(self::TABLENAME);

		$languageinfo = Array();

		if($hastable) {
			if($includeInActive) $result = $adb->query('SELECT * FROM '.self::TABLENAME);
			else $result = $adb->query('SELECT * FROM '.self::TABLENAME . ' WHERE active=1');

			for($index = 0; $index < $adb->num_rows($result); ++$index) {
				$resultrow = $adb->fetch_array($result);
				$prefix = $resultrow['prefix'];
				$label  = $resultrow['label'];
				$languageinfo[$prefix] = $label;
			}
		} else {
			global $languages;
			foreach($languages as $prefix=>$label) {
				$languageinfo[$prefix] = $label;
			}
		}
		return $languageinfo;
	}
}
?>
