<?php

use crazedsanity\Message;
use crazedsanity\MessageQueue;

require_once(dirname(__FILE__) .'/../Message.class.php');

class MessageQueueTest extends PHPUnit_Framework_TestCase {
    
    public function test_create() {
        $q = new MessageQueue();
	    $m = new Message('title', 'message');
	    $q->add($m);
	    $m->render(new \crazedsanity\Template(dirname(__FILE__) .'/files/templates/message.tmpl'));
    }
}