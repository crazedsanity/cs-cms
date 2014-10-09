<?php

namespace crazedsanity;

abstract class Controller extends baseAbstract {
	
	protected $parameters = array();
	
	
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
	
}
