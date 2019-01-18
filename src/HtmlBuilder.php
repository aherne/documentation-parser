<?php
namespace Lucinda\DocumentationParser;

class HtmlBuilder {
    private $remoteLinks=[];
    private $remoteClasses=[];
    private $namespaces=[];

    public function __construct(ProjectParser $parser, $fileName, $remoteLinks=[], $remoteClasses=[]) {
        $this->remoteLinks = $remoteLinks;
        $this->remoteClasses = $remoteClasses;
        $html = $this->build($parser);
        $this->save($html, $fileName);
    }
    
    private function build(ProjectParser $parser) {
        $html = "";
        $results = $parser->getResults();
        foreach($results as $class) {
            $html.="
<h2 id=\"".$class->getName()."\">".$class->getName()."</h2>";
            // build description
            $description = $class->getDescription();
            preg_match_all("/\\n-\s*([^\\n]+)/", $description, $matches);
            if(!empty($matches[1])) {
                foreach($matches[0] as $val) {
                    $description = str_replace($val, "", $description);
                }
                $description = htmlentities($description)."\n<ul>";
                foreach($matches[1] as $val) {
                    $description .= "<li>".$val."</li>";
                }
                $description .= "</ul>";
                $html .="
<p>".$description."</p>";
            } else {
                $html.="
<p>".htmlentities($description)."</p>";
            }
            
            // build signature
            $signature = "";
            if($class->getIsAbstract()) {
                $signature .= 'abstract ';
            }
            $signature .= ($class->getIsInterface()?"interface":"class")." ".$class->getName()." ";
            if($class->getExtends()) {
                $signature .= 'extends '.$this->getClassLink($class->getExtends()).' ';
            }
            if($class->getImplements()) {
                $signature .= 'implements ';
                $interfaces = $class->getImplements();
                foreach($interfaces as $interface) {
                    $signature .= $this->getClassLink($interface).', ';
                }
                $signature = substr($signature, 0, -2);
                
            }
            $html.="
<h3>Signature</h3>
<p>".$signature."</p>";
            
            if(empty($class->getMethods())) continue; 
            $html.="
<h3>Methods</h3>
<table>
    <thead>
        <tr>
            <td>Method</td>
            <td>Arguments</td>
            <td>Returns</td>
            <td>Description</td>
        </tr>
    </thead>
    <tbody>
";
            
            $methods = $class->getMethods();
            foreach($methods as $method) {
                $overrideSignature = $method->getOverrides();
                if($method->getOverrides()) {
                    $method = $this->getInheritedMethod($parser, $overrideSignature);
                }
                $html.="
        <tr>
            <td>".$method->getName()."</td>
            <td>";
                // compile arguments
                $parameters = $method->getArguments();
                foreach($parameters as $parameter) {
                    $html.= "<i>".$this->getClassLink($parameter->getType())."</i> \$".$parameter->getName().",<br/>";
                }
                if(!empty($parameters)) $html = substr($html, 0, -6);
                $html.="
            </td>
            <td>";
                // compile return type
                if($method->getReturnType()) {
                    $type = $method->getReturnType()->getType();
                    $parts = explode("|", $type);
                    $addition = "";
                    foreach($parts as $part) {
                        $addition .= $this->getClassLink($part).'<br/>';
                    }
                    $html.= "<i>".substr($addition,0,-5)."</i>";
                }
                // compile description
                $html.="
            </td>
            <td>
                ".$method->getDescription();
                
                $extraDesc = [];
                if($method->getIsAbstract() && !$overrideSignature) {
                    $extraDesc[] = 'abstract';
                }
                if(!$method->getIsPublic()) {
                    $extraDesc[] = 'protected';
                }
                if($method->getIsStatic()) {
                    $extraDesc[] = 'static';
                }                
                if($overrideSignature) {
                    $extraDesc[]="overriding ".$overrideSignature->getMethod()."() @ ".$this->getClassLink($overrideSignature->getClass());
                }
                $throws = $method->getThrows();
                if($method->getThrows()) {
                    foreach($throws as $throw) {
                        $extraDesc[] = "throwing ".$this->getClassLink($throw->getType())." ".$throw->getDescription();
                    }
                }
                if(!empty($extraDesc)) {
                    $html .=". This method is: <ul>";
                    foreach($extraDesc as $val) {
                        $html .="<li>".$val."</li>";
                    }
                    $html .="</ul>";
                }
                $html.="
            </td>
        </tr>";
            }
            $html.="
    </tbody>
</table>";
        }
        return $html;
    }

