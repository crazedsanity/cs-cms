<?php

use crazedsanity\Template;

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
        $this->assertEquals("contents from file3", $x->render())
		

    }
}