<?php

use crazedsanity\Template;
use crazedsanity\GenericPage;
use crazedsanity\cs_global;

//This is only for Cloud9; it works on my home machine...??? 
require_once(dirname(__FILE__) .'/../GenericPage.class.php');

class GenericPageTest extends PHPUnit_Framework_TestCase {

	public function test_instantiation() {
		$x = new GenericPage(new Template(dirname(__FILE__) .'/files/templates/main.tmpl'));
		$this->assertTrue(is_object($x));
		$this->assertTrue(is_object($x->mainTemplate));

		$x = new GenericPage();
		$this->assertTrue(is_object($x));
		$this->assertTrue(is_object($x->mainTemplate));
	}



	public function test_templateStuff() {
		$x = new GenericPage(new Template(dirname(__FILE__) .'/files/templates/main.tmpl'));

		$x->add_template_file('file1', dirname(__FILE__) .'/files/templates/file1.tmpl');

		$this->assertTrue(isset($x->mainTemplate->templates['file1']));
		$this->assertEquals(file_get_contents(dirname(__FILE__) .'/files/templates/file1.tmpl'), $x->mainTemplate->templates['file1']);

		$x->clear_content("file1");
		$this->assertEquals("", $x->mainTemplate->templates["file1"]);

		$x->change_content(__METHOD__, "file1");
		$this->assertEquals(__METHOD__, $x->mainTemplate->templates["file1"]);

		$this->assertEquals($x->render_page(), $x->render_page(true), "rendered page is different based on argument to render_page()");
		$this->assertNotEquals($x->render_page(true), $x->render_page(false), "rendered page without stripping undefined vars is broken");
	}


	public function test_allowInvalidUrls() {
		$x = new GenericPage();

		$this->assertEquals($x->allow_invalid_urls(), $x->allow_invalid_urls(null));
		$this->assertEquals(false, $x->allow_invalid_urls(), "unexpected initial setting");
	}
}
