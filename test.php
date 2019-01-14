<?php
use Lucinda\DocumentationParser\HtmlBuilder;

require_once("src/ClassParser.php");
require_once("src/Validator.php");
require_once("src/HtmlBuilder.php");
// "http-caching-api","php-view-language-api","php-sql-data-access-api","php-nosql-data-access-api","php-logging-api","php-request-validator","php-internationalization-api","php-security-api","oauth2client","errors-api","php-servlets-api"
// TODO: solve ErrorReporter[] links
// TODO: solve \SimpleXMLElement  response or args
$libraries = ["lucinda-framework-engine"];
foreach($libraries as $library) {
    $parser = new Lucinda\DocumentationParser\ClassParser("/home/aherne/git/".$library."/src");
    $validator = new Lucinda\DocumentationParser\Validator($parser);
    if($validator->hasPassed()) {
        new HtmlBuilder($parser, __DIR__."/".$library.".html");
        echo "OK";
    } else {
        var_dump($validator->getErrors());
    }
}