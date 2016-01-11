<?php
/**
 * 	File:Dbconfig.php - Define database credentials here
 *		MSSQL wrapper class to access Database.It can establish connections to Mssql. 
 *		Then it can be used to execute queries 
 * 		and retrieve results set data in arrays.
 *	@author:Bharath Kumar Gyadasu
 *	@date:
 *
 ***/
 //@ini_set("display_errors", 1);
 //Including the Global.inc.php constants to avoid conflicts
  /*php.ini settings for the mssql*/
  ini_set('max_input_time','14400');
  ini_set('default_socket_timeout','14400');
  ini_set("max_execution_time", "14400");
  ini_set('memory_limit', '1024M');
  ini_set('mssql.connect_timeout', -1); 
  ini_set('mssql.timeout', 14400);   
  
  define("DBMS_HOST", "localhost");
  define("DBMS_USERNAME", "root");
  define("DBMS_PASSWORD", "5P4nkm3");
  
 
  /*//developement server credentials
  define('DB_HOST', '10.64.2.30');
  define('DB_DATABASE', 'results');
  define('DB_USER', 'rnetv3');
  define('DB_PASSWORD', '5P4nkm3');
  define('DB_TYPE', 'MSSQL');
  define('DB_PREFIX', '');
  define('DB_PORT', '');*/
  
  define("MSSQL_PORT", "");
  define("MSSQL_TYPE", "");
  
  if($_SERVER['HTTP_HOST']=="dev.resultsuniversitytraining.com" || $_SERVER['HTTP_HOST']=="rnetv3.dev.resultstel.com" || $_SERVER['HTTP_HOST']=="rnetv3dev.resultstel.com")
	{
	  define("MSSQL_HOST", "10.64.2.234");		
	  define("MSSQL_DB","SCRIPT_LOOKUP");
	  define("MSSQL_USERNAME", "WCPayByPhone");	
	  define("MSSQL_PASSWORD", "WCPbp@123");
			
	  if(substr($_SERVER['REQUEST_URI'],1,7)=="Reports")
	  { 
		  //"Its the reports folder, so redirect to the live server"
		  define("MSSQL_HOST", "10.64.2.134");
		  //$sql_host = "10.64.2.134";	
		  //$db = mssql_connect($sql_host, "rnetv3", "5P4nkm3");
	  }
	} 
	else
	{ // it's the live server
		define("MSSQL_HOST", "10.64.2.134");
		//$sql_host = "10.64.2.134";
		define("MSSQL_DB","results");
		define("MSSQL_USERNAME", "rnetv3");	
		define("MSSQL_PASSWORD", "5P4nkm3");
	}
	
	
  //to use Static LIVE connection
  define("MSSQL_LIVE_HOST", "10.64.2.134");
  //$sql_host = "10.64.2.134";
  define("MSSQL_LIVE_DB","results");
  define("MSSQL_LIVE_USERNAME", "rnetv3");	
  define("MSSQL_LIVE_PASSWORD", "5P4nkm3");
  
  
  define("MSSQL_ARC_HOST", "10.64.2.32");
  define("MSSQL_ARC_DB","results");
  define("MSSQL_ARC_USERNAME", "rnetv3");	
  define("MSSQL_ARC_PASSWORD", "5P4nkm3");
  
  
  //Some pre defined constants 
  define("CSI_MASTER_HOST", "10.64.2.105");
  define("CSI_MASTER_USERNAME", "xm_event_user");
  define("CSI_MASTER_PASSWORD", "XM_event_user");
  define("CSI_HOST2", "10.64.2.105");
  define("CSI_USERNAME2", "resultsnet");
  define("CSI_PASSWORD2", "R3sul+sNet");
  define("NP_MASTER_HOST", "SQL2");
  define("NP_MASTER_USERNAME", "sa");
  define("NP_MASTER_PASSWORD", "\$QLS3rv3r");
  define("ST_MASTER_HOST", "Saturn");
  define("ST_MASTER_USERNAME", "sa");
  define("ST_MASTER_PASSWORD", "uldsup");
  define("PR_MASTER_HOST", "SQL2");
  define("PR_MASTER_USERNAME", "sa");
  define("PR_MASTER_PASSWORD", "\$QLS3rv3r");
  define("CLASS_PATH", "http://".$_SERVER["SERVER_NAME"]."/Include/class/");
  //define("IMAGE_PATH", "http://".$_SERVER["SERVER_NAME"]."/ResultsNet/images/");
  define("IMAGE_PATH", "http://".$_SERVER["SERVER_NAME"]."/Include/images/");
  define("DOCS_PATH", "http://".$_SERVER["SERVER_NAME"]."/ResultsNet/docs/");
  define("JAVA_PATH", "https://".$_SERVER["SERVER_NAME"]."/Include/javascript/");
  define("CSS_PATH", "https://".$_SERVER["SERVER_NAME"]."/Include/CSS");
  define("STYLE_PATH", "/var/www/localhost/htdocs/Include/stylesheet/");
  define("INCLUDE_PATH", "https://".$_SERVER["SERVER_NAME"]."/Include/");
  define("RESULTS_EMAIL", "resultsnet@resultstel.com");
  define("RESULTS_PASS", "resultsnet");
  define("MAIL_SERVER_1", "mail-cluster.resultstel.com");
  define("MAIL_SERVER_2", "mail-cluster.resultstel.com");
  define("FTP_USERNAME", "root");
  define("FTP_PASSWORD", "5P4nkm3");
  define("JAVASCRIPT_TIMER", "");
  
  
  //define("FTP_BONUSES_SERVER", "ftp.resultstel.com");
  define("FTP_BONUSES_SERVER", "66.104.153.196");
  define("FTP_BONUSES_USERNAME", "sitebonuses");
  define("FTP_BONUSES_PASSWORD", "sitebonus3s");
  
  // define new FTP Server , User & Password
  
  //define("NEW_FTP_SERVER", "10.64.2.193");
  define("NEW_FTP_SERVER", "10.64.2.106");
  define("NEW_FTP_USERNAME", "srv-ftp-web");
  define("NEW_FTP_PASSWORD", "Xee0ieDu");
  
  
  /* Define FTP server details*/
  define("FTP_SERVER", "10.32.2.121");
  define("FTP_SERVER2", "ftp.resultstel.com");
  define("FTP_USERNAME", "quality");
  define("FTP_PASSWORD", "Qu4l1tY");
  
  
  //Delivarable
  define("DELIVERABLE_HOST", "10.64.2.134");
  define("DELIVERABLE_USERNAME", "deliverable-mgr");
  define("DELIVERABLE_PASSWORD", "Webservice123");
  
  // Exceptions class
  
  include_once($_SERVER['DOCUMENT_ROOT'] . '/wellcarePOC/lib/RDSData/DbException.php');
  //DbLog Classes
  include_once($_SERVER['DOCUMENT_ROOT'] . '/wellcarePOC/lib/RDSData/DbLog.php');
  //database access layer
  
  include_once($_SERVER['DOCUMENT_ROOT'] . '/wellcarePOC/lib/RDSData/RDSData.php'); 
  
  
  
?>