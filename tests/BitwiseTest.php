<?php

class BitwiseTest extends PHPUnit_Framework_TestCase {
	
	public function test_isValid() {
		$x = new crazedsanity\Bitwise;
		$last = null;
		for($i=1;$i< 32; $i++) {
			if(is_null($last)) {
				$last = $i;
			}
			else {
				$last = $last * 2;
			}
			$this->assertTrue($x->isValid($last), "Failed on round #". $i);
		}
		
		$this->assertFalse($x->isValid(510));
		$this->assertFalse($x->isValid(96));
		$this->assertFalse($x->isValid(1536));
		$this->assertFalse($x->isValid(3072));
	}
	
	
	public function test_nextBit() {
		$x = new crazedsanity\Bitwise();
		
		$this->assertEquals(1, $x->nextBit(null));
		$this->assertEquals(2, $x->nextBit(1));
		$this->assertEquals(2, $x->nextBit(1));
		$this->assertEquals(4, $x->nextBit());
		$this->assertEquals(32, $x->nextBit(16));
		$this->assertEquals(2, $x->nextBit(1));
		
		try {
			$this->assertEquals(4, $x->nextBit(0));
		}
		catch(InvalidArgumentException $ex) {
			$this->assertTrue(true);
		}
	}
	
	
	
	public function test_canAccess() {
		$x = new crazedsanity\Bitwise();
		
		$this->assertTrue($x->canAccess(1,1));
		$this->assertTrue($x->canAccess(2,2));
		$this->assertTrue($x->canAccess(33, 32));
		$this->assertTrue($x->canAccess(33, 1));
		$this->assertTrue($x->canAccess(35, 1));
		$this->assertTrue($x->canAccess(35, 2));
		$this->assertTrue($x->canAccess(36, 32));
		
		$this->assertFalse($x->canAccess(64, 32));
		$this->assertFalse($x->canAccess(65, 2));
		$this->assertFalse($x->canAccess(0, 1));
		$this->assertFalse($x->canAccess(null, 1));
		
		try {
			$x->canAccess(65, 33);
			$this->assertTrue(false, "allowed access using invalid bit");
		} catch (Exception $ex) {
			$this->assertTrue(true);
		}
		
	}
	
	
	public function test_addAccess() {
		$x = new crazedsanity\Bitwise();
		
		$this->assertEquals(1, $x->addAccess(null, 1));
		$this->assertEquals(32, $x->addAccess(null,32));
		$this->assertEquals(1, $x->addAccess(0, 1));
		$this->assertEquals(32, $x->addAccess(0, 32));
		$this->assertEquals(3, $x->addAccess(2,1));
		
		$this->assertEquals(3, $x->addAccess(3,1));
	}
	
	
	public function test_removeAccess() {
		$x = new crazedsanity\Bitwise();
		
		$pRead = $x->nextBit();
		$pWrite = $x->nextBit();
		$pExecute = $x->nextBit();
		$pDelete = $x->nextBit();
		
		$all = null;
		$all = $x->addAccess($all, $pRead);
		$all = $x->addAccess($all, $pWrite);
		$all = $x->addAccess($all, $pExecute);
		$all = $x->addAccess($all, $pDelete);
		
		//make sure what we did is valid
		$this->assertEquals($all, ($pRead + $pWrite + $pExecute + $pDelete));
		
		$noRead = $x->removeAccess($all, $pRead);
		$this->assertFalse($x->canAccess($noRead, $pRead));
		$this->assertTrue($x->canAccess($noRead, $pWrite));
		$this->assertTrue($x->canAccess($noRead, $pExecute));
		$this->assertTrue($x->canAccess($noRead, $pDelete));
		
		$noReadOrWrite = $x->removeAccess($noRead, $pWrite);
		$this->assertFalse($x->canAccess($noReadOrWrite, $pWrite));
		$this->assertFalse($x->canAccess($noReadOrWrite, $pRead));
		$this->assertTrue($x->canAccess($noReadOrWrite, $pExecute));
		$this->assertTrue($x->canAccess($noReadOrWrite, $pDelete));
	}
}