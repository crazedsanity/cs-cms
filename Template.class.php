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
	
	
	
	public function __construct($name, $file) {
		if(!is_null($file) && file_exists($file)) {
			try {
				$this->name = $name;
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
	
	
	public function render() {
		$numLoops = 0;
		$out = $this->_contents;
		$out = str_replace(array_keys($this->_templates), array_values($this->_templates), $this->_contents, $numLoops);
	}
}
