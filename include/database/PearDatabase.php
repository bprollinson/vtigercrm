<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/

require_once('include/logging.php');
include('adodb/adodb.inc.php');
require_once("adodb/adodb-xmlschema.inc.php");

$log =& LoggerManager::getLogger('VT');
$logsqltm =& LoggerManager::getLogger('SQLTIME');

// Callback class useful to convert PreparedStatement Question Marks to SQL value
// See function convertPS2Sql in PearDatabase below
class PreparedQMark2SqlValue {
	// Constructor
	function PreparedQMark2SqlValue($vals){
        $this->ctr = 0;
        $this->vals = $vals;
    }
    function call($matches){
		$this->ctr++;
		return $matches[1].$this->vals[$this->ctr-1];
    }
}

class PearDatabase{
    var $database = null;
    var $dieOnError = true;
    var $dbType = null;
    var $dbHostName = null;
    var $dbName = null;
    var $dbOptions = null;
    var $userName=null;
    var $userPassword=null;
    var $query_time = 0;
    var $log = null;
    var $lastmysqlrow = -1;
    var $enableSQLlog = false;

	// If you want to avoid executing PreparedStatement, set this to true
	// PreparedStatement will be converted to normal SQL statement for execution
	var $avoidPreparedSql = false;
	
    function isMySQL() { return dbType=='mysql'; }
    function isOracle() { return dbType=='oci8'; }
    
    function println($msg)
    {
	require_once('include/logging.php');
	$log1 =& LoggerManager::getLogger('VT');
	if(is_array($msg))
	{
	    $log1->info("PearDatabse ->".print_r($msg,true));
	}
	else
	{
	    $log1->info("PearDatabase ->".$msg);
	}
	return $msg;
    }

    function setDieOnError($value){	
	$this->dieOnError = $value;
    }
    
    function setDatabaseType($type){
	$this->dbType = $type;
    }
    
    function setUserName($name){
	$this->userName = $name;
    }
    
    function setOption($name, $value){
	if(isset($this->dbOptions))
	    $this->dbOptions[$name] = $value;
	if(isset($this->database))
	    $this->database->setOption($name, $value);
    }	
    
    function setUserPassword($pass){
	$this->userPassword = $pass;	
    }
    
    function setDatabaseName($db){
	$this->dbName = $db;	
    }
    
    function setDatabaseHost($host){
	$this->dbHostName = $host;	
    }
    
    function getDataSourceName(){
	return 	$this->dbType. "://".$this->userName.":".$this->userPassword."@". $this->dbHostName . "/". $this->dbName;
    }

    function startTransaction()
    {
	$this->checkConnection();
	$this->println("TRANS Started");
	$this->database->StartTrans();
    }

    function completeTransaction()
    {		
	if($this->database->HasFailedTrans()) 
	    $this->println("TRANS  Rolled Back");
	else
	    $this->println("TRANS  Commited");
	
	$this->database->CompleteTrans();
	$this->println("TRANS  Completed");
    }

    function checkError($msg='', $dieOnError=false)
    {
/*
 *	if($this->database->ErrorNo())
 *	{
 *	    if($this->dieOnError || $dieOnError)
 *	    {
 *		$this->println("ADODB error ".$this->database->ErrorNo());	
 *		die ($msg."ADODB error ".$this->database->ErrorNo());
 *	    } else {
 *		$this->log->error("MySQL error ".mysql_errno().": ".mysql_error());
 *	    }
 *	    return true;
 *	}
 */
	
	if($this->dieOnError || $dieOnError)
	{
	    $this->println("ADODB error ".$msg."->[".$this->database->ErrorNo()."]".$this->database->ErrorMsg());	
	    die ($msg."ADODB error ".$msg."->".$this->database->ErrorMsg());
	}
	else
	{
	    $this->println("ADODB error ".$msg."->[".$this->database->ErrorNo()."]".$this->database->ErrorMsg());
	}
	return false;
    }

    function change_key_case($arr)
    {
	return is_array($arr)?array_change_key_case($arr):$arr;
    }

    var $req_flist;	
    
