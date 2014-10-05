<?php

namespace crazedsanity;

use crazedsanity\Template;
//use crazedsanity\Message;

class GenericPage extends baseAbstract {
	public $mainTemplate;					//the default layout of the site
	public $printOnFinish=true;
	
	protected $tmplDir;
	protected $siteRoot;
	
	protected $allowInvalidUrls=NULL;
	
	protected $_hasFatalError = false;
	
	protected $_messages;
	
	//----------------------------------------------------------------------------
	/**
	 * The constructor.
	 */
	public function __construct(Template $mainTemplate=null, $loadMessages=true) {
		parent::__construct();

		if(is_object($mainTemplate)) {
			$this->mainTemplate = $mainTemplate;
		}
		else {
			$this->mainTemplate = new Template(null, "main");
		}
		$this->_messages = new MessageQueue($loadMessages);
	}//end initialize_locals()
	//----------------------------------------------------------------------------
	


	//----------------------------------------------------------------------------
	/**
	 * Remove all data from the special template var "content" (or optionally another ver).
	 * 
	 * @param $section		(str,optional) defines what template var to wip-out.
	 * @return (NULL)
	 */
	public function clear_content($section="content"){
		$this->mainTemplate->addVar($section, "");
	}//end clear_content()
	//----------------------------------------------------------------------------



	//----------------------------------------------------------------------------
	/**
	 * Change the content of a template to the given data.
	 * 
	 * @param $htmlString			(str) data to use.
	 * @param $section				(str,optional) define a different section.
	 * 
	 * @return (NULL)
	 */
	public function change_content($htmlString,$section="content"){
		$this->mainTemplate->addVar($section, $htmlString, false);
	}//end change_content()
	//----------------------------------------------------------------------------



	//----------------------------------------------------------------------------
	/**
	 * Adds a template file (with the given handle) to be parsed.
	 * 
	 * TODO: check if $fileName exists before blindly trying to parse it.
	 */
	public function add_template_file($handleName, $fileName){
		$this->mainTemplate->add(new Template($fileName, $handleName), false);
	}//end add_template_file()
	//----------------------------------------------------------------------------



	//----------------------------------------------------------------------------
	/**
	 * Adds a value for a template placeholder.
	 */
	public function add_template_var($varName, $varValue){
		$this->mainTemplate->addVar($varName, $varValue, false);
	}//end add_template_var();
	//----------------------------------------------------------------------------



	//----------------------------------------------------------------------------
	/**
	 * Processes all template vars & files, etc, to produce the final page.  NOTE: it is a wise idea
	 * for most pages to have this called *last*.
	 * 
	 * @param $stripUndefVars		(bool,optional) Remove all undefined template vars.
	 * 
	 * @return (str)				Final, parsed page.
	 */
	public function render_page($stripUndefVars=true) {
		//Show any available messages.
		$errorBox = $this->process_set_message();
		
		if($this->_hasFatalError) {
			$this->change_content($errorBox);
		}
		else {
			$this->add_template_var("error_msg", $errorBox);
		}


		$out = $this->mainTemplate->render($stripUndefVars);
		
		return $out;
		
	}//end of render_page()
	//----------------------------------------------------------------------------



	//----------------------------------------------------------------------------
	public function print_page($stripUndefVars=true) {
		print $this->render_page($stripUndefVars);
	}
	//----------------------------------------------------------------------------
	
	
	
	//----------------------------------------------------------------------------
	/**
	 * Handles a message that was set into the session.
	 */
	public function process_set_message() {
		$retval = null;
		if(isset($_SESSION['messages']) && is_array($_SESSION['messages'])) {
			$retval = "";
			if(isset($_SESSION['messages']['fatal']) && is_array($_SESSION['messages']['fatal']) && count($_SESSION['messages']['fatal']) > 0) {
				$this->_hasFatalError = count($_SESSION['messages']['fatal']);
			}
			
			$processOrder = array('fatal', 'error', 'status', 'notice');
			
			$lastType = null;
			foreach($processOrder as $type) {
				$lastType = $type;
				if(isset($_SESSION['messages'][$type])) {
					foreach($_SESSION['messages'][$type] as $k=>$v) {
						$retval .= $this->_process_single_session_message($type, $v);
					}
					unset($_SESSION['messages'][$type]);
				}
			}
			if(count($_SESSION['messages']) > 0) {
				foreach($_SESSION['messages'] as $k=>$subData) {
					foreach($subData as $n=>$msg) {
						$retval .= $this->_process_single_session_message($lastType, $msg);
					}
					unset($_SESSION['messages'][$k]);
				}
			}
		}
		
		return $retval;
	}//end of process_set_message()
	//----------------------------------------------------------------------------
	
	
	
