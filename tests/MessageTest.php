<?php

use crazedsanity\Template;
use crazedsanity\Message;
use crazedsanity\MessageQueue;
use crazedsanity\cs_global;

class MessageQueueTest extends PHPUnit_Framework_TestCase {
    
    public function test_create() {
        $que = new MessageQueue();
		
		
		$que->add(new Message('first title', 'first message'));
		$que->add(new Message('second title', 'second message', Message::TYPE_FATAL));
		$que->add(new Message('third', 'a 3rd of them', Message::TYPE_ERROR, 'http://foo.bar/error', 'GO'));
	    
		$this->assertEquals(true, (bool)$que->hasFatalError(), "fatal error not detected");
		$this->assertEquals(1, $que->hasFatalError(), "invalid number of fatal messages");
		
		$out = $que->render(new Template(dirname(__FILE__) .'/files/templates/message.tmpl'));
		
		$this->assertTrue(strlen($out) > 0, "rendered message is blank");
		
		$this->assertEquals(3, preg_match_all('~TITLE: ~', $out), "could not find all titles... ". cs_global::debug_print($out,0));
		$this->assertEquals(3, preg_match_all('~MESSAGE: ~', $out), "could not find all message bodies");
		$this->assertEquals(3, preg_match_all('~TYPE: ~', $out), "could not find all types");
		$this->assertEquals(3, preg_match_all('~LINKTEXT: ~', $out), "could not find all linkText fields");
		$this->assertEquals(3, preg_match_all('~URL: ~', $out), "could not find all url fields");
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
		$this->assertEquals($theMessage, $_SESSION[MessageQueue::SESSIONKEY][$theMessage->type][0], "Saved message was mangled... ". cs_global::debug_print($_SESSION[MessageQueue::SESSIONKEY][$theMessage->type][0],0));
	}
}