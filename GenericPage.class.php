<?php

namespace crazedsanity;

use crazedsanity\Template;
//use crazedsanity\Message;

class GenericPage extends baseAbstract {
	public $mainTemplate;					//the Template{} that sets the site's default layout
	public $messageTemplate;				//Template{} to use for rendering messages
	public $printOnFinish=true;
	
	protected $tmplDir;
	protected $siteRoot;
	
	protected $allowInvalidUrls=NULL;
	
	static $messages;
	
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
		self::$messages = new MessageQueue($loadMessages);
	}//end initialize_locals()
	//----------------------------------------------------------------------------
	
	
	
	//----------------------------------------------------------------------------
	public function get_version() {
		$v = $this->GetVersionObject();
		return $v->get_version();
	}
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
		if(!file_exists($fileName)) {
			$tryFile = preg_replace('/^\/\//', '/', $this->tmplDir .'/'. $fileName);
			if(file_exists($tryFile)) {
				$fileName = $tryFile;
			}
			else {
				throw new \InvalidArgumentException("file (". $fileName .") does not exist");
			}
		}
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
//		ToolBox::debug_print(__METHOD__ .": RENDERED PAGE: ". htmlentities($this->render_page($stripUndefVars)),1);
		print $this->render_page($stripUndefVars);
	}
	//----------------------------------------------------------------------------
	
	
	
	//----------------------------------------------------------------------------
	/**
	 * Handles a message that was set into the session.
	 */
	public function process_set_message() {
		$tmpl = $this->messageTemplate;
		if(is_null($this->messageTemplate) || !is_object($this->messageTemplate)) {
			$tmpl = new Template($this->mainTemplate->dir .'/system/message.tmpl');
		}
		return self::$messages->render($tmpl);
	}//end of process_set_message()
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
		self::$messages->add(new Message($title, $message, $type, $linkURL, $linkText));
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
	public static function add_message(Message $obj) {
		self::$messages->add($obj);
	}// end of add_message()
	//----------------------------------------------------------------------------
	
	
	
	//----------------------------------------------------------------------------
	/**
	 * Performs redirection, provided it is allowed.
	 */
	function conditional_header($url, $exitAfter=TRUE,$isPermRedir=FALSE) {
		ToolBox::conditional_header($url, $exitAfter, $isPermRedir);
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
	
	
	
	//----------------------------------------------------------------------------
	public function rip_all_block_rows() {
		return;
	}
	//----------------------------------------------------------------------------

}//end cs_genericPage{}
