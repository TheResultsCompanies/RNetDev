<?php
/**
 *	File:RDSData.php - Database access layer page used to establish a connection to database.
 *
 *	@description: This page execute queries and retrive results set data in arrays.
 *				Modify this page and add additional functionalities as per the requirement.	
 *
 *	@author: Bhararth Kumar Gyadasu
 *	@date:03/05/2014
 ****/
 
//echo 'Before: '.DB_HOST;exit;qw
//include_once($_SERVER['DOCUMENT_ROOT'] . '/wellcarePOC/class1/authenticate.inc.php');


 class RDSData 
 {
	 /**
	  *declare class varibles
	  ***/
	  protected $dbHost = MSSQL_HOST;
	  protected $dbUser = MSSQL_USERNAME;
	  protected $dbPassword = MSSQL_PASSWORD;
	  protected $dbPort = MSSQL_PORT;
	  protected $dbType = MSSQL_TYPE;
	  public $dbDatabase = MSSQL_DB;
	  protected $activeTransaction = false;
	  protected $persistent = false;
	  protected $autoCommit;
	  public $dbLink;
	  private $rs;
	  private $dbField = array();
	  
	  public $host;
	  public $user;
	  public $password;
	  public $database;
	  public $type;
	  public $port;	  
	  public $logErrors;	  
	  public $UserDetails;

	  /**
	   * class constructor
	   **/
	   
	   public function __construct($host='', $user='', $password='', $database='', $type='', $port='', $persistent = false)
	   {
		   global $Me;
				   		
		   if($host) $this->dbHost = $host;
   		   if($user) $this->dbUser = $user;
		   if($password) $this->dbPassword = $password;
		   if($database) $this->dbDatabase = $database;
		   if($type) $this->dbType = $type;
		   if($port) $this->dbPort = $port;
		   $this->persistent = $persistent;
		   $this->UserDetails = $Me;
		  			try
			{
				$this->connect();
			}
			catch (Exception $e)
			{
				throw $e;
			}
			
			return $this->dbLink;
		   
	   }
	   
	   
	   /**
	    *	@description:Bellow function establishes the connections to DB
		*/
		
		public function connect()
		{
			if(!is_resource($this->dbLink))
			{	
				if($this->persistent)
				{
					$this->dbLink = @mssql_pconnect( $this->dbHost, $this->dbUser, $this->dbPassword);
				}
				else
				{
					$this->dbLink = @mssql_connect( $this->dbHost, $this->dbUser, $this->dbPassword);
				}
				@mssql_select_db($this->dbDatabase, $this->dbLink);						
				//throw new Exception('Successfully Connected to DB');
				try
				{
					if(!is_resource($this->dbLink))
					{
						throw new DbException('Cant connect to database Mssql.\nhost = '.$this->dbHost);
					}
				}
				catch (DbException $e)
				{
					echo $e->getMessage();
				}
			}
		}
		
		/**
		 *	@description:This function used to execute the Query
		 *	@param string $query
		 **/
		public function execute($sqlQuery)
		{
			$sqlQuery = str_replace(array("\'", '\"') ,"''",$sqlQuery);
			
			DbLog::log(__METHOD__." Query:".$sqlQuery." res link:".$this->dbLink);
			if (!is_string($sqlQuery)) 
			{ 
            	throw new DbException("Illegal parameter query. Must be string."); 
	        }
			
    	    try 
			{
				$rs = mssql_query($sqlQuery, $this->dbLink);
			
            	if (!$rs) 
				{ 				
                	$this->throwMssqlException("SQL query caused Error. Query: ". $sqlQuery ."Error: ".mssql_get_last_message()); 
            	} 
        	} 
        	catch (Exception $e) 
			{ 
            	echo  $e->getMessage(); 
        	} 

       	 	return $rs; 
			
		}
		
		/**
		 *	@description:
		 *	@param 
		 *
		 **/
		public function getNumRows($result)
		{
			if (!is_resource($result)) 
			{ 
            	throw new DbException("Illegal parameter result. Must be valid result resource."); 
        	} 
        	else 
			{ 
				DbLog::log(__METHOD__."result:".$result);
            	return mssql_num_rows($result); 
        	}	 
			
		}
		
	/** 
     * Throws Exception with Mssql error infos 
     * @return void 
     */ 
    protected function throwMssqlException($addToMessage = "") 
	{ 
	
        if (is_string($addToMessage)) 
		{ 
            $message = $addToMessage ."\n". @mssql_get_last_message(); 
        } 
        else 
		{ 
            $message = @mssql_get_last_message(); 
        } 
        throw new DbException($message); 
    }
	
	
	/**
	 *This function move the internal result pointer to next result 
	 *@return array 	 
	 ***/
	
	public function nextResult($rs)
	{
		$recordSet = array();
		if (!is_resource($this->dbLink)) 
			{ 
            	throw new DbException("Unable to move to next recordset.  Not connected."); 
        	}
		if(!is_resource($rs))
			{
				throw new DbException("Unable to move to next recordset.  No open recordset available.");
			}
		try
		{
			do
			{
				while($row = mssql_fetch_row($rs))
				{
					$recordSet[] = $row;
				}
			}
			while (mssql_next_result($rs));
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
		return $recordSet;
	}
	
	/**
	 *This function returns the number of fields in result
	 **/
	 
	 public function getFieldCount($rs)
	 {
		 if (!is_resource($this->dbLink)) 
			{ 
            	throw new DbException("Unable to move to next recordset.  Not connected."); 
        	}
		 if(!is_resource($rs))
			{
				throw new DbException("Unable to move to next recordset.  No open recordset available.");
			}
			
		try
		{
			
			$fieldCnt = mssql_num_fields($rs);
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
		
		return $fieldCnt;
		 
	 }
	 
	 /**
	  *This function returns the column names
	  **/
	  public function getColNames($rs)
	  {
		  if(!is_resource($rs))
		  {
			  throw new DbException('Illegal parameter result. Must be valid result resource.');
		  }
		  if(!$numFields = @mssql_num_fields($rs))
		  {
			  throw new DbException('No Column in result.');
		  }
		  for($i =0; $i< $numFields; $i++)
		  {
			  if(!$colName = @mssql_field_name($rs, $i))
			  {
				  throw new DbException('Column names reading Error.');
			  }
			  $colNames[$i] = $colName;
		  }
		  return $colNames;
	  }
	  
	  /**
	   *This function retunrs results array
	   *@returns array
	   */
	   public function bindingInToArray($rs)
	   {
		  
		   $ArrayResults = array();
		   if(!is_resource($rs))
		   {
			   throw new DbException('Illegal parameter result. Must be valid result resource.');
		   }
		   
		   while($row = @mssql_fetch_assoc($rs))
			{
				$ArrayResults[] = $row; 
		
			}
			try 
			{			
			if(!empty($ArrayResults))
				{
					
					return $ArrayResults;
				}
			}
			catch (DbException $e)
			{
				throw new DbException('Problem when binding the data into an Array.');	
			}
		
	   }
	   
	   /**
	    *This function begins the SQL-Transaction
		*
		**/
		public function transactionStart($autoCommit)
		{
			//DbLog::log('Inside transactionStart');
			if (!is_bool($autoCommit)) 
			{ 
            	throw new DbException("Ilegal parameter autoCommit. Must be boolean."); 
        	} 
        	if (!$this->activeTransaction) 
			{ 
            try 
				{ 
					$this->execute("BEGIN TRANSACTION"); 
				} 
				catch (Exception $e) 
				{ 
					throw $e; 
				} 
				$this->autoCommit = $autoCommit; 
				$this->activeTransaction = true; 
        	} 
        	else 
			{ 
            throw new DbException("Multiple transactions are not supported."); 
        	}
		}
		
		/**
		 *This function Commits the transaction
		 *
		 **/
		public function transactionCommit()
		{
			if ($this->activeTransaction) 
			{ 
				try 
				{ 
					$this->execute("COMMIT TRANSACTION"); 
				} 
				catch (Exception $e) 
				{ 
					throw $e; 
				} 
            $this->activeTransaction = false; 
        	} 
        	else 
			{ 
				throw new DbException("No transaction active."); 
			} 
			
		}
		
		/**
		 *This function ROLLBACK's the transaction
		 *
		 *
		 **/
		 public function transactionRollback()
		 {
			 if ($this->activeTransaction) 
			 { 
            	try 
				{ 
                $this->execute("ROLLBACK TRANSACTION"); 
            	} 
            	catch (Exception $e) 
				{ 
					throw $e; 
				} 
            	$this->activeTransaction = false; 
        	 } 
        	else 
			{ 
            	throw new DbException("No transaction active."); 
        	}
		 }
		 
		 /**
		  *	Returns the value of the field specified by name in the current open record set 
		  *	at the current open record position in the form of an object
		  *	@param resultset
		  *	@param fieldName
		  *	@returns array
		  **/
		 public function getObject($rs, $fieldName)
		 {
			if (!is_resource($this->dbLink)) 
			{ 
            	throw new DbException("Unable to move to next recordset.  Not connected."); 
        	}
		 	if(!is_resource($rs))
			{
				throw new DbException("Unable to move to next recordset.  No open recordset available.");
			}		
			
			try
			{
				$colNames = self::getColNames($rs);
				foreach($colNames as $key => $val)
				{					
					if($fieldName == $val)
					{
						$dbField = @mssql_fetch_field($rs, $key);
					}
				}
				return $dbField;
			}
			catch ( Exception $e)
			{
				throw $e;
			}
		 }
		 
		 /**
		  *	This function used to check the record set is NULL or Not
		  *	@param result set
		  *	@param coumn name
		  *	@return bool
		  **/
		 public function isNull($rs, $columnName)
		 {
			if (!is_resource($this->dbLink)) 
			{ 
            	throw new DbException("Unable to move to next recordset.  Not connected."); 
        	}
		 	if(!is_resource($rs))
			{
				throw new DbException("Unable to move to next recordset.  No open recordset available.");
			}
			
			try
			{
				$cnt = self::getNumRows($rs);
				for($i = 0; $i < $cnt; $i++)
				{
					$bool = mssql_result($rs, $i, $columnName);
				}
				return (!empty($bool)) ? TRUE : FALSE;
				
			}
			catch (Exception $e)
			{
				throw $e;
			}
		 }
		 
		 /**
		  *
		  *
		  *
		  *
		  *
		  ***/
		  
		  public function getInt($rs, $columnName)
		  {
			  $retVal = 0;
			if (!is_resource($this->dbLink)) 
			{ 
            	throw new DbException("Unable to move to next recordset.  Not connected."); 
        	}
		 	if(!is_resource($rs))
			{
				throw new DbException("Unable to move to next recordset.  No open recordset available.");
			}
			
			try
			{
				if ( !mssql_num_rows($rs))
				{
					throw new DbException('No records found');
				}
				else 
				{
					$cnt = self::getNumRows($rs);
					for($i = 0; $i < $cnt; $i++)
					{
						$bool = mssql_result($rs, $i, $columnName);					
						$retVal = intval($bool);
					}
					return $retVal;
				}
				
				
				
			}
			catch (Exception $e)
			{
				throw $e;
			}
			
		  }
		  /**
		   *
		   **/
		  public function getFloat($rs, $columnName)
		  {
			 $retVal = 0;
			if (!is_resource($this->dbLink)) 
			{ 
            	throw new DbException("Unable to move to next recordset.  Not connected."); 
        	}
		 	if(!is_resource($rs))
			{
				throw new DbException("Unable to move to next recordset.  No open recordset available.");
			}
			
			try
			{
				if ( !mssql_num_rows($rs))
				{
					throw new DbException('No records found');
				}
				else 
				{
					$cnt = self::getNumRows($rs);
					for($i = 0; $i < $cnt; $i++)
					{
						$bool = mssql_result($rs, $i, $columnName);					
						$retVal = (float)($bool);
					}
					return $retVal;
					
				}
			}
			catch (Exception $e)
			{
				throw $e;
			}
			
		  }
		  
		  public function getDateTime($rs, $columnName)
		  {
			
			if (!is_resource($this->dbLink)) 
			{ 
            	throw new DbException("Unable to move to next recordset.  Not connected."); 
        	}
		 	if(!is_resource($rs))
			{
				throw new DbException("Unable to move to next recordset.  No open recordset available.");
			}
			
			try
			{
				if ( !mssql_num_rows($rs))
				{
					throw new DbException('No records found');
				}
				else 
				{
					$cnt = self::getNumRows($rs);
					for($i = 0; $i < $cnt; $i++)
					{
						$val = mssql_result($rs, $i, $columnName);					
						$dRet = new DateTime($val);
					}
					return $dRet->format('Y-m-d');
					
				}
			}
			catch (Exception $e)
			{
				throw $e;
			}
			
		  }
		  
		  
		  
		 /**
		  * This function used to close the database connection
		  * @param Resource link
		  **/ 
		public function closeConn($link)
		{
			
			if(!is_resource($link))
			{
			   throw new DbException('Illegal parameter result. Must be valid result resource.');
			}
			//DbLog::log('Inside'. __CLASS__ .":". __METHOD__.":".$link);
			mssql_close($link);
			
		}
		
		/**
		 * This function used to free the result
		 * @param result soruce
		 **/
		public function freeResult($rs)
		{
			if(!is_resource($rs))
			{
			   throw new DbException('Illegal parameter result. Must be valid result resource.');
			}
			//DbLog::log('Inside'. __CLASS__ .":". __METHOD__.":".$link);
			mssql_free_result($rs);
			
		}
	   /**
	    * class destructor
		**/
		/*public function __destruct() 
		{ 
			DbLog::log('Inside __destruct'. __CLASS__ .":". __METHOD__);
        	if ($this->activeTransaction) 
			{ 
           	 	if ($this->autoCommit) 
				{ 
               	 	$this->transactionCommit(); 
           		} 
            	else 
				{ 
                	$this->transactionRollback(); 
            	} 
        	} 
       		@mssql_close($this->dbLink); 
        }*/
		
		
	public function errorPage($MessageInput , $sqlError = null , $layoutOff = null )
	{	
		$ErrorMessage = '';
		if($sqlError!='')
		{
			$ErrorMessage = '<p style="font-size:12px; color:#000; padding-left:15px;">Sql query error : </p>';
		}
		$ErrorMessage .= '<p style="font-size:12px; color:#F00; padding-left:15px; font-weight:bold;">';
		$ErrorMessage .= $MessageInput;
		$ErrorMessage .= '</p>';
		include_once($_SERVER["DOCUMENT_ROOT"].'/Include/ErrorMessagePage.php');
		// May need to change above Error message page.
				
	}

 }
?>