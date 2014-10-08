<?php

use crazedsanity\IdObfuscator;

class TestOfIdObfuscator extends TestDbAbstract {
	
	
	public function __construct() {
		parent::__construct();
	}//end __construct()
	
	public function setUp() {}
	public function tearDown(){}
	
	
	
	public function test_smallNumbers() {
		
		if(!defined('CRYPT_SALT')) {
			define('CRYPT_SALT', microtime(true));
		}
		
		for($i=1; $i<1000; $i++) {
			
			$idToEncrypt = $i;
			
			$encoded = IdObfuscator::encode($idToEncrypt);
			$decoded = IdObfuscator::decode($encoded);
			
			$this->assertNotEquals($idToEncrypt, $encoded);
			#$this->assertFalse($encoded == $decoded);
			$this->assertEquals($idToEncrypt, $decoded);
		}
	}
	
	
	public function test_bigNumbers() {
		
		if(!defined('CRYPT_SALT')) {
			define('CRYPT_SALT', microtime(true));
		}
		
		for($i=1; $i<1000; $i++) {
			
			$idToEncrypt = ($i + rand(99999, 999999999));
			
			$encoded = IdObfuscator::encode($idToEncrypt);
			$decoded = IdObfuscator::decode($encoded);
			
			$this->assertNotEquals($idToEncrypt, $encoded);
			#$this->assertFalse($encoded == $decoded);
			$this->assertEquals($idToEncrypt, $decoded);
		}
	}
}