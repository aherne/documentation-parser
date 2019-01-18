<?php
use Lucinda\DocumentationParser\HtmlBuilder;

require_once("src/ProjectParser.php");
require_once("src/Validator.php");
require_once("src/HtmlBuilder.php");

//$libraries = [
//    "http-caching-api",
//    "php-view-language-api",
//    "php-sql-data-access-api",
//    "php-nosql-data-access-api",
//    "php-logging-api",
//    "php-request-validator",
//    "php-internationalization-api",
//    "php-security-api",
//    "oauth2client",
//    "errors-api",
//    "php-servlets-api"
//];
//foreach($libraries as $library) {
//    echo "Parsing library: ".$library;
//    $parser = new Lucinda\DocumentationParser\ProjectParser("/home/aherne/git/".$library."/src");
//    $validator = new Lucinda\DocumentationParser\Validator($parser);
//    if($validator->hasPassed()) {
//        new HtmlBuilder($parser, __DIR__."/".$library.".html");
//        echo " OK\n";
//    } else {
//        var_dump($validator->getErrors());
//        echo " \n";
//    }
//}
//
echo "Parsing library: lucinda-framework-engine";
$parser = new Lucinda\DocumentationParser\ProjectParser("/home/aherne/git/lucinda-framework-engine/src");
$validator = new Lucinda\DocumentationParser\Validator($parser);
if($validator->hasPassed()) {
    new HtmlBuilder($parser, __DIR__."/lucinda-framework-engine.html", [
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
    echo " OK\n";
} else {
    var_dump($validator->getErrors());
    echo " \n";
}

//echo "Parsing library: lucinda-framework";
//$parser = new Lucinda\DocumentationParser\ProjectParser("/home/aherne/git/lucinda-framework/application");
//$validator = new Lucinda\DocumentationParser\Validator($parser);
//if($validator->hasPassed()) {
//    new HtmlBuilder($parser, __DIR__."/lucinda-framework.html", [
//        "\OAuth2"=>"/oauth2-client/reference-guide",
//        "\Lucinda\Framework"=>"/framework-engine/reference-guide",
//        "\Lucinda\MVC\STDOUT"=>"/stdout-mvc/reference-guide",
//        "\Lucinda\MVC\STDERR"=>"/stderr-mvc/reference-guide",
//        "\Lucinda\Templating"=>"/view-language/reference-guide",
//        "\Lucinda\RequestValidator"=>"/request-validation/reference-guide",
//        "\Lucinda\Logging"=>"/logging/reference-guide",
//        "\Lucinda\WebSecurity"=>"/web-security/reference-guide",
//        "\Lucinda\SQL"=>"/sql-data-access/reference-guide",
//        "\Lucinda\NoSQL"=>"/nosql-data-access/reference-guide",
//        "\Lucinda\Caching"=>"/http-caching/reference-guide",
//        "\Lucinda\Internationalization"=>"/internationalization/reference-guide"
//    ], [
//        "\OAuth2\Driver"=>new Lucinda\DocumentationParser\ClassParser("/home/aherne/git/oauth2client/src/Driver.php"),
//        "\OAuth2\ResponseWrapper"=>new Lucinda\DocumentationParser\ClassParser("/home/aherne/git/oauth2client/src/ResponseWrapper.php"),
//        "\Lucinda\Framework\CacheableDriver"=>new Lucinda\DocumentationParser\ClassParser("/home/aherne/git/lucinda-framework-engine/src/caching/CacheableDriver.php"),
//        "\Lucinda\Framework\AbstractLoggerWrapper"=>new Lucinda\DocumentationParser\ClassParser("/home/aherne/git/lucinda-framework-engine/src/logging/AbstractLoggerWrapper.php"),
//        "\Lucinda\Framework\LogReporter"=>new Lucinda\DocumentationParser\ClassParser("/home/aherne/git/lucinda-framework-engine/src/error_reporting/LogReporter.php"),
//        "\Lucinda\MVC\STDERR\ErrorHandler"=>new Lucinda\DocumentationParser\ClassParser("/home/aherne/git/errors-api/src/ErrorHandler.php"),
//        "\Lucinda\MVC\STDERR\ErrorRenderer"=>new Lucinda\DocumentationParser\ClassParser("/home/aherne/git/errors-api/src/ErrorRenderer.php"),
//        "\Lucinda\MVC\STDERR\Controller"=>new Lucinda\DocumentationParser\ClassParser("/home/aherne/git/errors-api/src/Controller.php"),
//        "\Lucinda\MVC\STDOUT\Runnable"=>new Lucinda\DocumentationParser\ClassParser("/home/aherne/git/php-servlets-api/src/Runnable.php"),
//        "\Lucinda\MVC\STDOUT\ViewResolver"=>new Lucinda\DocumentationParser\ClassParser("/home/aherne/git/php-servlets-api/src/response/ViewResolver.php"),
//        "\Lucinda\WebSecurity\OAuth2Driver"=>new Lucinda\DocumentationParser\ClassParser("/home/aherne/git/php-security-api/src/authentication/OAuth2Driver.php"),
//        "\Lucinda\WebSecurity\OAuth2UserInformation"=>new Lucinda\DocumentationParser\ClassParser("/home/aherne/git/php-security-api/src/authentication/OAuth2UserInformation.php")
//    ]);
//    echo " OK";
//} else {
//    var_dump($validator->getErrors());
//}