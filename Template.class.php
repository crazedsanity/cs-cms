<?php

namespace crazedsanity;

/**
 * Description of template
 *
 * @author danf
 */
class Template implements iTemplate {
	private $_contents;
	private $_name;
	private $templates = array();
	private $blockRows = array();
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
					$this->_contents = $this->get_block_row_defs(file_get_contents($file));
					$this->_dir = dirname($file);
				} catch (Exception $ex) {
					throw new \InvalidArgumentException;
				}
			}
			else {
				throw new \InvalidArgumentException("template file does not exist (". $file .")");
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
			
			case 'blockRows':
				return $this->blockRows;
			
			case 'templates':
				return $this->templates;

			case 'contents':
				return $this->_contents;

			case 'dir':
				return $this->_dir;

			case 'origin':
				return $this->_origin;
			
			default:
				throw new \InvalidArgumentException("no such internal var, '$name'");
		}
	}
	//-------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	/**
	 * @param $value        Set internal contents to this value.
	 */
	public function setContents($value) {
		$this->_contents = $this->get_block_row_defs($value);
	}
	//-------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	/**
	 * @param Template $template    Template object to add
	 * @param bool $render          If the template should be rendered (default=true)
	 * @throws \Exception           Problems with nesting of block rows
	 */
	public function add(\crazedsanity\Template $template, $render=true) {
		foreach($template->templates as $name=>$content) {
			$this->templates[$name] = $content;
			unset($template->templates[$name]);
		}
		
		foreach($template->blockRows as $name=>$content) {
			$this->blockRows[$name] = $content;
			unset($template->blockRows[$name]);
		}

		$template->_contents = $this->get_block_row_defs($template->_contents);

		if($render === true) {
			$this->templates[$template->name] = $template->render();
		}
		else {
			$this->templates[$template->name] = $template->contents;
		}
	}
	//-------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	/**
	 * @param $name             Name of template var
	 * @param null $value       Value (contents) of template
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

		//handle block rows.
		foreach($this->blockRows as $name=>$blockRow) {
			$parsed = $blockRow->render();
//\crazedsanity\ToolBox::debug_print(__METHOD__ .": parsing '". $name ."'... ::: ". htmlentities($parsed),1);
			$this->addVar('__BLOCKROW__'. $name, $parsed, false); // calling render wastes time.
		}

		$rendered = array();
		foreach($this->templates as $name=>$obj) {
			if(is_object($obj)) {
				$rendered[$name] = $obj->render();
			}
			else {
				$rendered[$name] = $obj;
			}
		}

		while (preg_match_all('~\{(\S{1,})\}~U', $out, $tags) && $numLoops < $this->recursionDepth) {
			$out = ToolBox::mini_parser($out, $rendered, '{', '}');
			$numLoops++;
		}

		if($stripUndefinedVars === true) {
			$out = preg_replace('/\{.\S+?\}/', '', $out);
		}

		return $out;
	}
	//-------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	/**
	 * @param $templateContents
	 * @return array
	 * @throws \Exception
	 */
	protected function get_block_row_defs($templateContents) {
		//cast $retArr as an array, so it's clean.
		$retArr = array();

		//looks good to me.  Run the regex...
		$flags = PREG_PATTERN_ORDER;
		$reg = "/<!-- BEGIN (\S{1,}) -->/";
		preg_match_all($reg, $templateContents, $beginArr, $flags);
		$beginArr = $beginArr[1];

		$endReg = "/<!-- END (\S{1,}) -->/";
		preg_match_all($endReg, $templateContents, $endArr, $flags);
		$endArr = $endArr[1];

		$numIncomplete = 0;
		$nesting = "";

		//create a part of the array that shows any orphaned "BEGIN" statements (no matching "END"
		// statement), and orphaned "END" statements (no matching "BEGIN" statements)
		// NOTE::: by doing this, should easily be able to tell if the block rows were defined
		// properly or not.
		if(count(array_diff($beginArr, $endArr)) > 0) {
			foreach($retArr['incomplete']['begin'] as $num=>$val) {
				$nesting = ToolBox::create_list($nesting, $val);
				$numIncomplete++;
			}
		}
		if(count(array_diff($endArr, $beginArr)) > 0) {
			foreach($retArr['incomplete']['end'] as $num=>$val) {
				$nesting = ToolBox::create_list($nesting, $val);
				$numIncomplete++;
			}
		}

		if($numIncomplete > 0) {
			throw new \Exception("invalidly nested block rows: ". $nesting);
		}

		//YAY!!! we've got valid data!!!
		//reverse the order of the array, so when the ordered array
		// is looped through, all block rows can be pulled.
		foreach(array_reverse($beginArr) as $k=>$v) {
			$tempRow = new Template(null, $v);
			$tempRow->setContents($this->setBlockRow($templateContents, $v));
			$this->blockRows[$v] = $tempRow;
		}

		return($templateContents);
	}
	//---------------------------------------------------------------------------------------------



	//---------------------------------------------------------------------------------------------
	private function setBlockRow(&$contents, $handle, $removeDefs=true) {
//		ToolBox::debug_print(__METHOD__ .": setting blockrow for handle=(". $handle .")",1);
		$name = $handle;

		$reg = "/<!-- BEGIN $handle -->(.+){0,}<!-- END $handle -->/sU";
		preg_match_all($reg, $contents, $m);
		if(!is_array($m) || !isset($m[0][0]) ||  !is_string($m[0][0])) {
			throw new \Exception("could not find ". $handle ." in '". $contents ."'");
		} else {

			if($removeDefs) {
				$openHandle = "<!-- BEGIN $handle -->";
				$endHandle  = "<!-- END $handle -->";
				$m[0][0] = str_replace($openHandle, "", $m[0][0]);
				$m[0][0] = str_replace($endHandle, "", $m[0][0]);
			}
			$contents = preg_replace($reg, "{__BLOCKROW__" . $name ."}", $contents);
		}
		return($m[0][0]);
	}
	//---------------------------------------------------------------------------------------------



	//---------------------------------------------------------------------------------------------
	/**
	 * @param $name                     Name of the existing block row to parse
	 * @param array $listOfVarToValue   Data to iterate through to create parsed rows.
	 * @param null $useTemplateVar      Parse into the given name instead of the default (__BLOCKROW__$name)
	 */
	public function parseBlockRow($name, array $listOfVarToValue, $useTemplateVar=null) {
		if(isset($this->blockRows[$name])) {
			if(is_null($useTemplateVar)) {
				$useTemplateVar = '__BLOCKROW__'. $name;
			}

			$final = "";
			foreach($listOfVarToValue as $row => $kvp) {
				if(is_array($kvp)) {
					$tmp = clone $this->blockRows[$name];
					foreach($kvp as $var=>$value) {
						$tmp->addVar($var, $value);
					}
					$final .= $tmp->render();
				}
				else {
					throw new \InvalidArgumentException("malformed key value pair in row '". $row ."'");
				}
			}
			unset($this->blockRows[$name]);
			$this->addVar($useTemplateVar, $final);
		}
		else {
			throw new \InvalidArgumentException("block row '". $name ."' does not exist... ". ToolBox::debug_print($this,0));
		}
	}
	//---------------------------------------------------------------------------------------------



	//---------------------------------------------------------------------------------------------
	public function __toString() {
		return $this->render();
	}
	//---------------------------------------------------------------------------------------------



	//---------------------------------------------------------------------------------------------
	public static function getTemplateVarDefinitions($fromContents) {
		$matches = array();
		preg_match_all('~\{(\S{1,})\}~U', $fromContents, $matches);

		$retval = array();

		foreach($matches[1] as $name) {
			if(!isset($retval[$name])) {
				$retval[$name] = 1;
			}
			else {
				$retval[$name]++;
			}
		}

		return $retval;
	}
	//---------------------------------------------------------------------------------------------
}
