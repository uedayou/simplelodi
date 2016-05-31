# Simple LODI : Simple Linked Open Data Interface

コンテントネゴシエーションや拡張子変更により、RDF(Turtle)ファイルをさまざまなフォーマットで出力ができるLODフロントエンドプログラムです。

## 対応フォーマット(拡張子)

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

JSON-LD出力  
<http://uedayou.net/simplelodi/uedayou.jsonld>

## インストール・使い方

(1) simplelodi フォルダをWebサーバ上の任意の場所にコピーしてください。  
(2) simplelodi フォルダをリネームしてください(たとえば、resource など)  
(3) data フォルダに　サンプルファイル(data/uedayou.ttl)を参考にTurtle ファイルを作成し、コピーしてください。  
(4) http:// ... /resource/[拡張子を省略したファイル名] をブラウザで開くと、HTMLが表示されます。  
※ たとえば uedayou.ttl の場合は、 http:// ... /resource/uedayou となります  
(5) 対応する拡張子(対応フォーマット節　参照)をつけると、出力フォーマットを変更できます。  

## 利用ライブラリ

- [EasyRdf](http://www.easyrdf.org/)
- [JsonLD](https://github.com/lanthaler/JsonLD)
- [IRI](https://github.com/lanthaler/IRI)
- [Negotiation](http://williamdurand.fr/Negotiation/)
- [Twig](http://twig.sensiolabs.org/)

## 注意

Turtleファイルが10KBを超える場合、変換に時間がかかるかもしれません。
