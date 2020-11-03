<?php
namespace Jankx\Breadcrumb;

class Compatibles {
    protected $callable;

    public static function detect() {
        $dectector = new static();
        $dectector->findCallable();

        return $dectector;
    }

    public function findCallable() {
    }

    public function getCallable() {
        return $this->callable;
    }
}
