<?php
namespace Lucinda\DocumentationParser;

class OverridesDeclaration {
    private $class;
    private $method;
    
    public function getClass()
    {
        return $this->class;
    }
    
    public function setClass($class)
    {
        $this->class = $class;
    }
    
    public function getMethod()
    {
        return $this->method;
    }
    
    public function setMethod($method)
    {
        $this->method = $method;
    }
}