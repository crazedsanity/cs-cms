<?php

use crazedsanity\Template;
use crazedsanity\Message;
use crazedsanity\MessageQueue;
use crazedsanity\ToolBox;

class TestOfMessageAndMessageQueue extends PHPUnit_Framework_TestCase {
    
    public function test_create() {
        $que = new MessageQueue();
		
		
		$que->add(new Message('first title', 'first message'));
		$que->add(new Message('second title', 'second message', Message::TYPE_FATAL));
		$que->add(new Message('third', 'a 3rd of them', Message::TYPE_ERROR, 'http://foo.bar/error', 'GO'));
	    
		$this->assertEquals(true, (bool)$que->hasFatalError(), "fatal error not detected");
		$this->assertEquals(1, $que->hasFatalError(), "invalid number of fatal messages");
		
		
		
		$this->assertEquals(1, $que->getCount(Message::TYPE_ERROR), "no error message found");
		$this->assertEquals(1, $que->getCount(Message::TYPE_FATAL), "no fatal message found");
		$this->assertEquals(1, $que->getCount(Message::TYPE_NOTICE), "could not find the default message (added without specifying type)");
		$this->assertEquals($que->getCount(Message::TYPE_NOTICE), $que->getCount(Message::DEFAULT_TYPE), "unexpected default type (". Message::DEFAULT_TYPE .")");
		$this->assertEquals(3, $que->getCount(null), "not all messages are in the queue...?");
		
		
		$out = $que->render(new Template(dirname(__FILE__) .'/files/templates/message.tmpl'));
		
		$this->assertTrue(strlen($out) > 0, "rendered message is blank");
		
		$this->assertEquals(3, preg_match_all('~TITLE: ~', $out), "could not find all titles... ". ToolBox::debug_print($out,0));
		$this->assertEquals(3, preg_match_all('~MESSAGE: ~', $out), "could not find all message bodies");
		$this->assertEquals(3, preg_match_all('~TYPE: ~', $out), "could not find all types");
		$this->assertEquals(3, preg_match_all('~LINKTEXT: ~', $out), "could not find all linkText fields");
		$this->assertEquals(3, preg_match_all('~URL: ~', $out), "could not find all url fields");
		
		$this->assertEquals(0, $que->getCount(null), "message queue wasn't cleaned after render");
    }
	
	
	public function test_save() {
		$_SESSION = array();
		$que = new MessageQueue(false);
		
		$this->assertTrue(is_array($_SESSION), "Session isn't an array...?");
		$this->assertEquals(0, count($_SESSION), "Session is already populated...?");
		
		$theMessage = new Message('title', 'the message');
		$que->add($theMessage);
		$que->save();
		
		$this->assertTrue(is_array($_SESSION), "Session was mangled after saving");
		$this->assertEquals(1, count($_SESSION), "Too much stuff in the session");
		$this->assertTrue(isset($_SESSION[MessageQueue::SESSIONKEY]), "Session key for message queue is missing after save");
		
		$this->assertEquals(count(Message::$validTypes), count($_SESSION[MessageQueue::SESSIONKEY]), "Session has unexpected number of message types");
		foreach(Message::$validTypes as $type) {
			$expectedNum = 0;
			if($type == $theMessage->type) {
				$expectedNum = 1;
			}
			$this->assertEquals($expectedNum, count($_SESSION[MessageQueue::SESSIONKEY][$type]), "too many messages of type '". $type ."'");
		}
		$this->assertEquals($theMessage, $_SESSION[MessageQueue::SESSIONKEY][$theMessage->type][0], "Saved message was mangled...");
	}
}