    /**
    * @return void
    * @desc checks if a connection exists if it does not it closes the connection
     * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
     * All Rights Reserved..
     * Contributor(s): ______________________________________..
    */
    function checkConnection(){
	global $log;

	if(!isset($this->database))
	{
	    $this->println("TRANS creating new connection");
/*
 *	    $flist=get_included_files();
 *	    foreach($flist as $key=>$value)
 *	    {
 *		if(!strstr($value,'\\modules') && !strstr($value,'\\data'))
 *		unset($flist[$key]);
 *	    }
 *	    $this->println($flist);
 */
	    $this->connect(false);
	}
	else
	{
	    //$this->println("checkconnect using old connection");
	}
    }

    function query($sql, $dieOnError=false, $msg='')
    {
	global $log;
	//$this->println("ADODB query ".$sql);		
	$log->debug('query being executed : '.$sql);
	$this->checkConnection();
	$result = & $this->database->Execute($sql);
	$this->lastmysqlrow = -1;
	if(!$result)$this->checkError($msg.' Query Failed:' . $sql . '::', $dieOnError);
	return $result;		
    }

	
	/**
	 * Covert PreparedStatement to SQL statement
	 */
	function convert2Sql($ps, $vals) {
		// TODO: Checks need to be added array out of bounds situations
		for($index = 0; $index < count($vals); $index++) {
			if(is_string($vals[$index])) {
				if($vals[$index] == '') {
					$vals[$index] = $this->database->Quote($vals[$index]);
				}
				else {
					$vals[$index] = "'".mysql_real_escape_string($vals[$index]). "'";
				}
			} else if($vals[$index] == null) {
				$vals[$index] = "NULL";
			}
		}
		$sql = preg_replace_callback("/((('[^']*')|(\"[^\"]*\")|[^?])+)(\?)/", array(new PreparedQMark2SqlValue($vals),"call"), $ps);
		return $sql;
	}

  	/* ADODB prepared statement Execution
   	* @param $sql -- Prepared sql statement
   	* @param $params -- Parameters for the prepared statement
   	* @param $dieOnError -- Set to true, when query execution fails
   	* @param $msg -- Error message on query execution failure
   	*/	
	function pquery($sql, $params, $dieOnError=false, $msg='') {		
		global $log;
		$log->debug('Prepared sql query being executed : '.$sql . ' -> [' . implode(",", $params) . ']');
		$this->checkConnection();
		
		global $logsqltm;

		$sql_start_time = microtime();
		$params = $this->flatten_array($params);
		
		if($this->avoidPreparedSql) {
			$sql = $this->convert2Sql($sql, $params);
			$result = &$this->database->Execute($sql);
		} else {
			$result = &$this->database->Execute($sql, $params);
		}
		$sql_end_time = microtime();

		// Specifically for timing the SQL execution, you need to enable DEBUG in log4php.properties
		if($logsqltm->isDebugEnabled()){
			$sql_start_time = explode(" ", $sql_start_time);
			$sql_end_time = explode(" ", $sql_end_time);
			$sql_start_time = ((float)$sql_start_time[0] + (float)$sql_start_time[1]);
			$sql_end_time = ((float)$sql_end_time[0] + (float)$sql_end_time[1]);
			
			$logsqltm->debug("SQL: " . $sql);
			if($params != null && count($params) > 0) $logsqltm->debug("PARAMS: [" . implode(",", $params) . "]");
			$logsqltm->debug("EXEC: " . ($sql_end_time - $sql_start_time) ." micros [START=$sql_start_time, END=$sql_end_time]");
			$logsqltm->debug("");
		}
		
		$this->lastmysqlrow = -1;
		if(!$result)$this->checkError($msg.' Query Failed:' . $sql . '::', $dieOnError);
			return $result;	
	}

	/**
	 * Flatten the composite array into single value.
	 * Example:
	 * $input = array(10, 20, array(30, 40), array('key1' => '50', 'key2'=>array(60), 70));
	 * returns array(10, 20, 30, 40, 50, 60, 70);
	 */
	function flatten_array($input, $output=null) {
		if($input == null) return null;
		if($output == null) $output = array();
		foreach($input as $value) {
			if(is_array($value)) {	
				$output = $this->flatten_array($value, $output);
			} else {	
				array_push($output, $value);
			}
		}
		return $output;
	}
	
