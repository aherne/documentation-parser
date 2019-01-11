<?php
namespace Lucinda\DocumentationValidator;
require_once("ClassDeclaration.php");
require_once("MethodDeclaration.php");
require_once("ParameterDeclaration.php");
require_once("CommentParser.php");

class ClassParser {
    private $results = array();

    public function __construct($folder) {
        $this->queue($folder);
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
        preg_match_all("/\s*((?:\/\*(?:[^*]|(?:\*[^\/]))*\*\/))*\s*\n\s*(abstract)*\s*(class|interface)[\s]*([a-zA-Z]+)\s*(extends|implements)*\s*([\\a-zA-Z]+)*/", $contents, $m1);
        if(isset($m1[4][0])) {
            $cp = new CommentParser($m1[1][0]);

            $cd = new ClassDeclaration();
            $cd->setDescription($cp->getDescription());
            $cd->setIsAbstract($m1[2][0]?true:false);
            $cd->setIsInterface($m1[3][0]=="interface"?true:false);
            $cd->setName($m1[4][0]);
            if($m1[5][0]=="extends") $cd->setExtends($m1[6][0]);
            else if($m1[5][0]=="implements") $cd->setImplements($m1[6][0]);
            preg_match_all("/\s*((?:\/\*(?:[^*]|(?:\*[^\/]))*\*\/))*\s*\n\s*(abstract)?\s*(public)?\s*(static)?\s*function\s*([_a-zA-Z]+)\s*\(([^)]+)*\)/", $contents, $m2);
            foreach($m2[5] as $i=>$methodName) {
                $mp = new CommentParser($m2[1][$i]);
                $parametersComments = $mp->getParameters();

                $md = new MethodDeclaration();
                $md->setDescription($mp->getDescription());
                $md->setName($methodName);
                $md->setIsAbstract(!empty($m2[2][$i])?true:false);
                $md->setIsStatic(!empty($m2[4][$i])?true:false);
                $md->setReturnType($mp->getReturns());
                $md->setThrows($mp->getThrows());

                $parameters = $m2[6][$i];
                if($parameters) {
                    preg_match_all("/\\$([a-zA-Z]+)/", $parameters, $m3);
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

            $this->results[] = $cd;
        }
    }

    public function getResults() {
        return $this->results;
    }
}
