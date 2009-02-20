<?php

require_once(dirname(__FILE__) .'/abstract/csb_blog.abstract.class.php');

class csb_blog extends csb_blogAbstract {
	
	/** Internal name of blog (looks like a permalink) */
	protected $blogName;
	
	/** Displayable name of blog */
	protected $blogDisplayName;
	
	/** Numeric ID of blog */
	protected $blogId=false;
	
	/** Location of blog */
	protected $blogLocation;
	
	//-------------------------------------------------------------------------
	/**
	 * The constructor.
	 * 
	 * @param $blogName		(str) name of blog (NOT the display name)
	 * @param $dbType		(str) Type of database (pgsql/mysql/sqlite)
	 * @param $dbParams		(array) connection options for database
	 * 
	 * @return exception	throws an exception on error.
	 */
	public function __construct($blogName, array $dbParams=null) {
		
		//TODO: put these in the constructor args, or require CONSTANTS.
		parent::__construct($dbParams);
		
		
		if(!isset($blogName) || !strlen($blogName)) {
			throw new exception(__METHOD__ .": invalid blog name (". $blogName .")");
		}
		
		$this->blogName = $blogName;
		if(!defined('CSBLOG_SETUP_PENDING')) {
			//proceed normally...
			$this->initialize_locals($blogName);
		}
		
	}//end __construct()
	//-------------------------------------------------------------------------
	
	
	
}// end blog{}
?>
