<?php
namespace Lucinda\DocumentationParser;

class HtmlBuilder {
    public function __construct(ClassParser $parser, $fileName) {
        $html = $this->build($parser);
        $this->save($html, $fileName);
    }
    
    private function build(ClassParser $parser) {
        $html = "";
        $results = $parser->getResults();
        foreach($results as $class) {  
            // TODO: strip documentation of lists
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
                if($class->getExtends()=="\\Exception") {
                    $signature .= 'extends <a href="http://php.net/manual/ro/class.exception.php" target="_blank">'.$class->getExtends().'</a> ';
                } else {
                    $signature .= 'extends <a href="#'.$class->getExtends().'">'.$class->getExtends().'</a> ';
                }
            }
            if($class->getImplements()) {
                $signature .= 'implements ';
                $interfaces = $class->getImplements();
                foreach($interfaces as $interface) {
                    $signature .= '<a href="#'.$interface.'">'.$interface.'</a>, ';
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
                    $tempMethod = $this->getInheritedMethod($parser, $overrideSignature);
                    if($tempMethod) $method = $tempMethod;
                }
                $html.="
        <tr>
            <td>".$method->getName()."</td>
            <td>";
                // compile arguments
                $parameters = $method->getArguments();
                foreach($parameters as $parameter) {
                    $type = $parameter->getType();
                    if(preg_match("/^[A-Z]+[a-zA-Z]+$/", $type)) {
                        $type = '<a href="#'.$type.'">'.$type.'</a>';
                    }
                    $html.= "<i>".$type."</i> \$".$parameter->getName().",<br/>";
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
                        if(preg_match("/^[A-Z]+[a-zA-Z]+$/", $part)) {
                            $addition .= '<a href="#'.$type.'">'.$part.'</a><br/>';
                        } else {
                            $addition .= $part."<br/>";
                        }
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
                    $extraDesc[]="overriding ".$overrideSignature->getMethod()."() @ <a href=\"#".$overrideSignature->getClass()."\">".$overrideSignature->getClass()."</a>";
                }
                $throws = $method->getThrows();
                if($method->getThrows()) {
                    foreach($throws as $throw) {
                        $type = $throw->getType();
                        $extraDesc[] = "throwing <a href=\"#".$type."\">".$type."</a> ".$throw->getDescription();
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
    
    private function getInheritedMethod(ClassParser $parser, $overrideSignature) {
        $results = $parser->getResults();
        if(!isset($results[$overrideSignature->getClass()])) return;
        $inheritedClass = $results[$overrideSignature->getClass()];
        $inheritedClassMethods = $inheritedClass->getMethods();
        return $inheritedClassMethods[$overrideSignature->getMethod()];
    }
    
    private function save($html, $fileName) {
        file_put_contents($fileName, $html);
    }
}