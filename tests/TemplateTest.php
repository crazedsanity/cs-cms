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



	public function test_origin() {
		$file = dirname(__FILE__) .'/files/templates/main.tmpl';
		$x = new Template($file);
		$this->assertEquals($file, $x->origin);

		$y = new Template(null);
		$this->assertEquals(null, $y->origin);
	}


	public function test_dir() {
		$file = dirname(__FILE__) .'/files/templates/main.tmpl';

		$x = new Template($file);
		$this->assertEquals(dirname($file), $x->dir, "template dir not set");

		$y = new Template(null);
		$this->assertEquals(null, $y->dir, "template dir not null when null used for filename (". $y->dir .")");
	}


	public function test_basics() {
		$x = new Template(dirname(__FILE__) .'/files/templates/main.tmpl');

		$one = new Template(dirname(__FILE__) .'/files/templates/file1.tmpl');
		$one->addVar('file2', "test");
		$one->addVar('var1', "template");
		$one->addVar('var2', "file");
		$one->addVar('var3', "inheritance is awesome");

		$x->add($one);

		$this->assertTrue((bool)preg_match('~file2: test~', $x->render()), "template inheritance failed::: ". $x->render());
		$this->assertTrue((bool)preg_match('~file1: contents from file1~', $x->render()), "contents from file1 not loaded into main template");
		$this->assertTrue((bool)preg_match('~template file inheritance is awesome~', $x->render()), "template var inheritance failed");

		$two = new Template(dirname(__FILE__) .'/files/templates/file2.tmpl');
		$two->addVar('var3', "was changed");

		$x->add($two);

		$this->assertTrue((bool)preg_match('~file2: contents from file2~', $x->render()), "new template did not work");
		$this->assertTrue((bool)preg_match('~template file was changed~', $x->render()), "new template did not overwrite original vars");
	}
}
