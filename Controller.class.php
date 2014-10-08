<?php

namespace crazedsanity;

abstract class Controller extends baseAbstract {
	
	protected $parameters = array();
	
	
	public function __construct(array $parameters) {
		$this->parameters = null;
	}
	
	
	abstract public function index();
	
	
	public function after() {
		
	}
	
}
