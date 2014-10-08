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
}