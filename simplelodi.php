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

define("DEBUG",true);

// Warning 表示しない
error_reporting(0);

if (!DEBUG&&!isset($_GET["path"])) {
	header("HTTP/1.1 404 Not Found");
	echo "Error!";
	exit;
}

$path = DEBUG?"uedayou":$_GET["path"];
$acceptHeader = DEBUG?"application/turtle":$_SERVER['HTTP_ACCEPT'];

$negotiator = new \Negotiation\Negotiator();

$priorities   = array(
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

$path = DATA_DIR.$path.DATA_EXTENSION;

if (!file_exists($path)) {
	// ファイルがないとき
	header("HTTP/1.1 404 Not Found");
	echo "Error!";
	exit;
}

$graph = new \EasyRdf\Graph();
//$rdf->load();
//$graph->parse($data, "turtle");
$graph->parseFile($path, DATA_TYPE);

$format = "turtle";// "php"; // html
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

$format = \EasyRdf\Format::getFormat($format);
$output = $graph->serialise($format);

if (!is_scalar($output)) {
	// HTML に変換する
	// 未実装
	$output = var_export($output, true);
}
else {
	header('Content-Type: '.$format->getDefaultMimeType());
	print $output;
}

?>