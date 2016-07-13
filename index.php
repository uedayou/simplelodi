<?php

require_once 'simplelodi_loader.php';
require_once 'SimpleLODI.php';

// デバッグフラグ
define("DEBUG", false);

$path = DEBUG?"uedayou.ttl":$_GET["path"];
$path = urldecode($path);
$url = DEBUG?"http://uedayou.net/simplelodi/uedayou":(empty($_SERVER["HTTPS"])?"http://":"https://").$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
$acceptHeader = DEBUG?"application/turtle":$_SERVER['HTTP_ACCEPT'];

// デフォルトはxml,nt,ttlのいずれかの拡張子を自動識別して、
// 拡張子に対応した解析方式 (rdf/xml、n-triples、turtle) により解析します。
$options = array();

// RDF/XML のみ使用する場合 
/*
$options = array(
    "DATA_TYPE"=>"rdfxml",
    "DATA_EXTENSION"=>".xml",
);
*/

// N-Triples のみ使用する場合 
/*
$options = array(
    "DATA_TYPE"=>"ntriples",
    "DATA_EXTENSION"=>".nt",
);
*/

// Turtle のみ使用する場合 
/*
$options = array(
    "DATA_TYPE"=>"turtle",
    "DATA_EXTENSION"=>".ttl",
);
*/

// SPARQLは現在未サポートです。
/*
$options = array(
	"USE_SPARQL"=>true,
	"SPARQL_REQUEST_TYPE"=>"POST",
	"SPARQL_ENDPOINT"=>"http://localhost:8080/sparql"
);
*/

$simplelodi = new SimpleLODI($options);

$simplelodi->initialize($path, $url, $acceptHeader);

$simplelodi->output();

?>