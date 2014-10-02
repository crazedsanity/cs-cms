<?php

use crazedsanity\Template;
use crazedsanity\cs_global;

class TemplateTest extends PHPUnit_Framework_TestCase {

	public function test_create() {
		$justFile = new Template(dirname(__FILE__) .'/files/templates/main.tmpl');
		$this->assertEquals('main', $justFile->name);
		$this->assertEquals(file_get_contents(dirname(__FILE__) .'/files/templates/main.tmpl'), $justFile->contents);

		$full = new Template(dirname(__FILE__) .'/files/templates/main.tmpl', "test");
		$this->assertEquals('test', $full->name);
		$this->assertEquals(file_get_contents(dirname(__FILE__) .'/files/templates/main.tmpl'), $full->contents);

		$empty = new Template(null, "empty");
		$this->assertEquals('empty', $empty->name);
		$this->assertEquals(null, $empty->contents);
	}


	public function test_render() {
		$x = new Template(dirname(__FILE__) .'/files/templates/file3.tmpl');
		$originalContents = $x->contents;

		$rendered = $x->render();
		$this->assertTrue(strlen($x->render()) > 0, "failed to render template");

		$this->assertNotEquals($originalContents, $x->render(), "render did not remove loose template strings");

		$this->assertEquals($originalContents, $x->render(false), "render removed loose template strings when told not to");
		$x->addVar("empty");
		$this->assertEquals($x->render(false), $x->render(true), "render failed when all template vars accounted for");
	}


	public function test_setRecursion() {
		try {
			$x = new Template(null);
			$x->set_recursionDepth(null);
		}
		catch(InvalidArgumentException $ex) {
			$this->assertTrue((bool)preg_match('~^$~', $ex->getMessage()), "unexpected exception contents: ". $ex->getMessage());
		}
	}


	public function test_recursion() {
		$x = new Template(null, "main");
		$x->setContents("{recursive1}");
		$x->set_recursionDepth(50);


		$x->add(new Template(dirname(__FILE__) .'/files/templates/recursive1.tmpl'),false);
		$x->add(new Template(dirname(__FILE__) .'/files/templates/recursive2.tmpl'), false);

		$rendered = $x->render();
		$this->assertTrue(strlen($rendered) > 0, "rendered value is blank... ");

		$matches = array();
		$num = preg_match_all('~recursive1~', $rendered, $matches);

		$this->assertEquals(50, $num, "did not recurse... ");
	}
}
