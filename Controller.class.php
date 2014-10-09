<?php

namespace crazedsanity;

abstract class Controller extends baseAbstract {
	
	protected $parameters = array();
	
	/**
	 *
	 * @var bool		Load all views based on the URL.
	 */
	protected $loadViewsByUrl=true;
	
	
	public function __construct(array $parameters) {
		$this->parameters = $parameters;
	}
	
	
	/**
	 * Display index page (action) for this controller.
	 * 
	 * @return crazedsanity\View
	 */
	abstract public function index();
	
	
	/**
	 * Called after page load.  For things like logging, after business and 
	 * display logic has been handled.
	 * 
	 * @return void
	 */
	abstract public function _after();
	
	
	
	/**
	 * Determines if all views 
	 * 
	 * @return bool		Returns value of loadViewsByUrl
	 */
	public function get__loadViewsByUrl() {
		return $this->loadViewsByUrl;
	}
	
	
	
	public function set__loadViewsByUrl($newVal) {
		$this->loadViewsByUrl = $newVal;
		return $this->loadViewsByUrl;
	}
	
}
