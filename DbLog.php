<?php
/**
 *	@description: Error Logging class for Dal.
 *	@author:
 *	@date:3/06/2014
 *	
 **/
 class DbLog 
 {
	 protected static $handler; 
	 
	 protected static $fileName = 'DbLogging.log';
	 
	 
	 public function log($message)
	 {
		 try
		 {
			 if(!is_string($message))
			 {
				 throw new Exception('Invalid parameter message, must be string.');
			 }
			
			$time = date("m-d-Y H:i:s"); 
            $script = $_SERVER['PHP_SELF']; 
            $log  = "\n"; 
            $log .= $time ."\n". $message ."\n"; 
            $log .= "Client script: ". str_replace ( "\\", "/", $script) ."\n"; 
            $log .= "#########################################################################################################\n"; 
            $log .= "\n"; 
			
			if(fwrite(self::getHandler(), $log) < 0)
			{
				throw new Exception('Cannot write into logfile');
			}
		 }
		 catch (Exception $e)
		 {
			 throw $e;
		 }
	 }
	 
	 public function logError(DbException $e)
	 {
		 try
		 {
			$message  = " ERROR!\n"; 
            $message .= "Exception code: ". $e->getCode() ."\n"; 
            $message .= "Exception message: ". $e->getMessage() ."\n"; 
            $message .= "Thrown by: '". $e->getFile() ."'"; 
            $message .= " on line: ". $e->getLine() ."\n"; 
            $message .= "Stack trace: ". $e->getTraceAsString() ."\n"; 
            self::log($message); 
			
		 }
		 catch (Exceptions $e)
		 {
			 throw new Exception($e->getMessage(), $e->getCode());
		 }
	 }
	/** 
     * write log into logfile 
     * 
     */ 
    protected function getHandler() { 
        if (is_resource(self::$handler)) { 
            return self::$handler; 
        } 
        else { 
            self::$handler = fopen(self::getFilename(), "a"); 
        } 
        if (!is_resource(self::$handler)) { 
            throw new Exception("Cannot open logfile."); 
        } 
        return self::$handler; 
    } 
	 /** 
    * getter for a filename 
    * 
    */ 
	public function getFilename() { 
        $currentFileFull = str_replace("\\", "/", __FILE__); 
        # An standard function dirname() is deranged by filenames with more than one doth. 
        $currentFile = $currentFileFull; 
        if (substr($currentFile, 0, 1) == "/") { 
            $currentFile = substr($currentFile, 1); 
           } 
        while($pos = strpos($currentFile, "/")) { 
            $currentFile = substr($currentFile, $pos + 1); 
        } 
        $currentDir = substr($currentFileFull, 0, strpos($currentFileFull, $currentFile)); 
        return  $currentDir . self::$fileName; 
    } 
 }
?>