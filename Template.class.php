<?php

namespace crazedsanity;

/**
 * Description of template
 *
 * @author danf
 */
class Template {
	private $_contents;
	private $_name;
	private $_templates = array();
	
	
	
	public function __construct($file, $name=null) {
		if(!is_null($name)) {
			$this->_name = $name;
		}
		if(!is_null($file) && file_exists($file)) {
			try {
				if(is_null($name)) {
					$bits = explode('/', $file);
					$this->_name = preg_replace('~\.tmpl~', '', array_pop($bits));
				}
				$this->_contents = file_get_contents($file);
			} catch (Exception $ex) {
				throw new \InvalidArgumentException;
			}
		}

	}
	
	
	
	public function __get($name) {
		switch($name) {
			case 'name':
				return $this->_name;
		
			case 'templates':
				return $this->_templates;

			case 'contents':
				return $this->_contents;
			
			default:
				throw new \InvalidArgumentException;
		}
	}
	
	
	public function add(\crazedsanity\Template $template, $inherit=true) {
		if($inherit === true) {
			foreach($template->templates as $name=>$content) {
				$this->_templates[$name] = $content;
			}
		}
		
		$this->_templates[$template->name] = $template->render();
	}


	public function addVar($name, $value=null) {
		$this->_templates[$name] = $value;
	}
	
	
	public function render($stripUndefinedVars=true) {
		$numLoops = 0;
		$out = $this->_contents;
		$out = str_replace(array_keys($this->_templates), array_values($this->_templates), $this->_contents, $numLoops);
		if($stripUndefinedVars === true) {
			$out = preg_replace('/\{.\S+?\}/', '', $out);
		}
		return $out;
	}
}
