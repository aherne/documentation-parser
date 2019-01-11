<?php
namespace Lucinda\DocumentationValidator;

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

    public function setExtends($extends)
    {
        $this->extends = $extends;
    }

    public function getImplements()
    {
        return $this->implements;
    }

    public function setImplements($implements)
    {
        $this->implements = $implements;
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function addMethod($method)
    {
        $this->methods[] = $method;
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