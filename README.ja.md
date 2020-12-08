# Simple LODI : Simple Linked Open Data Interface(v2.1.0)

コンテントネゴシエーションや拡張子変更により、RDFファイルをさまざまなフォーマットで出力ができるLODフロントエンドプログラムです。
対応する入力フォーマットは、RDF/XML(拡張子：xml)、N-Triples(拡張子:nt)、Turtle(拡張子:ttl)です。

## 対応出力フォーマット(拡張子)

- HTML(.html)
- RDF/XML(.rdf/.xml)
- JSON(.json)
- JSON-LD(.jsonld)
- Turtle(.ttl)
- Notation3(.n3)
- N-Triples(.nt)

## 環境

- PHP 7.2.5以上
- mod_rewrite

## デモ

<http://uedayou.net/simplelodi-v2/uedayou>

HTML出力  
<http://uedayou.net/simplelodi-v2/uedayou.html>

Turtle出力  
<http://uedayou.net/simplelodi-v2/uedayou.ttl>

XML出力  
<http://uedayou.net/simplelodi-v2/uedayou.xml>

JSON出力  
<http://uedayou.net/simplelodi-v2/uedayou.json>

JSON-LD出力  
<http://uedayou.net/simplelodi-v2/uedayou.jsonld>

## インストール・使い方(Turtleファイルの場合)

(1) simplelodi フォルダをWebサーバ上の任意の場所にコピーしてください。  
(2) simplelodi フォルダをリネームしてください(たとえば、resource など)  
(3) data フォルダに　サンプルファイル(data/uedayou.ttl)を参考にTurtle ファイルを作成し、拡張子を`ttl`としてコピーしてください。  
(4) `http:// ... /resource/[拡張子を省略したファイル名]` をブラウザで開くと、HTMLが表示されます。 たとえば uedayou.ttl の場合は、 `http:// ... /resource/uedayou` となります  
(5) 対応する拡張子(対応フォーマット節　参照)をつけると、出力フォーマットを変更できます。 

※ RDF/XMLであれば拡張子をxmlに、N-Triplesであれば拡張子をntとしてください。

あわせて、こちらもご覧ください。　　

[比較的簡単にDBpediaのようにLinked Open Data(LOD)を公開する方法](http://qiita.com/uedayou/items/d66b7c406f1f231347f5)

## Amazon Web Service上での利用

SimpleLODIはAWS SAMを利用してサーバレス環境で動作させることも可能です。
以下をデプロイするとAWSクラウド上に以下のような環境が作成されます。

```
Amazon API Gateway -- AWS Lambda
```

(1) AWS CLI と AWS SAM CLI のインストール

AWS CLI と AWS SAM CLI をあらかじめインストールしておいてください。

<https://aws.amazon.com/cli/>
<https://aws.amazon.com/serverless/sam/>

(2) SAMパッケージ作成

以下のコマンドにより、SimpleLODIをパッケージ化してください。

```
$ sam package --template-file template.yaml --output-template-file packaged.yaml --s3-bucket [AWS SAM用のS3バケット名]
```

(3) デプロイ

パッケージ化したファイルをAWS上にデプロイします。

```
$ sam deploy --template-file packaged.yaml --capabilities CAPABILITY_IAM --stack-name [スタック名]
```

デプロイが完了したら以下のように表示されます。
`SimpleLodiApi` の値がサーバのURLとなります。

```
CloudFormation outputs from deployed stack
-------------------------------------------------------
Outputs
-------------------------------------------------------
Key                 SimpleLodiApi
Description         -
Value               https://xxxxxxxxxx.execute-api.[リージョン名].amazonaws.com/Prod/
```

## 利用ライブラリ

- [EasyRdf](http://www.easyrdf.org/)
- [JsonLD](https://github.com/lanthaler/JsonLD)
- [IRI](https://github.com/lanthaler/IRI)
- [Negotiation](http://williamdurand.fr/Negotiation/)
- [Twig](http://twig.sensiolabs.org/)
- [aws-sdk-php](https://github.com/aws/aws-sdk-php)

## 注意

dataフォルダにコピーするTurtle・RDF/XML・N-Triplesファイルは、必ず文字エンコードを**UTF8**としてください。
文字エンコードがUTF8以外の場合、文字化けやエラーの原因となります。  

Turtleファイルのみ、ファイルサイズが10KBを超える場合、読み込みに時間がかかるかもしれません。
10KBと超えるものは、RDF/XMLかN-Triplesを利用してください。