    function getEmptyBlob()
    {
	//if(dbType=="oci8") return 'empty_blob()';
	//else return 'null';
	return 'null';
    }

    function updateBlob($tablename, $colname, $id, $data)	
    {
	$this->println("updateBlob t=".$tablename." c=".$colname." id=".$id);
	$this->checkConnection();
	$result = $this->database->UpdateBlob($tablename, $colname, $data, $id);
	$this->println("updateBlob t=".$tablename." c=".$colname." id=".$id." status=".$result);
	return $result;
    }

    function updateBlobFile($tablename, $colname, $id, $filename)	
    {
	$this->println("updateBlobFile t=".$tablename." c=".$colname." id=".$id." f=".$filename);
	$this->checkConnection();
	$result = $this->database->UpdateBlobFile($tablename, $colname, $filename, $id);
	$this->println("updateBlobFile t=".$tablename." c=".$colname." id=".$id." f=".$filename." status=".$result);
	return $result;
    }

    function limitQuery($sql,$start,$count, $dieOnError=false, $msg='')
    {
	global $log;
	//$this->println("ADODB limitQuery sql=".$sql." st=".$start." co=".$count);
	$log->debug(' limitQuery sql = '.$sql .' st = '.$start .' co = '.$count);
	$this->checkConnection();
	$result =& $this->database->SelectLimit($sql,$count,$start);
	if(!$result) $this->checkError($msg.' Limit Query Failed:' . $sql . '::', $dieOnError);
	return $result;		
    }
    
    function getOne($sql, $dieOnError=false, $msg='')
    {
	$this->println("ADODB getOne sql=".$sql);
	$this->checkConnection();
	$result =& $this->database->GetOne($sql);
	if(!$result) $this->checkError($msg.' Get one Query Failed:' . $sql . '::', $dieOnError);
	return $result;		
    }

    function getFieldsArray(&$result)
    {
	//$this->println("ADODB getFieldsArray");
	$field_array = array();
	if(! isset($result) || empty($result))
	{
	    return 0;
	}

	$i = 0;
	$n = $result->FieldCount();
	while ($i < $n) 
	{
	    $meta = $result->FetchField($i);
	    if (!$meta) 
	    {
		return 0;
	    }
	    array_push($field_array,$meta->name);
	    $i++;
	}

	//$this->println($field_array);
	return $field_array;			
    }
    
    function getRowCount(&$result){
	global $log;
	//$this->println("ADODB getRowCount");
	if(isset($result) && !empty($result))
	    $rows= $result->RecordCount();			
	//$this->println("ADODB getRowCount rows=".$rows);	
	//$log->debug('getRowCount rows= '.$rows);
	return $rows;			
    }

    /* ADODB newly added. replacement for mysql_num_rows */
    function num_rows(&$result)
    {
	return $this->getRowCount($result);
    }

    /* ADODB newly added. replacement form mysql_num_fields */
    function num_fields(&$result)
    {
	return $result->FieldCount();
    }

    /* ADODB newly added. replacement for mysql_fetch_array() */
    function fetch_array(&$result)
    {
	if($result->EOF)
	{
	    //$this->println("ADODB fetch_array return null");
	    return NULL;
	}
	$arr = $result->FetchRow();
        if(is_array($arr))
                $arr = array_map('to_html', $arr);
        return $this->change_key_case($arr);	
	//return $this->change_key_case($result->FetchRow());
    }

    ## adds new functions to the PearDatabase class to come around the whole
    ## broken query_result() idea
    ## Code-Contribution given by weigelt@metux.de - Starts
    function run_query_record_html($query)
    {
	    if (!is_array($rec = $this->run_query_record($query)))
	    //         throw new Exception("no rec: $query");
	    	return $rec;
	    foreach ($rec as $walk => $cur)
	    	$r[$walk] = to_html($cur);
	    return $r;
    }

