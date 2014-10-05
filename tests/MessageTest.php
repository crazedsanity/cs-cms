<?php

use crazedsanity\Message;

require_once(dirname(__FILE__) .'/../Message.class.php');

class MessageQueueTest extends PHPUnit_Framework_TestCase {
    
    public function test_create() {
        $x = new MessageQueue();
    }
}