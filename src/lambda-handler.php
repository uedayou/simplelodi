<?php
ini_set('display_errors', "On");
require __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/SimpleLODI.php';
//require_once __DIR__.'/SimpleLODIS3.php';

// デバッグフラグ
define("DEBUG", false);

$path = isset($_SERVER["PATH_INFO"])?$_SERVER["PATH_INFO"]:$_SERVER["SCRIPT_NAME"];
$path = urldecode($path);
$url = DEBUG?"http://uedayou.net/simplelodi/uedayou":(empty($_SERVER["HTTPS"])?"http://":"https://").$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
$acceptHeader = DEBUG?"application/turtle":(isset($_SERVER['HTTP_ACCEPT'])?$_SERVER['HTTP_ACCEPT']:"");

$path = str_replace("_"," ",$path);
$path = preg_replace("/^\/(.+$)/i","$1", $path);
$url = str_replace("_"," ",$url);

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
// header("Access-Control-Allow-Origin: *");

$simplelodi = new SimpleLODI($options, $prefixes);
//$simplelodi = new SimpleLODIS3($options, $prefixes);

$simplelodi->initialize($path, $url, $acceptHeader);

$simplelodi->output();

?>