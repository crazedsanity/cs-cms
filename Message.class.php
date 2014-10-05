<?php

namespace crazedsanity;

use crazedsanity\Template;

class Message {
    
    const TYPE_NOTICE = "notice";
    const TYPE_STATUS = "status";
    const TYPE_ERROR  = "error";
    const TYPE_FATAL  = "fatal";
    
    
    // this is a sort of hack to make it easier to know the list of valid types.
    private $_validTypes = array(
        self::TYPE_NOTICE,
        self::TYPE_STATUS,
        self::TYPE_ERROR,
        self::TYPE_FATAL
    );
    
    
    private $_title;
    private $_message;
    private $_url;
    private $_linkText;
    
    public function __construct($title, $message, $type=self::TYPE_NOTICE, $linkUrl=null, $linkText=null) {
        if(!is_null($title) && strlen($title) >2) {
            $this->_title = $title;
        }
        else {
            throw new \InvalidArgumentException("invalid title");
        }
        
        if(!is_null($message) && strlen($message) > 5) {
            $this->_message = $message;
        }
        else {
            throw new \InvalidArgumentException("invalid message length");
        }
        
        if(!is_null($type) && in_array($type, $this->_validTypes)) {
            $this->_type = $type;
        }
        else {
            throw new \InvalidArgumentException("invalid type");
        }
        
        if(!is_null($linkUrl) && strlen($linkUrl) > 0 && !is_null($linkText) && strlen($linkText) > 0) {
            $this->_url = $linkUrl;
            $this->_linkText = $linkText;
        }
    }
    
    
    public function getContents() {
        $retval = array(
            'title'     => $this->_title,
            'message'   => $this->_message,
            'url'       => $this->_url,
            'linkText'  => $this->_linkText,
        );
        return $retval;
    }
    
    
    
    public function __get($name) {
        $propName = preg_replace('~^_~', '', $name);
        return $this->$name;
    }
    
    
    
    public function render(Template $obj) {
        $obj->addVar('title', $this->_title);
        $obj->addVar('message', $this->_message);
        $obj->addVar('type', $this->_type);
        $obj->addVar('url', $this->_url);
        $obj->addVar('linkText', $this->_linkText);
        return $obj->render();
    }
}