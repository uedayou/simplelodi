<?php

require_once 'simplelodi_loader.php';
require_once 'SimpleLODI.php';

// デバッグフラグ
define("DEBUG", false);

$path = DEBUG?"uedayou.xml":$_GET["path"];
$path = urldecode($path);
$url = DEBUG?"http://uedayou.net/simplelodi/uedayou":(empty($_SERVER["HTTPS"])?"http://":"https://").$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
$acceptHeader = DEBUG?"application/turtle":$_SERVER['HTTP_ACCEPT'];

$simplelodi = new SimpleLODI();

// SPARQLは現在未サポートです。
/*
$options = array(
	"USE_SPARQL"=>true,
	"SPARQL_REQUEST_TYPE"=>"POST",
	"SPARQL_ENDPOINT"=>"http://localhost:8080/sparql"
);
$simplelodi = new SimpleLODI($options);
*/

$simplelodi->initialize($path, $url, $acceptHeader);

$simplelodi->output();

?>