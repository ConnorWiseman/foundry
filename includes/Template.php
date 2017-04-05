<?php

namespace Foundry;

class Template {

    private $contents;

    private function readFile($fileName, $path) {
        if (!strpos($fileName, '.')) {
            $fileName = "{$fileName}.html";
        }
        $str = @file_get_contents(realpath("{$path}/{$fileName}"));
        if ($str === FALSE) {
            $class = get_class($this);
            throw new \Exception("Cannot load {$class} {$fileName}");
        }
        return $str;
    }

    public function __construct($path, $dir, $fileName) {
        $this->contents = $this->readFile($fileName, "{$path}{$dir}");
    }

    public function getContents() {
        return $this->contents;
    }

}
