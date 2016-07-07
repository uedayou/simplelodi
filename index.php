<?php

require_once 'simplelodi_loader.php';
require_once 'SimpleLODI.php';

// デバッグフラグ
define("DEBUG", false);

$path = DEBUG?"uedayou.ttl":$_GET["path"];
$path = urldecode($path);
$url = DEBUG?"http://uedayou.net/simplelodi/uedayou":(empty($_SERVER["HTTPS"])?"http://":"https://").$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
$acceptHeader = DEBUG?"application/turtle":$_SERVER['HTTP_ACCEPT'];

$options = array();

// 2016年7月現在 easyrdf　による Turtleファイルの解析が異常に遅いので、
// 10KB以上のRDFファイルを扱いたい場合は、TurtleファイルをRDF/XMLに変換し、拡張子をxmlに変更して
// 以下のコメントアウトをはずしてください。
/*
$options = array(
    "DATA_TYPE"=>"rdfxml",
    "DATA_EXTENSION"=>".xml",
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