    function sql_quote($data)
    {
	if (is_array($data))
	{
		switch($data{'type'})
		{
			case 'text':
			case 'numeric':
			case 'integer':
			case 'oid':
				return $this->quote($data{'value'});
				break;
			case 'timestamp':
				return $this->formatDate($data{'value'});
				break;
			default:
				throw new Exception("unhandled type: ".serialize($cur));
		}
	} else
		return $this->quote($data);
    }

    function sql_insert_data($table, $data)
    {
	if (!$table)
		throw new Exception("missing table name");
	if (!is_array($data))
		throw new Exception("data must be an array");
	if (!count($table))
	    	throw new Exception("no data given");

	$sql_fields = '';
	$sql_data = '';
	foreach($data as $walk => $cur)
	{
		$sql_fields .= ($sql_fields?',':'').$walk;
		$sql_data   .= ($sql_data?',':'').$this->sql_quote($cur);
	}

	return 'INSERT INTO '.$table.' ('.$sql_fields.') VALUES ('.$sql_data.')';
    }

    function run_insert_data($table,$data)
    {
	    $query = $this->sql_insert_data($table,$data);
	    $res = $this->query($query);
	    $this->query("commit;");
    }

    function run_query_record($query)
    {
	    $result = $this->query($query);
	    if (!$result)
	    	return;
	    //         throw new Exception("empty result !");
	    if (!is_object($result))
	    	throw new Exception("query \"$query\" failed: ".serialize($result));
	    $res = $result->FetchRow();
	    $rowdata = $this->change_key_case($res);
	    return $rowdata;
    }

    function run_query_allrecords($query)
    {
	    $result = $this->query($query);
	    $records = array();
	    $sz = $this->num_rows($result);
	    for ($i=0; $i<$sz; $i++)
		$records[$i] = $this->change_key_case($result->FetchRow());
	    return $records;
    }

    function run_query_field($query,$field='')
    {
	    $rowdata = $this->run_query_record($query);
	    if(isset($field) && $field != '')
	    	return $rowdata{$field};
	    else
	    	return array_shift($rowdata);
    }

    function run_query_list($query,$field)
    {
	    $records = $this->run_query_allrecords($query);
	    foreach($records as $walk => $cur)
		$list[] = $cur{$field};
    }

    function run_query_field_html($query,$field)
    {
	    return to_html($this->run_query_field($query,$field));
    }

    function result_get_next_record($result)
    {
	    return $this->change_key_case($result->FetchRow());
    }

    // create an IN expression from an array/list
    function sql_expr_datalist($a)
    {
	    if (!is_array($a))
	    	throw new Exception("not an array");
	    if (!count($a))
	    	throw new Exception("empty arrays not allowed");

	    foreach($a as $walk => $cur)
	    	$l .= ($l?',':'').$this->quote($cur);
	    return ' ( '.$l.' ) ';
    }

    // create an IN expression from an record list, take $field within each record
    function sql_expr_datalist_from_records($a,$field)
    {
	    if (!is_array($a))
	    	throw new Exception("not an array");
	    if (!$field)
	    	throw new Exception("missing field");
	    if (!count($a))
	    	throw new Exception("empty arrays not allowed");
	    
	    foreach($a as $walk => $cur)
	    	$l .= ($l?',':'').$this->quote($cur{$field});

	    return ' ( '.$l.' ) ';
    }

    function sql_concat($list)
    {
	    switch ($this->dbType)
	    {
		    case 'mysql':
			    return 'concat('.implode(',',$list).')';
		    case 'pgsql':
			    return '('.implode('||',$list).')';
		    default:
			    throw new Exception("unsupported dbtype \"".$this->dbType."\"");
	    }
    }
    ## Code-Contribution given by weigelt@metux.de - Ends

