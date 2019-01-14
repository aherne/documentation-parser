<?php
namespace Lucinda\DocumentationParser;
require_once("ClassDeclaration.php");
require_once("MethodDeclaration.php");
require_once("ParameterDeclaration.php");
require_once("CommentParser.php");

class ClassParser {
    private $results = array();

    public function __construct($folder) {
        $this->queue($folder);
        uasort($this->results, function($left, $right) {
            return strcmp($left->getName(), $right->getName());
        });
    }

    private function queue($folder) {
        $files = scandir($folder);
        foreach($files as $file) {
            if($file == "." || $file=="..") continue;
            $dir = $folder."/".$file;
            if(is_dir($dir)) {
                $this->queue($dir);
            } else {
                $this->parse($folder."/".$file);
            }
        }
    }

    private function parse($file) {
        $contents = file_get_contents($file);
        preg_match_all("/\s*((?:\/\*(?:[^*]|(?:\*[^\/]))*\*\/))*\s*\n\s*(abstract)*\s*(class|interface)[\s]*([a-zA-Z0-9]+)\s*(extends\s*[\\\a-zA-Z0-9]+)*\s*(implements\s*[^{]+)*/", $contents, $m1);
        if(isset($m1[4][0])) {
            $cp = new CommentParser($m1[1][0]);

            $cd = new ClassDeclaration();
            $cd->setDescription($cp->getDescription());
            $cd->setIsAbstract($m1[2][0]?true:false);
            $cd->setIsInterface($m1[3][0]=="interface"?true:false);
            $cd->setName($m1[4][0]);
            if($m1[5][0]) $cd->setExtends(trim(substr($m1[5][0],7)));
            if($m1[6][0]) {
                $pt = substr($m1[6][0],10);
                $implements = explode(",", $pt);
                foreach($implements as $className) {
                    $cd->setImplements(trim($className));
                }
            }
            preg_match_all("/\s*((?:\/\*(?:[^*]|(?:\*[^\/]))*\*\/))*\s*\n\s*(abstract)?\s*(public|protected)?\s*(static)?\s*function\s*([_a-zA-Z0-9]+)\s*\(([^)]+)*\)/", $contents, $m2);
            foreach($m2[5] as $i=>$methodName) {
                $mp = new CommentParser($m2[1][$i]);
                $parametersComments = $mp->getParameters();

                $md = new MethodDeclaration();
                $md->setDescription($mp->getDescription());
                $md->setName($methodName);
                $md->setIsAbstract(!empty($m2[2][$i])?true:false);
                $md->setIsPublic($m2[3][$i]=="protected"?false:true);
                $md->setIsStatic(!empty($m2[4][$i])?true:false);
                $md->setReturnType($mp->getReturns());
                $md->setThrows($mp->getThrows());
                $md->setOverrides($mp->getOverrides());

                $parameters = $m2[6][$i];
                if($parameters) {
                    preg_match_all("/\\$([a-zA-Z0-9]+)/", $parameters, $m3);
                    foreach($m3[1] as $parameter) {
                        $pd = new ParameterDeclaration();
                        $pd->setName($parameter);
                        if(isset($parametersComments[$parameter])) {
                            $pd->setType($parametersComments[$parameter]->getType());
                            $pd->setDescription($parametersComments[$parameter]->getDescription());
                        }
                        $md->addArgument($pd);
                    }
                }

                $cd->addMethod($md);
            }

            $this->results[$m1[4][0]] = $cd;
        }
    }

    public function getResults() {
        return $this->results;
    }
}
