<?php

namespace crazedsanity;

use crazedsanity\Template;

class Message extends baseAbstract {
    
    const MSGTYPE_NOTICE = "notice";
    const MSGTYPE_STATUS = "status";
    const MSGTYPE_ERROR  = "error";
    const MSGTYPE_FATAL  = "fatal";
    
    const SESSIONKEY = 'messages';
    private $_messages = array();
    
    
    // this is a sort of hack to make it easier to know the list of valid types.
    private $types = array(
        self::MSGTYPE_NOTICE,
        self::MSGTYPE_STATUS,
        self::MSGTYPE_ERROR,
        self::MSGTYPE_FATAL
    );
    
    
    
	//----------------------------------------------------------------------------
    public function __construct($load=false) {
        if($load===true) {
            $this->load();
        }
    }
	//----------------------------------------------------------------------------
    
    
    
	//----------------------------------------------------------------------------
    protected function init() {
        foreach($this->types as $k) {
            if(!isset($this->_messages[$k]) || !is_array($this->_messages[$k])) {
                $this->_messages[$k] = array();
            }
        }
    }
	//----------------------------------------------------------------------------
    
    
    
	//----------------------------------------------------------------------------
    public function hasFatalError() {
        $retval = false;
        if(count($this->_messages[self::MSGTYPE_FATAL])) {
            $retval = true;
        }
        return $retval;
    }
	//----------------------------------------------------------------------------
    
    
    
	//----------------------------------------------------------------------------
    public function load() {
        if(isset($_SESSION[self::SESSIONKEY]) && is_array($_SESSION[self::SESSIONKEY])) {
            $this->_messages = $_SESSION[self::SESSIONKEY];
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
	public function render(Template $tmpl) {
	    $output = "";
	    foreach($this->_messages as $type=>$data) {
	        $x = clone $tmpl;
	        foreach($data as $k=>$v) {
	            $x->addVar($k, $v);
	        }
	        $output .= $x->render();
	    }
	    
	    return $output;
	}
	//----------------------------------------------------------------------------
}