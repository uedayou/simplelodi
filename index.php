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
$prefixes = array();

// PREFIX追加(共通語彙基盤は既に登録済み)
/*
$prefixes = array(
    "pb"=>"http://uedayou.net/lod/property/",
);
*/

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
// RDFストアにVirtuoso を使う場合は必ず、USE_VIRTUOSO を true にしてください。
/*
$options = array(
	"USE_SPARQL"=>true,
    "USE_VIRTUOSO"=>false,
	"SPARQL_REQUEST_TYPE"=>"POST",
	"SPARQL_ENDPOINT"=>"http://localhost:8080/sparql"
);
*/

// 文字コード自動判別モード オン
// ※ dataフォルダの中のファイルの文字エンコードがまちまちの場合は、true にしてください。
// ※ 文字コードを自動判別するようになりますが、誤判定することもあります。
/*
$options = array(
    "ENCODING_AUTODETECT_MODE"=>true
);
*/

// CORS
header("Access-Control-Allow-Origin: *");

$simplelodi = new SimpleLODI($options, $prefixes);

$simplelodi->initialize($path, $url, $acceptHeader);

$simplelodi->output();

?>