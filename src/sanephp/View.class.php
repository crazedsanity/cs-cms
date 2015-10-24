<?php

namespace crazedsanity\sanephp;

use crazedsanity\Template;

class View implements iTemplate {
	
	protected $template;
	
	public function __construct(Template $tmpl) {
		$this->template = $tmpl;
	}
	
	
	
	public function addVar($name,$value) {
		$this->template->addVar($name, $value, false);
	}
	
	
	
	public function add(Template $obj) {
		$this->template->add($obj);
	}
	
	
	
	public function render($stripUndefinedVars=true) {
		return $this->template->render($stripUndefinedVars);
	}
	
	
	public function parseBlockRow($name, array $listOfVarToValue, $useTemplateVar = null) {
		return $this->template->parseBlockRow($name, $listOfVarToValue, $useTemplateVar);
	}
}
