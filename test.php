<?php
use Lucinda\DocumentationParser\HtmlBuilder;

require_once("src/ProjectParser.php");
require_once("src/Validator.php");
require_once("src/HtmlBuilder.php");
// "http-caching-api","php-view-language-api","php-sql-data-access-api","php-nosql-data-access-api","php-logging-api","php-request-validator","php-internationalization-api","php-security-api","oauth2client","errors-api","php-servlets-api"
// TODO: solve ErrorReporter[] links
// TODO: solve \SimpleXMLElement  response or args
// TODO: strip documentation of lists
$libraries = ["lucinda-framework-engine"];
foreach($libraries as $library) {
    $parser = new Lucinda\DocumentationParser\ProjectParser("/home/aherne/git/".$library."/src");
    $validator = new Lucinda\DocumentationParser\Validator($parser);
    if($validator->hasPassed()) {
        new HtmlBuilder($parser, __DIR__."/".$library.".html", [
            "\OAuth2"=>"/oauth2-client/reference-guide",
            "\Lucinda\Framework"=>"/framework-engine/reference-guide",
            "\Lucinda\MVC\STDOUT"=>"/stdout-mvc/reference-guide",
            "\Lucinda\MVC\STDERR"=>"/stderr-mvc/reference-guide",
            "\Lucinda\Templating"=>"/view-language/reference-guide",
            "\Lucinda\RequestValidator"=>"/request-validation/reference-guide",
            "\Lucinda\Logging"=>"/logging/reference-guide",
            "\Lucinda\WebSecurity"=>"/web-security/reference-guide",
            "\Lucinda\SQL"=>"/sql-data-access/reference-guide",
            "\Lucinda\NoSQL"=>"/nosql-data-access/reference-guide",
            "\Lucinda\Caching"=>"/http-caching/reference-guide",
            "\Lucinda\Internationalization"=>"/internationalization/reference-guide"
        ], [
            "\Lucinda\Caching\Cacheable"=>new Lucinda\DocumentationParser\ClassParser("/home/aherne/git/http-caching-api/src/Cacheable.php"),
            "\Lucinda\MVC\STDERR\ErrorReporter"=>new Lucinda\DocumentationParser\ClassParser("/home/aherne/git/errors-api/src/ErrorReporter.php"),
            "\Lucinda\Logging\Logger"=>new Lucinda\DocumentationParser\ClassParser("/home/aherne/git/php-logging-api/src/Logger.php")
        ]);
        echo "OK";
    } else {
        var_dump($validator->getErrors());
    }
}