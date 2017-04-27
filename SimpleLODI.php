<?php
/**
 * Simple LODI : Simple Linked Open Data Interface
 *
 * Copyright (c) 2016 Hiroshi Ueda. All rights reserved.
 *
 */

class SimpleLODI {
	
	protected $data_dir = "data/";
	protected $data_type = "auto"; // auto, turtle, rdfxml, ntriples, jsonld, n3
	protected $data_extension = ".ttl";
	protected $template_html = "basic.tmpl";
	protected $use_sparql = false;
	protected $sparql_endpoint = "http://localhost/sparql";
	protected $sparql_request_type = "POST"; // GET or POST
	protected $sparql_use_virtuoso = false;

	private $path = null;

	private $url = null;

	private $acceptHeader = null;

	protected $dir = null;
	protected $basename = null;
	protected $extension = null;
	protected $filename = null;

	// 文字コード自動判別モード
	protected $encoding_autodetectmode = false;

	private $mediaType = 'text/html';

	public $notFound = false;

	protected $priorities = array(
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
		'application/json'
		);

	public function __construct($options=null, $prefixes=null) {
		$this->data_dir = isset($options["DATA_DIR"])?$options["DATA_DIR"]:$this->data_dir;
		$this->data_type = isset($options["DATA_TYPE"])?$options["DATA_TYPE"]:$this->data_type;
		$this->data_extension = isset($options["DATA_EXTENSION"])?$options["DATA_EXTENSION"]:$this->data_extension;
		$this->template_html = isset($options["TEMPLATE_HTML"])?$options["TEMPLATE_HTML"]:$this->template_html;
		$this->use_sparql = isset($options["USE_SPARQL"])?$options["USE_SPARQL"]:$this->use_sparql;
		$this->sparql_use_virtuoso = isset($options["USE_VIRTUOSO"])?$options["USE_VIRTUOSO"]:$this->sparql_use_virtuoso;
		$this->sparql_endpoint = isset($options["SPARQL_ENDPOINT"])?$options["SPARQL_ENDPOINT"]:$this->sparql_endpoint;
		$this->sparql_request_type = isset($options["SPARQL_REQUEST_TYPE"])?$options["SPARQL_REQUEST_TYPE"]:$this->sparql_request_type;
		$this->encoding_autodetectmode = isset($options["ENCODING_AUTODETECT_MODE"])?$options["ENCODING_AUTODETECT_MODE"]:$this->encoding_autodetectmode;

		// 共通語彙基盤 NS追加
		\EasyRdf\RdfNamespace::set('ic', "http://imi.go.jp/ns/core/rdf#");
		\EasyRdf\RdfNamespace::set('ic22', "http://imi.ipa.go.jp/ns/core/rdf#");

		if (isset($prefixes)&&is_array($prefixes)) {
			foreach($prefixes as $k=>$v) {
				\EasyRdf\RdfNamespace::set($k, $v);
			}
		}
	}

	public function initialize($path,$url,$acceptHeader) {
		if ($this->notFound) return;
		$this->url = $url;
		$this->acceptHeader = $acceptHeader;
		setlocale(LC_ALL, 'ja_JP.UTF-8');
		//setlocale(LC_ALL, 'Japanese_Japan.932');
		// ディレクトリ指定の場合はファイル名 index をつける 2017.04.20
		if (substr($path, -1) === "/"||strlen($path)===0) {
			$path .= "index";
		}
		$path_parts = pathinfo($path);
		$this->dir = $path_parts['dirname']."/";
		$this->dir = $this->dir=="."?"":$this->dir;
		$this->basename = $path_parts['basename'];
		$this->extension = isset($path_parts['extension'])?$path_parts['extension']:"";
		$this->filename = $path_parts['filename'];

		if (strlen($this->extension)>0) {
			$this->acceptHeader = $this->getAcceptHeaderByExtension($this->extension, $this->acceptHeader);
		}

		$negotiator = new \Negotiation\Negotiator();

		try {
			$mediaType = $negotiator->getBest($this->acceptHeader, $this->priorities);
		} catch (Exception $e){
			$mediaType = null;
		}

		if ($mediaType!=null) {
			$this->mediaType = $mediaType->getValue();
		}
	}

