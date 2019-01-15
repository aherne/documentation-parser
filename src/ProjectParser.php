<?php
namespace Lucinda\DocumentationParser;

require_once("ClassParser.php");

class ProjectParser {
    private $results = array();

    public function __construct($folder) {
        $this->setResults($folder);
        ksort($this->results);
    }

    private function setResults($folder) {
        $files = scandir($folder);
        foreach($files as $file) {
            if($file == "." || $file=="..") continue;
            $dir = $folder."/".$file;
            if(is_dir($dir)) {
                $this->setResults($dir);
            } else {
                $cp = new ClassParser($folder."/".$file);
                $classInfo = $cp->getInfo();
                if($classInfo) {
                    $this->results[$classInfo->getName()] = $classInfo;
                } else {
                    echo("No class found: ".$folder."/".$file."\n");
                }
            }
        }
    }

    public function getResults() {
        return $this->results;
    }
}