    /* ADODB newly added. replacement for mysql_result() */
    function query_result(&$result, $row, $col=0)
    {		
	//$this->println("ADODB query_result r=".$row." c=".$col);
	if (!is_object($result))
                throw new Exception("result is not an object");
	$result->Move($row);
	$rowdata = $this->change_key_case($result->FetchRow());
	//$this->println($rowdata);
	//Commented strip_selected_tags and added to_html function for HTML tags vulnerability
	//$coldata = strip_selected_tags($rowdata[$col],'script');
	if($col == 'fieldlabel')
		$coldata = $rowdata[$col];
	else
		$coldata = to_html($rowdata[$col]);
	//$this->println("ADODB query_result ". $coldata);
	return $coldata;
    }

    function getAffectedRowCount(&$result)
    {
	global $log;
	//$this->println("ADODB getAffectedRowCount");
	$log->debug('getAffectedRowCount');
	$rows =$this->database->Affected_Rows(); 
	//$this->println("ADODB getAffectedRowCount rows=".rows);
	$log->debug('getAffectedRowCount rows = '.$rows);
	return $rows;
    }

    function requireSingleResult($sql, $dieOnError=false,$msg='', $encode=true)
    {
	$result = $this->query($sql, $dieOnError, $msg);

	if($this->getRowCount($result ) == 1)				
	    return $result;
	$this->log->error('Rows Returned:'. $this->getRowCount($result) .' More than 1 row returned for '. $sql);
	return '';
    }
/* function which extends requireSingleResult api to execute prepared statment
 */

    function requirePsSingleResult($sql, $params, $dieOnError=false,$msg='', $encode=true)
    {
	$result = $this->pquery($sql, $params, $dieOnError, $msg);

	if($this->getRowCount($result ) == 1)				
	    return $result;
	$this->log->error('Rows Returned:'. $this->getRowCount($result) .' More than 1 row returned for '. $sql);
	return '';
    }   



    function fetchByAssoc(&$result, $rowNum = -1, $encode=true)
    {
	//$this->println("ADODB fetchByAssoc ".$rowNum." fetch mode=".$adb->database->$ADODB_FETCH_MODE);
	if($result->EOF)
	{
	    $this->println("ADODB fetchByAssoc return null");
	    return NULL;
	}
	if(isset($result) && $rowNum < 0)
	{			
	    $row = $this->change_key_case($result->GetRowAssoc(false));			
	    $result->MoveNext();			
	    //print_r($row);
	    //$this->println("ADODB fetchByAssoc r< 0 isarray r=".is_array($row)." r1=".is_array($row[1]));			
	    //$this->println($row);
	    if($encode&& is_array($row))
		return array_map('to_html', $row);
	    //$this->println("ADODB fetchByAssoc r< 0 not array r1=".$row[1]);			
	    return $row;			
	}

	//$this->println("ADODB fetchByAssoc after if ".$rowNum);	
	
	if($this->getRowCount($result) > $rowNum)
	{
	    $result->Move($rowNum);				
	}

	$this->lastmysqlrow = $rowNum; //srini - think about this
	$row = $this->change_key_case($result->GetRowAssoc(false));		
	$result->MoveNext();
	//print_r($row);		
	$this->println($row);
			
	if($encode&& is_array($row))
	    return array_map('to_html', $row);	
	return $row;
    }
    
    function getNextRow(&$result, $encode=true){
	global $log;

	//$this->println("ADODB getNextRow");
	$log->info('getNextRow');
	if(isset($result)){
	    $row = $this->change_key_case($result->FetchRow());
	    if($row && $encode&& is_array($row))
		return array_map('to_html', $row);	
	    return $row;
	}
	return null;
    }

    function fetch_row(&$result, $encode=true)
    {
	return $this->getNextRow($result);
    }

    function field_name(&$result, $col)
    {
	return $result->FetchField($col);
    }
    
    function getQueryTime(){
	return $this->query_time;	
    }

