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
	private $_origin;
	private $_dir;
	private $recursionDepth=10;



	//-------------------------------------------------------------------------
	/**
	 * @param $file         Template file to use for contents (can be null)
	 * @param null $name    Name to use for this template
	 */
	public function __construct($file, $name=null) {
		$this->_origin = $file;
		if(!is_null($name)) {
			$this->_name = $name;
		}
		if(!is_null($file)) {
			if (file_exists($file)) {
				try {
					if (is_null($name)) {
						$bits = explode('/', $file);
						$this->_name = preg_replace('~\.tmpl~', '', array_pop($bits));
					}
					$this->_contents = file_get_contents($file);
					$this->_dir = dirname($file);
				} catch (Exception $ex) {
					throw new \InvalidArgumentException;
				}
			}
			else {
				throw new \InvalidArgumentException("file does not exist (". $file .")");
			}
		}

	}
	//-------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	/**
	 * @param $newValue     How many times to recurse (default=10)
	 */
	public function set_recursionDepth($newValue) {
		if(is_numeric($newValue) && $newValue > 0) {
			$this->recursionDepth = $newValue;
		}
		else {
			throw new \InvalidArgumentException();
		}
	}
	//-------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	/**
	 * @param $name                 Internal var to retrieve
	 * @return array|mixed|string   Value of internal var
	 */
	public function __get($name) {
		switch($name) {
			case 'name':
				return $this->_name;
		
			case 'templates':
				return $this->_templates;

			case 'contents':
				return $this->_contents;

			case 'dir':
				return $this->_dir;

			case 'origin':
				return $this->_origin;
			
			default:
				throw new \InvalidArgumentException;
		}
	}
	//-------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	/**
	 * @param $value        Set internal contents to this value.
	 */
	public function setContents($value) {
		$this->_contents = $value;
	}
	//-------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	/**
	 * @param Template $template    Template object to add
	 * @param bool $render          If the template should be rendered (default=true)
	 */
	public function add(\crazedsanity\Template $template, $render=true) {
		foreach($template->templates as $name=>$content) {
			$this->_templates[$name] = $content;
		}

		if($render === true) {
			$this->_templates[$template->name] = $template->render();
		}
		else {
			$this->_templates[$template->name] = $template->contents;
		}
	}
	//-------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	/**
	 * @param $name             Name of template var
	 * @param null $value       Value (contents) of template
	 * @param bool $render      See $render argument for add()
	 */
	public function addVar($name, $value=null, $render=true) {
		$x = new Template(null, $name);
		$x->setContents($value);
		$this->add($x, $render);
	}
	//-------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	/**
	 * @param bool $stripUndefinedVars      Removes undefined template vars
	 * @return mixed|string                 Rendered template
	 */
	public function render($stripUndefinedVars=true) {
		$numLoops = 0;
		$out = $this->_contents;

		try {
			while (preg_match_all('~~', $out, $tags) && $numLoops < $this->recursionDepth) {
				$out = cs_global::mini_parser($out, $this->_templates, '{', '}');
				$numLoops++;
			}
		}
		catch(\Exception $ex) {
			Throw new \LogicException("This should never happen... ". $ex->getMessage());
		}

		if($stripUndefinedVars === true) {
			$out = preg_replace('/\{.\S+?\}/', '', $out);
		}

		return $out;
	}
	//-------------------------------------------------------------------------
}
