<?php

namespace crazedsanity\sanephp;

use \InvalidArgumentException;

class Route extends baseAbstract {
	
	protected $map = array();
	protected $aliasMap = array();
	
	protected $defaultController = null;
	
	public function __construct($defaultController=null) {
		$this->defaultController = $defaultController;
	}
	
	
	/**
	 * 
	 * @param type $path			Path of the route (URL, like "/")
	 * @param type $urlTemplate		Array with index=>defaults. For example, in M$ MVC, "{controller}/{action}/{id}" becomes: array("controller"=>"content","action"=>"index","id"=>null)
	 */
	public function addRoute($path, array $urlTemplate) {
		if(!isset($this->map[$path])) {
			$this->map[$path] = $urlTemplate;
		}
		else {
			throw new InvalidArgumentException("route '". $path ."' already exists");
		}
	}
	
	
	public function addAlias($path, $aliasPath) {
		if(isset($this->map[$path])) {
			if(!isset($this->aliasMap[$aliasPath])) {
				$this->aliasMap[$aliasPath] = $path;
			}
			else {
				throw new InvalidArgumentException("alias path (". $aliasPath .") already exists");
			}
		}
		else {
			throw new InvalidArgumentException("path (". $path .") already exists");
		}
	}
}
