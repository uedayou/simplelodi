# Simple LODI : Simple Linked Open Data Interface(v2.1.0)

[![Test](https://github.com/uedayou/simplelodi/workflows/Test/badge.svg)](https://github.com/uedayou/simplelodi/actions?query=workflow%3ATest)

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

- PHP 7.2.5 or later
- mod_rewrite

## Examples

<http://uedayou.net/simplelodi-v2/uedayou>

HTML  
<http://uedayou.net/simplelodi-v2/uedayou.html>

Turtle  
<http://uedayou.net/simplelodi-v2/uedayou.ttl>

RDF/XML  
<http://uedayou.net/simplelodi-v2/uedayou.xml>

JSON  
<http://uedayou.net/simplelodi-v2/uedayou.json>

JSON-LD  
<http://uedayou.net/simplelodi-v2/uedayou.jsonld>

## Installation

(1) Download "simplelodi".

(2) Put it on your web server.
    You can change the folder name as you want.

(3) Put your RDF file(Turtle, RDF/XML or N-Triples) in `src/data` folder.

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


## How to deploy on Amazon Web Service

It can deploy on Amazon Web Service's serverless environments.
It will be created like below:

```
Amazon API Gateway -- AWS Lambda
```

(1) Install AWS CLI and AWS SAM CLI

<https://aws.amazon.com/cli/>
<https://aws.amazon.com/serverless/sam/>

(2) Create the SAM package

Create the package of SAM by the following command.

```
$ sam package --template-file template.yaml --output-template-file packaged.yaml --s3-bucket [your_s3_bucket_for_aws_sam]
```

(3) Deploy

Deploy the package on AWS.

```
$ sam deploy --template-file packaged.yaml --capabilities CAPABILITY_IAM --stack-name [your_stack_name]
```

After completing deployment, The URL of API Gateway will be shown.

```
CloudFormation outputs from deployed stack
-------------------------------------------------------
Outputs
-------------------------------------------------------
Key                 SimpleLodiApi
Description         -
Value               https://xxxxxxxxxx.execute-api.[region].amazonaws.com/Prod/
```

## Notice

If your Turtle file size is bigger than 10 kB, it may take a long time to load it. In this case, please convert your Turtle file to RDF/XML or N-Triples format in advance. And then please do it again.
