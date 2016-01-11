<?php
/**
 *	@description: Exception class for Dal
 *	@auhtor: 
 *	@date:3/06/2014
 *
 *
 ***/
 
 class DbException extends exception
 {
	 public function __construct($message = NULL, $code=0)
	 {
		 
		 parent::__construct($message, $code);
		 DbLog::logError($this);
	 }
 }
?>