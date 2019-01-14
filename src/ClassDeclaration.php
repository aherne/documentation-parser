<?php
namespace Lucinda\DocumentationParser;

class ClassDeclaration {
    private $isAbstract;
    private $isInterface;
    private $name;
    private $extends;
    private $implements;
    private $methods = array();
    private $description;

    public function getIsAbstract()
    {
        return $this->isAbstract;
    }

    public function setIsAbstract($isAbstract)
    {
        $this->isAbstract = $isAbstract;
    }

    public function getIsInterface()
    {
        return $this->isInterface;
    }

    public function setIsInterface($isInterface)
    {
        $this->isInterface = $isInterface;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getExtends()
    {
        return $this->extends;
    }

    public function setExtends($className)
    {
        $this->extends = $className;
    }

    public function getImplements()
    {
        return $this->implements;
    }

    public function setImplements($className)
    {
        $this->implements[] = $className;
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function addMethod($method)
    {
        $this->methods[$method->getName()] = $method;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }
}