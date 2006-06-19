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

require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');
global $current_user;
$vtigerpath = $_SERVER['REQUEST_URI'];
$vtigerpath = str_replace("/index.php?module=uploads&action=add2db", "", $vtigerpath);

$crmid = $_REQUEST['return_id'];

	// Arbitrary File Upload Vulnerability fix - Philip
	$binFile = $_FILES['filename']['name'];

	$ext_pos = strrpos($binFile, ".");

	$ext = substr($binFile, $ext_pos + 1);

	if (in_array($ext, $upload_badext))
	{
		$binFile .= ".txt";
	}

	$_FILES["filename"]["name"] = $binFile;
	// Vulnerability fix ends

	//decide the file path where we should upload the file in the server
	$upload_filepath = decideFilePath();

	$current_id = $adb->getUniqueID("vtiger_crmentity");
	
	if(move_uploaded_file($_FILES["filename"]["tmp_name"],$upload_filepath.$current_id."_".$_FILES["filename"]["name"])) 
	{
		$filename = basename($binFile);
		$filetype= $_FILES['filename']['type'];
		$filesize = $_FILES['filename']['size'];

		if($filesize != 0)	
		{
			$desc = $_REQUEST['txtDescription'];
			$description = addslashes($desc);
			$date_var = date('YmdHis');

			$query = "insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime) values('";
			$query .= $current_id."','".$current_user->id."','".$current_user->id."','".$_REQUEST['return_module'].' Attachment'."','".$description."','".$date_var."')";
			$result = $adb->query($query);

			$sql = "insert into vtiger_attachments values(";
			$sql .= $current_id.",'".$filename."','".$description."','".$filetype."','".$upload_filepath."')";
			$result = $adb->query($sql);


			$sql1 = "insert into vtiger_seattachmentsrel values('";
			$sql1 .= $crmid."','".$current_id."')";
			$result = $adb->query($sql1);

			echo '<script>window.opener.location.href = window.opener.location.href;self.close();</script>';
		}
		else
		{
			$errormessage = "<font color='red'><B>Error Message<ul>
				<li><font color='red'>Invalid file OR</font>
				<li><font color='red'>File has no data</font>
				</ul></B></font> <br>" ;
			echo $errormessage;
			include "upload.php";
		}			
	} 
	else 
	{
		$errorCode =  $_FILES['binFile']['error'];

		if($errorCode == 4)
		{
			$errormessage = "<B><font color='red'>Kindly give a valid file for upload!</font></B> <br>" ;
			echo $errormessage;
			include "upload.php";
		}
		else if($errorCode == 2)
		{
			$errormessage = "<B><font color='red'>Sorry, the uploaded file exceeds the maximum filesize limit. Please try a file smaller than 1000000 bytes</font></B> <br>";
			echo $errormessage;
			include "upload.php";
			//echo $errorCode;
		}
		else if($errorCode == 3 || $errorcode == '')
		{
			echo "<b><font color='red'>Problems in file upload. Please try again!</font></b><br>";
			include "upload.php";
		}

	}

?>
