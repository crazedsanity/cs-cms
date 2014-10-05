<?php

namespace crazedsanity;

use crazedsanity\Template;
use crazedsanity\Message;

class MessageQueue extends baseAbstract {
    
    const SESSIONKEY = 'messages';
    private $_messages = array();
    
    
	//----------------------------------------------------------------------------
    public function __construct($load=false) {
        if($load===true) {
            $this->load();
        }
        else {
            $this->init();
        }
    }
	//----------------------------------------------------------------------------
    
    
    
	//----------------------------------------------------------------------------
    protected function init() {
        foreach(Message::$validTypes as $k) {
            if(!isset($this->_messages[$k]) || !is_array($this->_messages[$k])) {
                $this->_messages[$k] = array();
            }
        }
    }
	//----------------------------------------------------------------------------
    
    
    
	//----------------------------------------------------------------------------
    public function hasFatalError() {
        $retval = false;
        if(count($this->_messages[Message::TYPE_FATAL])) {
            $retval = true;
        }
        return $retval;
    }
	//----------------------------------------------------------------------------
    
    
    
	//----------------------------------------------------------------------------
    public function load() {
        if(isset($_SESSION[self::SESSIONKEY]) && is_array($_SESSION[self::SESSIONKEY])) {
            //$this->_messages = $_SESSION[self::SESSIONKEY];
            foreach($_SESSION[self::SESSIONKEY] as $type=>$list) {
                foreach($list as $num=>$obj) {
                    if(is_object($obj)) {
                        $this->add($obj);
                    }
                    else {
                        throw new \LogicException("Session data contains non-object message");
                    }
                }
            }
        }
        $this->init();
    }
	//----------------------------------------------------------------------------
    
    
    
	//----------------------------------------------------------------------------
    public function save() {
        $_SESSION[self::SESSIONKEY] = $this->_messages;
    }
	//----------------------------------------------------------------------------
    
    
    
	//----------------------------------------------------------------------------
    public function __destruct() {
        $this->save();
    }
	//----------------------------------------------------------------------------

	
	
	//----------------------------------------------------------------------------
	public function add(Message $msg) {
	    $this->_messages[$msg->type] = $msg;
	}
	//----------------------------------------------------------------------------
	
	
	
	//----------------------------------------------------------------------------
	public function render(Template $tmpl) {
	    $output = "";
	    
	    foreach($this->_messages as $type => $subData) {
	        foreach($subData as $num => $obj) {
	            $x = clone $tmpl;
	            $output .= $obj->render($x);
	        }
	    }
	    $this->_messages = array();
	    $this->init();
	    
	    return $output;
	}
	//----------------------------------------------------------------------------
}