    private function getInheritedMethod(ProjectParser $parser, $overrideSignature) {
        $results = $parser->getResults();
        $inheritedClass = null;
        if(isset($results[$overrideSignature->getClass()])) {
            $inheritedClass = $results[$overrideSignature->getClass()];
        } else if(isset($this->remoteClasses[$overrideSignature->getClass()])) {
            $inheritedClass = $this->remoteClasses[$overrideSignature->getClass()]->getInfo();
        } else {
            throw new \Exception("Inherited class not found: ".$overrideSignature->getClass());
        }
        $inheritedClassMethods = $inheritedClass->getMethods();
        $inheritedMethod = $inheritedClassMethods[$overrideSignature->getMethod()];
        // get namespace
        $namespace = ($overrideSignature->getClass(){0}=="\\"?substr($overrideSignature->getClass(),0, strrpos($overrideSignature->getClass(),"\\")):"");
        // append to arguments
        $arguments = $inheritedMethod->getArguments();
        foreach($arguments as $argument) {
            $type = $argument->getType();
            if(ctype_upper($type{0})) {
                $argument->setType($namespace."\\".$type);
            }
        }
        // replace return type
        $returnType = $inheritedMethod->getReturnType();
        if($returnType ) {
            $type = $returnType->getType();
            if(ctype_upper($type{0})) {
                $returnType->setType($namespace."\\".$type);
            }
        }

        // TODO: create table of contents
        // TODO: fix "Exception" class when there is no namespace
        return $inheritedMethod;
    }

    private function getClassLink($className) {
        if(strpos($className, "\\")===0) {
            preg_match("/(.*)\\\([^\\\]+)/", $className, $matches);
            $namespace = $matches[1];
            if(isset($this->remoteLinks[$namespace])) {
                preg_match("/([\\\a-zA-Z0-9]+)(\[*[a-z]*\]*)/", $matches[2], $m2);
                if($m2[2]) {
                    return '<a href="'.$this->remoteLinks[$namespace].'#'.$m2[1].'">'.$namespace.'\\'.$m2[1].'</a>'.$m2[2];
                } else {
                    return '<a href="'.$this->remoteLinks[$namespace].'#'.$m2[1].'">'.$namespace.'\\'.$m2[1].'</a>';
                }
            } else if($className=="\\Exception") {
                return '<a href="http://php.net/manual/ro/class.exception.php" target="_blank">'.$className.'</a>';
            } else if($className=="\\SimpleXMLElement") {
                return '<a href="http://php.net/manual/ro/class.simplexmlelement.php" target="_blank">'.$className.'</a>';
            } else if($className=="\\SimpleXMLElement[string]") {
                return '<a href="http://php.net/manual/ro/class.simplexmlelement.php" target="_blank">\SimpleXMLElement</a>[string]';
            } else if($className=="\\SessionHandlerInterface") {
                return '<a href="http://php.net/manual/ro/class.sessionhandlerinterface.php" target="_blank">'.$className.'</a>';
            } else if($className=="\\PDO") {
                return '<a href="http://php.net/manual/en/book.pdo.php" target="_blank">'.$className.'</a>';
            } else if($className=="\\PDOStatement") {
                return '<a href="http://php.net/manual/en/class.pdostatement.php" target="_blank">'.$className.'</a>';
            } else if($className=="\\CouchbaseBucket") {
                return '<a href="http://docs.couchbase.com/sdk-api/couchbase-php-client-2.0.1/classes/CouchbaseBucket.html" target="_blank">'.$className.'</a>';
            } else if($className=="\\Memcache") {
                return '<a href="http://php.net/manual/en/class.memcache.php" target="_blank">'.$className.'</a>';
            } else if($className=="\\Memcached") {
                return '<a href="http://php.net/manual/en/class.memcached.php" target="_blank">'.$className.'</a>';
            } else if($className=="\\Redis") {
                return '<a href="https://github.com/phpredis/phpredis" target="_blank">'.$className.'</a>';
            } else if($className=="\\RedisCluster") {
                return '<a href="https://github.com/phpredis/phpredis/blob/develop/cluster.markdown" target="_blank">'.$className.'</a>';
            } else {
                throw new \Exception("Unknown class: ".$className);
            }
        } else if(ctype_upper($className{0})) {
            preg_match("/([\\\a-zA-Z0-9]+)(\[*[a-z]*\]*)/", $className, $m2);
            if($m2[2]) {
                return '<a href="#'.$m2[1].'">'.$m2[1].'</a>'.$m2[2];
            } else {
                return '<a href="#'.$m2[1].'">'.$m2[1].'</a>';
            }
        } else {
            return $className;
        }
    }
    
    private function save($html, $fileName) {
        file_put_contents($fileName, $html);
    }
}