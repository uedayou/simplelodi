<?php
/**
 * Simple LODI for AWS S3 : Simple Linked Open Data Interface
 * Version: v2.1.0
 * Copyright (c) 2020 uedayou. All rights reserved.
 *
 */
require_once __DIR__.'/SimpleLODI.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class SimpleLODIS3 extends SimpleLODI {

	private $AWS_S3_BUCKET = "your-s3-bucket-name";
	private $AWS_REGION = "ap-northeast-1"; // or us-east-1, us-west-2, etc.
	//private $AWS_ACCESS_KEY = "";
	//private $AWS_SECRET_KEY = "";

	// The directory name with rdf files on s3 bucket.
	protected $data_dir = "data/";
	
	protected $root_dir = "";
	private $s3 = null;

	public function initialize($path,$url,$acceptHeader) {
		parent::initialize($path,$url,$acceptHeader);
		$this->s3 = S3Client::factory([
			//'credentials' => [
			//	'key' => $this->AWS_ACCESS_KEY,
			//	'secret' => $this->AWS_SECRET_KEY,
			//],
			'region' => $this->AWS_REGION,
			'version' => 'latest',
		]);
	}

	protected function getGraph() {
		$graph = new \EasyRdf\Graph();
		if ($this->use_sparql) {
			$this->setGraphFromSparql($graph);
		}
		else {
			$this->setGraphFromS3($graph);
		}
		return $graph;
	}

	private function setGraphFromS3(&$graph) {
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
		$text = $this->getS3Object($path);
		if ($this->encoding_autodetectmode) {
			// 文字コード自動判別モード
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
		}
		$graph->parse($text, $type);
	}


	private function getS3Object($key) {
		try {
			$result = $this->s3->getObject([
				'Bucket' => $this->AWS_S3_BUCKET,
				'Key' => $key
			]);
			return $result["Body"];
		} catch (S3Exception $e) {
			// echo $e->getMessage() . "\n";
		}
		return null;
	}
}

?>