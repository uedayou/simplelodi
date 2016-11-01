# Simple LODI : Simple Linked Open Data Interface

"Simple LODI” is a PHP program which can convert RDF data to various formats. It contributes to make Linked Data.

## Input file extensions

- Turtle(.ttl)
- N-Triples(.nt)
- RDF/XML(.rdf/.xml)

## Output file extensions:

- HTML(.html)
- RDF/XML(.rdf/.xml)
- JSON(.json)
- JSON-LD(.jsonld)
- Turtle(.ttl)
- Notation3(.n3)
- N-Triples(.nt)

## Requirements:

- PHP 5.4 or later
- mod_rewrite

## Examples

<http://uedayou.net/simplelodi/uedayou>

HTML  
<http://uedayou.net/simplelodi/uedayou.html>

Turtle  
<http://uedayou.net/simplelodi/uedayou.ttl>

RDF/XML  
<http://uedayou.net/simplelodi/uedayou.xml>

JSON  
<http://uedayou.net/simplelodi/uedayou.json>

JSON-LD  
<http://uedayou.net/simplelodi/uedayou.jsonld>

## Installation

(1) Download "simplelodi".

(2) Put it on your web server.
    You can change the folder name as you want.

(3) Put your RDF file(Turtle, RDF/XML or N-Triples) in `data` folder.

(4) Run your browser and enter the following URL:  
`http:// ... /your-folder-name/your-file-name(file name without the extension)`

(5) You can get various kinds of files by adding the file-extension you want.

ex.

HTML  
`http://your-domain.com/your-folder-name/your-file-name.html`

Turtle  
`http://your-domain.com/your-folder-name/your-file-name.ttl`

RDF/XML  
`http://your-domain.com/your-folder-name/your-file-name.xml`

JSON  
`http://your-domain.com/your-folder-name/your-file-name.json`

JSON-LD  
`http://your-domain.com/your-folder-name/your-file-name.jsonld`


## Notice

If your Turtle file size is bigger than 10 kB, it may take a long time to load it. In this case, please convert your Turtle file to RDF/XML or N-Triples format in advance. And then please do it again.
