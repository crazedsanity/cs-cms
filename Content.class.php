<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace crazedsanity;

use crazedsanity\Route;
use crazedsanity\ToolBox;

/**
 * Description of Content
 *
 * @author danf
 */
class Content extends baseAbstract {
	
	protected $routes;
	protected $controller;
	
	private $urlBits = array();
	
	public function __construct($defaultRoute=null) {
		self::$routes = new Route($defaultRoute);
		
		$this->urlBits = $this->parseUrl($_SERVER['REQUEST_URI']);
		
		$controllerClass = __NAMESPACE__ .'\\Controller\\'. $this->urlBits['controller'];
		$this->controller = new $controllerClass();
		
		$this->routes = new Route($defaultRoute);
	}
	
	public function parseUrl($uri) {
		$cleaned = ToolBox::clean_url($uri);
		$bits = explode('/', $cleaned);
		
		//NOTE::: the class needs to be either included/required, or autoloaded.
		$retval = array(
			'controller'	=> array_shift($bits),
			'bits'			=> $bits,
		);
		
		
		return $retval;
	}
	
	
	public static function get__controller() {
		return $this->controller;
	}
	public static function get__routes() {
		return $this->routes;
	}
	public function get_urlBits() {
		return $this->urlBits;
	}
	
	
	
	public function finish() {
		
		
		//TODO: determine controller from path (e.g. "/content/index")
		
		//load the controller... should be something like 'crazedsanity\Controller\Content'
		$tClass = __NAMESPACE__ .'\\Controller\\'. self::$routes->getRoute($this->pathBits[0]);
		
		$controller = new $tClass($this->params);
	}
}