	//----------------------------------------------------------------------------
	public function _process_single_session_message($type, array $data) {
		$tmpl = new Template($this->mainTemplate->dir .'/system/message_box.tmpl');
		$data['messageType'] = strtolower($type);
		$data['type'] = $type;
		foreach($data as $key => $value) {
			$tmpl->addVar($key, $value);
		}

		return $tmpl->render();
	}
	//----------------------------------------------------------------------------
	
	
	
	//----------------------------------------------------------------------------
	/**
	 * Creates an array in the session, used by the templating system as a way to
	 * get messages generated by the code into the page, without having to add 
	 * special templates & such each time.
	 * 
	 * @param $title			(str) the title of the message.
	 * @param $message			(str) text beneath the title.
	 * @param $linkURL			(str,optional) URL for the link below the message.
	 * @param $type				(str) notice/status/error/fatal message, indicating
	 * 								it's importance.  Generally, fatal messages 
	 * 								cause only the message to be shown.
	 * @param $linkText			(str,optional) text that the link wraps.
	 */
	public static function set_message($title=NULL, $message=NULL, $linkURL=NULL, $type=NULL, $linkText=NULL) {
		if(is_null($type) || !strlen($type)) {
			$type = 'notice';
		}
		
		if(!array_key_exists('messages', $_SESSION)) {
			$_SESSION['messages'] = array();
		}
		if(!array_key_exists($type, $_SESSION['messages'])) {
			$_SESSION['messages'][$type] = array();
		}
		
		$setThis = array(
			"title"		=> $title,
			"message"	=> $message,
			"type"		=> $type,
		);
		
		if(strlen($linkURL)) {
			if(!strlen($linkText) || is_null($linkText)) {
				$linkText = "Link";
			}
			$setThis['redirect'] = '<a href="'. $linkURL .'">'. $linkText .'</a>';
		}
		
		$_SESSION['messages'][$type][] = $setThis;
	} // end of set_message()
	//----------------------------------------------------------------------------
	
	
	
	
	//----------------------------------------------------------------------------
	/**
	 * Add a message to the queue.
	 * 
	 * @param type $title		Title of the message.
	 * @param type $message		Contents of the message.
	 * @param type $type		Type, default is "notice" []
	 * @param type $linkUrl
	 * @param type $linkText
	 */
	public static function add_message($title, $message, $type=selfMSGTYPE_NOTICE, $linkUrl=null, $linkText="Link") {
		self::set_message($title, $message, $linkUrl, $type, $linkText);
	}// end of add_message()
	//----------------------------------------------------------------------------
	
	
	
	//----------------------------------------------------------------------------
	/**
	 * 
	 * @param array $array	Key=>value pairs for use with self::set_message()
	 */
	public static function set_message_wrapper(array $array) {
		$title = null;
		$message = null;
		$linkUrl = null;
		$type = null;
		$linkText = null;
		
		
		foreach($array as $k=>$v) {
			switch(strtolower($k)) {
				case 'title':
					$title = $v;
					break;
				
				case 'message':
					$message = $v;
					break;
				
				case 'linkurl':
					$linkUrl = $v;
					break;
				
				case 'type':
					$type = $v;
					break;
				
				case 'linktext':
					$linkText = $v;
					break;
			}
		}
		
		self::set_message($title, $message, $linkUrl, $type, $linkText);
	}//end set_message_wrapper()
	//----------------------------------------------------------------------------
	
	
	
	//----------------------------------------------------------------------------
	/**
	 * Performs redirection, provided it is allowed.
	 */
	function conditional_header($url, $exitAfter=TRUE,$isPermRedir=FALSE) {
		cs_global::conditional_header($url, $exitAfter, $isPermRedir);
	}//end conditional_header()
	//----------------------------------------------------------------------------
	
	
	
	//----------------------------------------------------------------------------
	/**
	 * This setting is used by ContentSystem, and not directly by this class.
	 * @param null $newSetting
	 * @return bool|null
	 */
	public function allow_invalid_urls($newSetting=NULL) {
		if(!is_null($newSetting) && is_bool($newSetting)) {
			$this->allowInvalidUrls = $newSetting;
		}
		return($this->allowInvalidUrls);
	}//end allow_invalid_urls()
	//----------------------------------------------------------------------------
	
	
	
	//----------------------------------------------------------------------------
	public function return_printed_page($stripUndefVars=true) {
		return $this->render_page($stripUndefVars);
	}//end return_printed_page()
	//----------------------------------------------------------------------------
	
	
	
	//----------------------------------------------------------------------------
	/**
	 * Magic PHP method for retrieving the values of private/protected vars.
	 */
	public function __get($var) {
		return(@$this->$var);
	}//end __get()
	//----------------------------------------------------------------------------
	
	
	
	//----------------------------------------------------------------------------
	/**
	 * Magic PHP method for changing the values of private/protected vars (or 
	 * creating new ones).
	 */
	public function __set($var, $val) {
		
		//TODO: set some restrictions on internal vars...
		$this->$var = $val;
	}//end __set()
	//----------------------------------------------------------------------------

}//end cs_genericPage{}
