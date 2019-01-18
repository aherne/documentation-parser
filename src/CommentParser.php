<?php
namespace Lucinda\DocumentationParser;
require_once("ParameterDeclaration.php");
require_once("ReturnDeclaration.php");
require_once("ThrowsDeclaration.php");
require_once("OverridesDeclaration.php");

class CommentParser {
    private $description;
    private $parameters=[];
    private $returns;
    private $throws=[];
    private $overrides;

    public function __construct($comment) {
        if(!$comment) return;
        $comment = trim(str_replace(["/**","*/"," * ","\r"],"", $comment));
        $lines = explode("\n", $comment);
        foreach($lines as $line) {
            $this->parseLine(trim($line));
        }
    }

    private function parseLine($line) {
        if(strpos($line, "@")!==0 && $line!="*") {
            $this->description .= $line."\n";
            return;
        }
        preg_match("/@param\s+([^\s]+)\s+\\$([a-zA-Z]+)\s*(.*)/", $line, $m1);
        if(sizeof($m1)>0) {
            $param = new ParameterDeclaration();
            $param->setName($m1[2]);
            $param->setType($m1[1]);
            $param->setDescription(trim($m1[3]));
            $this->parameters[$m1[2]] = $param;

        }
        preg_match("/@return\s+([^\s]+)\s*(.*)/", $line, $m2);
        if(sizeof($m2)>0) {
            $rd = new ReturnDeclaration();
            $rd->setType($m2[1]);
            $rd->setDescription(trim($m2[2]));
            $this->returns = $rd;
        }
        preg_match("/@throws\s+([^\s]+)\s*(.*)/", $line, $m3);
        if(sizeof($m3)>0) {
            $td = new ThrowsDeclaration();
            $td->setType($m3[1]);
            $td->setDescription(trim($m3[2]));
            $this->throws[] = $td;
        }
        preg_match("/@see\s+([^\:]+)::([a-zA-Z]+)/", $line, $m4);
        if(sizeof($m4)>0) {
            $od = new OverridesDeclaration();
            $od->setClass($m4[1]);
            $od->setMethod(trim($m4[2]));
            $this->overrides = $od;
        }
    }

    public function getDescription() {
        return $this->description;
    }

    public function getParameters() {
        return $this->parameters;
    }

    public function getReturns() {
        return $this->returns;
    }

    public function getThrows() {
        return $this->throws;
    }
    
    public function getOverrides() {
        return $this->overrides;
    }
}