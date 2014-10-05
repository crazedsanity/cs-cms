<?php

namespace crazedsanity;

use crazedsanity\Template;

class Message {
    
    const TYPE_NOTICE = "notice";
    const TYPE_STATUS = "status";
    const TYPE_ERROR  = "error";
    const TYPE_FATAL  = "fatal";
    
    
    // this is a sort of hack to make it easier to know the list of valid types.
    public static $validTypes = array(
        self::TYPE_NOTICE,
        self::TYPE_STATUS,
        self::TYPE_ERROR,
        self::TYPE_FATAL
    );
    
    
    private $title;
    private $message;
	private $type;
    private $url;
    private $linkText;


	//----------------------------------------------------------------------------
	/**
	 * 
	 * @param type $title
	 * @param type $message
	 * @param type $type
	 * @param type $linkUrl
	 * @param type $linkText
	 * @throws \InvalidArgumentException
	 */
    public function __construct($title, $message, $type=self::TYPE_NOTICE, $linkUrl=null, $linkText=null) {
        if(!is_null($title) && strlen($title) >2) {
            $this->title = $title;
        }
        else {
            throw new \InvalidArgumentException("invalid title");
        }
        
        if(!is_null($message) && strlen($message) > 5) {
            $this->message = $message;
        }
        else {
            throw new \InvalidArgumentException("invalid message length");
        }
        
        if(!is_null($type) && in_array($type, self::$validTypes)) {
            $this->type = $type;
        }
        else {
            throw new \InvalidArgumentException("invalid type");
        }
        
        if(!is_null($linkUrl) && strlen($linkUrl) > 0 && !is_null($linkText) && strlen($linkText) > 0) {
            $this->url = $linkUrl;
            $this->linkText = $linkText;
        }
    }
	//----------------------------------------------------------------------------



	//----------------------------------------------------------------------------
    public function getContents() {
        $retval = array(
            'title'     => $this->title,
            'message'   => $this->message,
            'url'       => $this->url,
            'linkText'  => $this->linkText,
        );
        return $retval;
    }
	//----------------------------------------------------------------------------



	//----------------------------------------------------------------------------
    public function __get($name) {
        return $this->$name;
    }
	//----------------------------------------------------------------------------



	//----------------------------------------------------------------------------
    public function render(Template $obj) {
        $obj->addVar('title', $this->title);
        $obj->addVar('message', $this->message);
        $obj->addVar('type', $this->type);
        $obj->addVar('url', $this->url);
        $obj->addVar('linkText', $this->linkText);
//cs_global::debug_print(__METHOD__ .": TEMPLATE::: ". cs_global::debug_print($obj,0),1);
//exit;
        return $obj->render();
    }
	//----------------------------------------------------------------------------
}