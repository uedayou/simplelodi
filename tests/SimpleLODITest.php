<?php

require_once __DIR__.'/../src/vendor/autoload.php';
require_once __DIR__.'/../src/SimpleLODI.php';

use PHPUnit\Framework\TestCase;

class SimpleLODITest extends TestCase
{
    public function testTurtle()
    {
        $path = "uedayou";
        $url = "http://uedayou.net/simplelodi/uedayou";
        $acceptHeader = "application/turtle";
        $options = array();
        $prefixes = array();
        $simplelodi = new SimpleLODI($options, $prefixes);
        $simplelodi->initialize($path, $url, $acceptHeader);
        ob_start();
        $simplelodi->output(true);
        $actual = ob_get_clean();
        $expected = "@prefix foaf: <http://xmlns.com/foaf/0.1/> .\n\n<http://uedayou.net/simplelodi/uedayou>\n  a foaf:Person ;\n  foaf:mbox <mailto:yooueda@gmail.com> ;\n  foaf:homepage <http://uedayou.net/> ;\n  foaf:name \"Hiroshi Ueda\" ;\n  foaf:Image <http://uedayou.net/uedayou.jpg> .\n\n";
        $this->assertEquals($expected, $actual);
    }

    public function testJson()
    {
        $path = "uedayou";
        $url = "http://uedayou.net/simplelodi/uedayou";
        $acceptHeader = "application/json";
        $options = array();
        $prefixes = array();
        $simplelodi = new SimpleLODI($options, $prefixes);
        $simplelodi->initialize($path, $url, $acceptHeader);
        ob_start();
        $simplelodi->output(true);
        $actual = ob_get_clean();
        $expected = '{"http://uedayou.net/simplelodi/uedayou":{"http://www.w3.org/1999/02/22-rdf-syntax-ns#type":[{"type":"uri","value":"http://xmlns.com/foaf/0.1/Person"}],"http://xmlns.com/foaf/0.1/mbox":[{"type":"uri","value":"mailto:yooueda@gmail.com"}],"http://xmlns.com/foaf/0.1/homepage":[{"type":"uri","value":"http://uedayou.net/"}],"http://xmlns.com/foaf/0.1/name":[{"type":"literal","value":"Hiroshi Ueda"}],"http://xmlns.com/foaf/0.1/Image":[{"type":"uri","value":"http://uedayou.net/uedayou.jpg"}]}}';
        $this->assertEquals($expected, $actual);
    }

    public function testXml()
    {
        $path = "uedayou";
        $url = "http://uedayou.net/simplelodi/uedayou";
        $acceptHeader = "text/xml";
        $options = array();
        $prefixes = array();
        $simplelodi = new SimpleLODI($options, $prefixes);
        $simplelodi->initialize($path, $url, $acceptHeader);
        ob_start();
        $simplelodi->output(true);
        $actual = ob_get_clean();
        $expected = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n<rdf:RDF xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"\n         xmlns:foaf=\"http://xmlns.com/foaf/0.1/\">\n\n  <foaf:Person rdf:about=\"http://uedayou.net/simplelodi/uedayou\">\n    <foaf:mbox rdf:resource=\"mailto:yooueda@gmail.com\"/>\n    <foaf:homepage rdf:resource=\"http://uedayou.net/\"/>\n    <foaf:name>Hiroshi Ueda</foaf:name>\n    <foaf:Image rdf:resource=\"http://uedayou.net/uedayou.jpg\"/>\n  </foaf:Person>\n\n</rdf:RDF>\n";
        $this->assertEquals($expected, $actual);        
    }

    public function testHtml()
    {
        $path = "uedayou";
        $url = "http://uedayou.net/simplelodi/uedayou";
        $acceptHeader = "text/html";
        $options = array();
        $prefixes = array();
        $simplelodi = new SimpleLODI($options, $prefixes);
        $simplelodi->initialize($path, $url, $acceptHeader);
        ob_start();
        $simplelodi->output(true);
        $actual = ob_get_clean();
        $expected = file_get_contents(__DIR__.'/../src/templates/spa/simple-lodi-frontend.html');
        $expected = substr($expected, 0, 100);
        $actual = substr($actual, 0, 100);
        $this->assertEquals($expected, $actual);
    }
}