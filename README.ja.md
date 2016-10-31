# Simple LODI : Simple Linked Open Data Interface

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

- PHP 5.4以上
- mod_rewrite

## デモ

<http://uedayou.net/simplelodi/uedayou>

HTML出力  
<http://uedayou.net/simplelodi/uedayou.html>

Turtle出力  
<http://uedayou.net/simplelodi/uedayou.ttl>

XML出力  
<http://uedayou.net/simplelodi/uedayou.xml>

JSON出力  
<http://uedayou.net/simplelodi/uedayou.json>

JSON-LD出力  
<http://uedayou.net/simplelodi/uedayou.jsonld>

## インストール・使い方(Turtleファイルの場合)

(1) simplelodi フォルダをWebサーバ上の任意の場所にコピーしてください。  
(2) simplelodi フォルダをリネームしてください(たとえば、resource など)  
(3) data フォルダに　サンプルファイル(data/uedayou.ttl)を参考にTurtle ファイルを作成し、拡張子を`ttl`としてコピーしてください。  
(4) `http:// ... /resource/[拡張子を省略したファイル名]` をブラウザで開くと、HTMLが表示されます。 たとえば uedayou.ttl の場合は、 `http:// ... /resource/uedayou` となります  
(5) 対応する拡張子(対応フォーマット節　参照)をつけると、出力フォーマットを変更できます。 

※ RDF/XMLであれば拡張子をxmlに、N-Triplesであれば拡張子をntとしてください。

あわせて、こちらもご覧ください。　　

[比較的簡単にDBpediaのようにLinked Open Data(LOD)を公開する方法](http://qiita.com/uedayou/items/d66b7c406f1f231347f5)

## 利用ライブラリ

- [EasyRdf](http://www.easyrdf.org/)
- [JsonLD](https://github.com/lanthaler/JsonLD)
- [IRI](https://github.com/lanthaler/IRI)
- [Negotiation](http://williamdurand.fr/Negotiation/)
- [Twig](http://twig.sensiolabs.org/)

## 注意

dataフォルダにコピーするTurtle・RDF/XML・N-Triplesファイルは、必ず文字エンコードを**UTF8**としてください。
文字エンコードがUTF8以外の場合、文字化けやエラーの原因となります。  

Turtleファイルのみ、ファイルサイズが10KBを超える場合、読み込みに時間がかかるかもしれません。
10KBと超えるものは、RDF/XMLかN-Triplesを利用してください。