	public function output() {
		if ($this->notFound) return;
		$graph = $this->getGraph();
		if ($this->notFound) return;

		$format = $this->getFormat($this->mediaType);

		$format = \EasyRdf\Format::getFormat($format);
		$output = $graph->serialise($format);

		if (!is_scalar($output)) {
			// HTML に変換する
			$output = $this->getHTML($output, $this->url);
		}
		else {
			header('Content-Type: '.$format->getDefaultMimeType());
		}
		print $output;
	}

	protected function getGraph() {
		$graph = new \EasyRdf\Graph();
		if ($this->use_sparql) {
			$this->setGraphFromSparql($graph);
		}
		else {
			$this->setGraphFromFilesystem($graph);
		}
		return $graph;
	}

	private function setGraphFromFilesystem(&$graph) {
		$path = $type = false;
		if($this->data_type=="auto") {
			$rt = $this->searchRdfFilePath();
			if (is_array($rt)) {
				$path = $rt["path"];
				$type = $rt["type"];
			}
		} else {
			$path = $this->getRdfFilePath($this->data_extension);
			if (is_string($path)) {
				$type = $this->data_type;
			}
		}
		if ($path==false) {
			$this->show404();
			return;
		}
		if ($this->encoding_autodetectmode) {
			// 文字コード自動判別モード
			$text = file_get_contents($path);
			$encoding = false;
			foreach(array('UTF-8','SJIS-win','SJIS','EUC-JP','ASCII','JIS') as $ccode){
			  if(strcmp(mb_convert_encoding($text, $ccode, $ccode),$text)==0){
			    $encoding = $ccode;
			    break;
			  }
			}
			if($encoding!==false){
				$text = mb_convert_encoding($text, "utf8", $encoding);
			}
			// UTF8 BOM 削除
			$text = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $text);
			$graph->parse($text, $type);
		} else {
			$graph->parseFile($path, $type);
		}
	}

	private function searchRdfFilePath() {
		// rdf/xml
		$path = $this->getRdfFilePath(".xml");
		if (is_string($path)) {
			return array("path"=>$path,"type"=>"rdfxml");
		}
		// n-triples
		$path = $this->getRdfFilePath(".nt");
		if (is_string($path)) {
			return array("path"=>$path,"type"=>"ntriples");
		}
		// turtle
		$path = $this->getRdfFilePath(".ttl");
		if (is_string($path)) {
			return array("path"=>$path,"type"=>"turtle");
		}
		return false;
	}

	private function getRdfFilePath($extension) {
		$path = $this->data_dir.$this->dir.$this->filename.$extension;
		if (!file_exists($path)) {
			// ファイルがないとき
			$path = $this->data_dir.$this->dir.$this->basename.$extension;
			if (!file_exists($path)) {
				// ファイルがないとき
				return false;
			}
		}
		return $path;
	}

	private function setGraphFromSparql(&$graph) {
		// SPARQLからデータ取得
		$text = false;
		if ($this->sparql_use_virtuoso) {
			// RDFストアが Virtuosoの場合
			$endpoint = $this->sparql_endpoint."?format=text/plain&query=".urlencode($this->getSparqlQuery());
			$text = file_get_contents($endpoint);
		} else {
			$opts = array(
				'http'=>array(
					'method'=>$this->sparql_request_type,
					//'header'=>"Accept: text/turtle\r\n"
					'header'=>"Accept: application/n-triples\r\n"
					.($this->sparql_request_type=="POST"?"Content-Type: application/x-www-form-urlencoded\r\n":""),
					"content"=>"query=".$this->getSparqlQuery()
				)
			);
			$context = stream_context_create($opts);
			$endpoint = $this->sparql_endpoint;
			$text = file_get_contents($endpoint, false, $context);
		}
		if ($text==false) {
			$this->show404();
			return;
		}
		//$graph->parse($text, "turtle");
		$graph->parse($text, "ntriples");
	}

	protected function getSparqlQuery() {
		//return "describe <http://linkdata.org/resource/rdf1s947i#".$this->filename.">";
		//return "describe <".$this->url.">";
		// ブランクノードへの対応
		return "CONSTRUCT {<".$this->url."> ?y ?z. ?z ?w ?v } WHERE {<".$this->url."> ?y ?z. OPTIONAL {?z ?w ?v. FILTER (isBlank(?z))} }";
	}

	function getHTML($data, $url) {
		$html = "";

		//$loader = new Twig_Loader_String();
		$loader = new Twig_Loader_Filesystem('templates');
		$twig = new Twig_Environment($loader);

		$function = new Twig_SimpleFunction('bnode_info', function ($val,$_data) {
			$islink = function($text,$f=true) {
				$label = $text;
				if ($f) {
					$short = \EasyRdf\RdfNamespace::shorten($text);
				$label = $short?$short:$text;
				}
				if ( preg_match('{^https?://.+?\.(jpg|jpeg|gif|png)$}i' ,$text ) ){
					return '<a href="'.$text.'" target="_blank"><img src="'.$text.'" /></a>';
				} else if (preg_match('{^https?://.+$}i', $text) ){
					return '<a href="'.$text.'" target="_blank">'.$label.'</a>';
				} else if (preg_match('{^mailto:.+$}i', $text) ){
					return '<a href="'.$text.'" target="_blank">'.$label.'</a>';
				}
				return $text;
			};
			$output = "";
			$d = $_data[$val];
			$output .= "<table class='table table-bordered'>";
			foreach($d as $k=>$v ) {
				$output .= "<tr><th class='bnode'>".$islink($k)."</th><td>";
				foreach($v as $vo) {
					$output .= $islink($vo["value"]);
				}
				$output .= "</td></tr>";
			}
			$output .= "</table>";
			return $output;
		});
		$twig->addFunction($function);

		$function2 = new Twig_SimpleFunction('shorten', function ($uri) {
			$short = \EasyRdf\RdfNamespace::shorten($uri);
			return $short?$short:$uri;
		});
		$twig->addFunction($function2);

		$function3 = new Twig_SimpleFunction('getlatlng', function($_rdfs) {
			$plat = "http://www.w3.org/2003/01/geo/wgs84_pos#lat";
			$plng = "http://www.w3.org/2003/01/geo/wgs84_pos#long";
			$lat = $lng = null;
			if (isset($_rdfs[$plat])) {
				$lat = $_rdfs[$plat][0]["value"];
			}
			if (isset($_rdfs[$plng])) {
				$lng = $_rdfs[$plng][0]["value"];
			}
			if (isset($lat)&&isset($lng)){
				return <<< EOM
<div id="map" style="width:100%; height: 400px"></div>
<script type="text/javascript">
var map = L.map('map').setView([$lat, $lng], 16);
L.tileLayer('http://tile.openstreetmap.jp/{z}/{x}/{y}.png', {
	maxZoom: 18,
	attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);
L.marker([$lat, $lng]).addTo(map);
</script>
EOM;
			}
		});
		$twig->addFunction($function3);

		$_url = urldecode($url);
		$html = $twig->render($this->template_html, array('data'=>$data, 'url'=>$_url, 'title'=>$this->getTitleFromRDF($data,$_url)));

		return $html;
	}

	function getTitleFromRDF($data, $url) {
		$d = $data[$url];
		$lprops = array(
			"http://xmlns.com/foaf/0.1/name",
			"http://purl.org/dc/elements/1.1/title",
			"http://purl.org/dc/terms/title",
			"http://schema.org/name",
			"http://www.w3.org/2000/01/rdf-schema#label"
		);
		foreach( $lprops as $p) {
			if ( isset($d[$p])) {
				return $d[$p][0]["value"];
			}
		}
		return $url;
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
		if ($this->notFound) return;
		header("HTTP/1.1 404 Not Found");
		echo "Error!";
		$this->notFound = true;
	}


}

?>