    function connect($dieOnError = false)
    {
	//$this->println("ADODB connect");
	global $dbconfigoption,$dbconfig;
	//$this->println("ADODB type=".$this->dbType." host=".$this->dbHostName." dbname=".$this->dbName." user=".$this->userName." password=".$this->userPassword);

	if(!isset($this->dbType))
	{
	    $this->println("ADODB Connect : DBType not specified");
	    return;
	}
	
	$this->database = ADONewConnection($this->dbType);
	//$this->database->debug = true;
	
	$this->database->PConnect($this->dbHostName, $this->userName, $this->userPassword, $this->dbName);
	$this->database->LogSQL($this->enableSQLlog);
	//$this->database->SetFetchMode(ADODB_FETCH_ASSOC); 
	//$this->println("ADODB type=".$this->dbType." host=".$this->dbHostName." dbname=".$this->dbName." user=".$this->userName." password=".$this->userPassword);		
    }

    function PearDatabase($dbtype='',$host='',$dbname='',$username='',$passwd='')
    {
	//$this->println("PearDatabase");
	global $currentModule;
	$this->log =& LoggerManager::getLogger('PearDatabase_'. $currentModule);
	$this->resetSettings($dbtype,$host,$dbname,$username,$passwd);
    }

    function resetSettings($dbtype,$host,$dbname,$username,$passwd)
    {
	global $dbconfig, $dbconfigoption;
	    
	if($host == '')
	{
	    $this->disconnect();
	    $this->setDatabaseType($dbconfig['db_type']);
	    $this->setUserName($dbconfig['db_username']);
	    $this->setUserPassword($dbconfig['db_password']);
	    $this->setDatabaseHost( $dbconfig['db_hostname']);
	    $this->setDatabaseName($dbconfig['db_name']);
	    $this->dbOptions = $dbconfigoption;
	    if($dbconfig['log_sql'])
	    $this->enableSQLlog = ($dbconfig['log_sql'] == true);
	    //$this->println("resetSettings log=".$this->enableSQLlog);
	    //$this->println($dbconfig);
	    /*if($this->dbType != "mysql"){
		require_once( 'DB.php' );	
	    }*/
	}
	else
	{
	    $this->disconnect();
	    $this->setDatabaseType($dbtype);
	    $this->setDatabaseName($dbname);
	    $this->setUserName($username);
	    $this->setUserPassword($passwd);
	    $this->setDatabaseHost( $host);
	}
    }

    function quote($string){
	return $this->database->qstr($string);	
    }

    function disconnect() {
	$this->println("ADODB disconnect");
	if(isset($this->database)){
	    if($this->dbType == "mysql"){
		mysql_close($this->database);
	    } else {
		$this->database->disconnect();
	    }
	    unset($this->database);
	}
    }

    function setDebug($value)
    {
	$this->database->debug = $value;
    }


    // ADODB newly added methods
    function createTables($schemaFile, $dbHostName=false, $userName=false, $userPassword=false, $dbName=false, $dbType=false)
    {
	$this->println("ADODB createTables ".$schemaFile);
	if($dbHostName!=false) $this->dbHostName=$dbHostName;
	if($userName!=false) $this->userName=$userPassword;
	if($userPassword!=false) $this->userPassword=$userPassword;
	if($dbName!=false) $this->dbName=$dbName;
	if($dbType!=false) $this->dbType=$dbType;		

	//$db = ADONewConnection($this->dbType);
	$this->checkConnection();
	$db = $this->database;
	//$db->debug = true;

	//$this->println("ADODB createTables connect status=".$db->Connect($this->dbHostName, $this->userName, $this->userPassword, $this->dbName));
	$schema = new adoSchema( $db );
	//Debug Adodb XML Schema
	$sehema->XMLS_DEBUG = TRUE;
	//Debug Adodb
	$sehema->debug = true;
	$sql = $schema->ParseSchema( $schemaFile );

	$this->println("--------------Starting the table creation------------------");
	//$this->println($sql);

	//integer ExecuteSchema ([array $sqlArray = NULL], [boolean $continueOnErr = NULL])
	$result = $schema->ExecuteSchema( $sql, true );
	if($result)
	print $db->errorMsg();
	// needs to return in a decent way
	$this->println("ADODB createTables ".$schemaFile." status=".$result);
	return $result;
    }

