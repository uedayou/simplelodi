<?php

/**
 * Simple LODI : Simple Linked Open Data Interface
 *
 * Copyright (c) 2016 Hiroshi Ueda. All rights reserved.
 *
 */

include 'Negotiation/AbstractNegotiator.php';
include 'Negotiation/Match.php';
include 'Negotiation/BaseAccept.php';
include 'Negotiation/Exception/Exception.php';
include 'Negotiation/Exception/InvalidArgument.php';
include 'Negotiation/Exception/InvalidMediaType.php';
include 'Negotiation/AcceptHeader.php';
include 'Negotiation/Accept.php';
include 'Negotiation/Negotiator.php';

include 'IRI/IRI.php';
include 'JsonLD/GraphInterface.php';
include 'JsonLD/JsonLdSerializable.php';
include 'JsonLD/Value.php';
include 'JsonLD/LanguageTaggedString.php';
include 'JsonLD/TypedValue.php';
include 'JsonLD/RdfConstants.php';
include 'JsonLD/NodeInterface.php';
include 'JsonLD/Node.php';
include 'JsonLD/Graph.php';
include 'JsonLD/JsonLD.php';

include 'easyrdf/lib/Http/Client.php';
include 'easyrdf/lib/Http/Response.php';
include 'easyrdf/lib/Http.php';
include 'easyrdf/lib/RdfNamespace.php';
include 'easyrdf/lib/Literal.php';
include 'easyrdf/lib/Resource.php';
include 'easyrdf/lib/Parser.php';
include 'easyrdf/lib/Serialiser.php';
include 'easyrdf/lib/Format.php';
include 'easyrdf/lib/Utils.php';
include 'easyrdf/lib/TypeMapper.php';
include 'easyrdf/lib/Exception.php';
include 'easyrdf/lib/Parser/RdfPhp.php';
include 'easyrdf/lib/Parser/Exception.php';
include 'easyrdf/lib/Parser/Json.php';
include 'easyrdf/lib/Parser/JsonLd.php';
include 'easyrdf/lib/Parser/Ntriples.php';
include 'easyrdf/lib/Parser/Rapper.php';
include 'easyrdf/lib/Parser/Rdfa.php';
include 'easyrdf/lib/Parser/Arc.php';
include 'easyrdf/lib/Parser/RdfXml.php';
include 'easyrdf/lib/Parser/Redland.php';
include 'easyrdf/lib/Parser/Turtle.php';
include 'easyrdf/lib/ParsedUri.php';
include 'easyrdf/lib/Serialiser/RdfPhp.php';
include 'easyrdf/lib/Serialiser/GraphViz.php';
include 'easyrdf/lib/Serialiser/Json.php';
include 'easyrdf/lib/Serialiser/JsonLd.php';
include 'easyrdf/lib/Serialiser/Ntriples.php';
include 'easyrdf/lib/Serialiser/Rapper.php';
include 'easyrdf/lib/Serialiser/Arc.php';
include 'easyrdf/lib/Serialiser/RdfXml.php';
include 'easyrdf/lib/Serialiser/Turtle.php';
include 'easyrdf/lib/Literal/HexBinary.php';
include 'easyrdf/lib/Literal/Boolean.php';
include 'easyrdf/lib/Literal/Date.php';
include 'easyrdf/lib/Literal/DateTime.php';
include 'easyrdf/lib/Literal/Decimal.php';
include 'easyrdf/lib/Literal/HTML.php';
include 'easyrdf/lib/Literal/Integer.php';
include 'easyrdf/lib/Literal/XML.php';
include 'easyrdf/lib/Graph.php';

define("DATA_DIR","data/");

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

$path = DATA_DIR.$path.".ttl";

if (!file_exists($path)) {
	// ファイルがないとき
	header("HTTP/1.1 404 Not Found");
	echo "Error!";
	exit;
}

$graph = new \EasyRdf\Graph();
//$rdf->load();
//$graph->parse($data, "turtle");
$graph->parseFile($path, "turtle");

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