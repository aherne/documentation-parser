<?php
namespace Lucinda\DocumentationParser;

class Validator
{
    private $errors=[];

    public function __construct(ClassParser $parser) {
        $this->validate($parser);
    }

    private function validate(ClassParser $parser) {
        $results = $parser->getResults();
        foreach($results as $class) {
            if(!$class->getDescription()) {
                $this->errors[]="Class ".$class->getName()." lacks description";
            }

            $methods = $class->getMethods();
            foreach($methods as $method) {
                if($method->getOverrides()) {
                    continue;
                }
                if(!$method->getDescription()) {
                    $this->errors[]="Method ".$method->getName()." @ ".$class->getName()." lacks description";
                }
                if($method->getReturnType()) {
                    if(!$method->getReturnType()->getType()) {
                        $this->errors[]="Method ".$method->getName()." @ ".$class->getName()." lacks return type";
                    }
                }
                $parameters = $method->getArguments();
                foreach($parameters as $parameter) {
                    if(!$parameter->getType()) {
                        $this->errors[]="Parameter ".$parameter->getName()." @ ".$method->getName()." @ ".$class->getName()." lacks type";
                    }
                }
                $throws = $method->getThrows();
                foreach($throws as $throw) {
                    if(!$throw->getDescription()) {
                        $this->errors[]="Throws ".$throw->getType()." @ ".$method->getName()." @ ".$class->getName()." lacks description";
                    }
                }
            }
        }
    }

    public function hasPassed() {
        return sizeof($this->errors)==0;
    }

    public function getErrors() {
        return $this->errors;
    }

}