    function createTable($tablename, $flds)
    {
	$this->println("ADODB createTable table=".$tablename." flds=".$flds);
	$this->checkConnection();
	//$dict = NewDataDictionary(ADONewConnection($this->dbType));
	$dict = NewDataDictionary($this->database);
	$sqlarray = $dict->CreateTableSQL($tablename, $flds);
	$result = $dict->ExecuteSQLArray($sqlarray);
	$this->println("ADODB createTable table=".$tablename." flds=".$flds." status=".$result);
	return $result;
    }

    function alterTable($tablename, $flds, $oper)
    {
	$this->println("ADODB alterTableTable table=".$tablename." flds=".$flds." oper=".$oper);
	//$dict = NewDataDictionary(ADONewConnection($this->dbType));
	$this->checkConnection();
	$dict = NewDataDictionary($this->database);
	//$sqlarray = new Array(); 
	
	if($oper == 'Add_Column')
	{
	    $sqlarray = $dict->AddColumnSQL($tablename, $flds);
	}
	else if($oper == 'Delete_Column')
	{
	    $sqlarray = $dict->DropColumnSQL($tablename, $flds);
	}

	$this->println("sqlarray");
	$this->println($sqlarray);

	$result = $dict->ExecuteSQLArray($sqlarray);

	$this->println("ADODB alterTableTable table=".$tablename." flds=".$flds." oper=".$oper." status=".$result);
	return $result;

    }

    function getColumnNames($tablename)
    {
	$this->println("ADODB getColumnNames table=".$tablename);	
	$this->checkConnection();
	$adoflds = $this->database->MetaColumns($tablename);
	//$colNames = new Array();
	$i=0;
	foreach($adoflds as $fld)
	{
	    $colNames[$i] = $fld->name;
	    $i++;
	}
	return $colNames;	
    }

    function formatString($tablename,$fldname, $str)
    {
	//$this->println("ADODB formatString table=".$tablename." fldname=".$fldname." str=".$str);
	$this->checkConnection();
	$adoflds = $this->database->MetaColumns($tablename);
	
	foreach ( $adoflds as $fld )
	{
	    //$this->println("ADODB formatString adofld =".$fld->name);
	    if(strcasecmp($fld->name,$fldname)==0)
	    {
		//$this->println("ADODB formatString fldname=".$fldname." fldtype =".$fld->type);

		$fldtype =strtoupper($fld->type); 	
		if(strcmp($fldtype,'CHAR')==0 || strcmp($fldtype,'VARCHAR') == 0 || strcmp($fldtype,'VARCHAR2') == 0 || strcmp($fldtype,'LONGTEXT')==0 || strcmp($fldtype,'TEXT')==0)
		{
		    //$this->println("ADODB return else normal");
		    return $this->database->Quote($str);
		}
		else if(strcmp($fldtype,'DATE') ==0 || strcmp($fldtype,'TIMESTAMP')==0)
		{
		    return $this->formatDate($str);
		}
		else
		{				
		    return $str;
		}
	    }
	}
	$this->println("format String Illegal field name ".$fldname);
	return $str;
    }

    function formatDate($datetime, $strip_quotes=false)
    {
	$this->checkConnection();
	//$db = ADONewConnection($this->dbType);
	$db = &$this->database;
	$date = $db->DBTimeStamp($datetime);
	//if($db->dbType=='mysql') return $this->quote($date);
	/* Asha: Stripping single quotes to use the date as parameter for Prepared statement */
	if($strip_quotes == true) {
		return trim($date, "'");
	}
	return $date;
    }

    function getDBDateString($datecolname)
    {
	$this->checkConnection();
	$db = &$this->database;
	$datestr = $db->SQLDate("Y-m-d, H:i:s" ,$datecolname);
	return $datestr;	
    }

    function getUniqueID($seqname)
    {
	$this->checkConnection();
	return $this->database->GenID($seqname."_seq",1);
    }
    function get_tables()
    {
	$this->checkConnection();
	$result = & $this->database->MetaTables('TABLES');
	$this->println($result);
	return $result;		
    }
} /* End of class */

$adb = new PearDatabase();
$adb->connect();
//$adb->database->setFetchMode(ADODB_FETCH_NUM);

?>
