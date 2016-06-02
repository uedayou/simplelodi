<?php

/**
 * Simple LODI : Simple Linked Open Data Interface
 *
 * Copyright (c) 2016 Hiroshi Ueda. All rights reserved.
 *
 */

require_once 'simplelodi_load.php';

define("DATA_DIR","data/");

define("DATA_TYPE", "turtle");
define("DATA_EXTENSION", ".ttl");

define("TEMPLATE_HTML", "basic.html");

// 未サポート
define("SPARQL_FLAG", false);
define("SPARQL_ENDPOINT", "http://localhost/sparql");

// デバッグフラグ
define("DEBUG", false);

// Warning 表示しない
error_reporting(0);

if (!DEBUG&&!isset($_GET["path"])) {
	show404();
	exit;
}

$path = DEBUG?"uedayou.xml":$_GET["path"];
$url = DEBUG?"http://uedayou.net/simplelodi/uedayou":(empty($_SERVER["HTTPS"])?"http://":"https://").$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
$acceptHeader = DEBUG?"application/turtle":$_SERVER['HTTP_ACCEPT'];

$path_parts = pathinfo($path);
$dir = $path_parts['dirname'];
$dir = $dir=="."?"":$dir;
$basename = $path_parts['basename'];
$extension = isset($path_parts['extension'])?$path_parts['extension']:"";
$filename = $path_parts['filename'];

if (strlen($extension)>0) {
	$acceptHeader = getAcceptHeaderByExtension($extension, $acceptHeader);
}

$negotiator = new \Negotiation\Negotiator();

$priorities = array(
	'text/html',
	'application/rdf+xml',
	'application/xml',
	'text/xml',
	'text/rdf',
	'application/n-triples',
	'text/plain',
	'text/turtle',
	'application/x-turtle',
	'application/turtle',
	'text/n3',
	'text/rdf+n3',
	'application/rdf+n3',
	'application/ld+json',
	'application/json');

try {
	$mediaType = $negotiator->getBest($acceptHeader, $priorities);
} catch (Exception $e){
	$mediaType = null;
}

if ($mediaType!=null) {
	$value = $mediaType->getValue();
} else {
	$value = 'text/html';
}

$graph = new \EasyRdf\Graph();
//$rdf->load();

if (SPARQL_FLAG) {
	// SPARQLからデータ取得
	// POST
	$opts = array(
	  'http'=>array(
	    'method'=>"POST",
	    'header'=>"Accept: text/turtle\r\nContent-Type: application/x-www-form-urlencoded\r\n",
	    //"content"=>"query=describe <http://linkdata.org/resource/rdf1s947i#".$filename.">"
	    "content"=>"query=describe <".$url.">"
	  )
	);
	
	// GET
	/*
	$opts = array(
	  'http'=>array(
	    'method'=>"GET",
	    'header'=>"Accept: text/turtle\r\n",
	    //"content"=>"query=describe <http://linkdata.org/resource/rdf1s947i#".$filename.">"
	    "content"=>"query=describe <".$url.">"
	  )
	);
	*/
	$context = stream_context_create($opts);
	$endpoint = SPARQL_ENDPOINT;
	$text = file_get_contents($endpoint, false, $context);
	if ($text==false) {
		show404();
		exit;
	}
	$graph->parse($text, "turtle");

}
else {
	// ローカル Turtleファイルからデータ取得
	
	$path = DATA_DIR.$dir.$filename.DATA_EXTENSION;
	if (!file_exists($path)) {
		// ファイルがないとき
		$path = DATA_DIR.$dir.$basename.DATA_EXTENSION;
		if (!file_exists($path)) {
			// ファイルがないとき
			show404();
			exit;
		}
	}

	$graph->parseFile($path, DATA_TYPE);
}

$format = getFormat($value);

$format = \EasyRdf\Format::getFormat($format);
$output = $graph->serialise($format);


if (!is_scalar($output)) {
	// HTML に変換する
	$output = getHTML($output, $url);
}
else {
	header('Content-Type: '.$format->getDefaultMimeType());
}
print $output;


function getHTML($data, $url) {
	$html = "";

	//$loader = new Twig_Loader_String();
	$loader = new Twig_Loader_Filesystem('templates');
	$twig = new Twig_Environment($loader);
	$html = $twig->render(TEMPLATE_HTML, array('data'=>$data, 'url'=>$url));

	return $html;
}

function getFormat($value) {
	$format = "php"; // html
	if (preg_match("/xml/i", $value)) {
		$format = "rdfxml";
	}
	else if ($value=="text/rdf") {
		$format = "rdfxml";
	}
	else if (preg_match("/ld\+json/i", $value)) {
		$format = "jsonld";
	}
	else if (preg_match("/json/i", $value)) {
		$format = "json";
	}
	else if (preg_match("/turtle/i", $value)) {
		$format = "turtle";
	}
	else if (preg_match("/plain/i", $value)) {
		$format = "turtle";
	}
	else if (preg_match("/n3/i", $value)) {
		$format = "n3";
	}
	else if (preg_match("/n-triples/i", $value)) {
		$format = "ntriples";
	}
	return $format;
}

function getAcceptHeaderByExtension($extension, $acceptHeader) {
	if ($extension=="jsonld") {
		$acceptHeader = 'application/ld+json';
	}
	else if ($extension=="json") {
		$acceptHeader = 'application/json';
	}
	else if ($extension=="xml") {
		$acceptHeader = 'application/rdf+xml';
	}
	else if ($extension=="rdf") {
		$acceptHeader = 'application/rdf+xml';
	}
	else if ($extension=="ttl") {
		$acceptHeader = 'application/turtle';
	}
	else if ($extension=="n3") {
		$acceptHeader = 'application/rdf+n3';
	}
	else if ($extension=="nt") {
		$acceptHeader = 'application/n-triples';
	}
	else if ($extension=="html") {
		$acceptHeader = 'text/html';
	}
	return $acceptHeader;
}

function show404() {
	header("HTTP/1.1 404 Not Found");
	echo "Error!";
}

?>