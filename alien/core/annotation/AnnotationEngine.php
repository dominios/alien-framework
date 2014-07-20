<?php

namespace Alien\Annotaion;

class AnnotationEngine {

    public function getMethodAnnotaions($object, $method) {
        if (!mb_strlen($method)) {
            throw new \InvalidArgumentException();
        }
        $rc = new \ReflectionClass($object);
        $m = $rc->getMethod($method);
        $annotations = $this->parseBlock($m->getDocComment());
        foreach ($annotations as $annotation) {
            $a = new $annotation;
        }

    }

    protected function parseLine($line) {
        $line = trim($line);
        if (strpos($line, '@')) {
            $line = preg_replace('/^\*\s*@/', '', $line);
//            return new Annotation($line);
            return $line;
        }
    }

    protected function parseBlock($block) {
        $annotations = array();
        $block = str_replace("/**", "", $block);
        $block = str_replace("*/", "", $block);
        $lines = explode("\n", trim($block));
        foreach ($lines as $l) {
            $a = $this->parseLine($l);
//            if ($a instanceof Annotation) {
            $annotations[] = $a;
//            }
        }
        return $annotations;
    }
}