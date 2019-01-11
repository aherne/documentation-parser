<?php
namespace Lucinda\DocumentationValidator;
class MethodDeclaration {
    private $isStatic;
    private $isAbstract;
    private $name;
    private $arguments = array();
    private $description;
    private $returnType;
    private $throws = array();

    public function getThrows()
    {
        return $this->throws;
    }

    public function setThrows($throws)
    {
        $this->throws = $throws;
    }

    public function getReturnType()
    {
        return $this->returnType;
    }

    public function setReturnType($returnType)
    {
        $this->returnType = $returnType;
    }

    public function getisStatic()
    {
        return $this->isStatic;
    }

    public function setIsStatic($isStatic)
    {
        $this->isStatic = $isStatic;
    }

    public function getisAbstract()
    {
        return $this->isAbstract;
    }

    public function setIsAbstract($isAbstract)
    {
        $this->isAbstract = $isAbstract;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function addArgument($argument)
    {
        $this->arguments[] = $argument;
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