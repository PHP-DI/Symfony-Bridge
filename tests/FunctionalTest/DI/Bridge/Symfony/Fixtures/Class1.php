<?php

namespace FunctionalTest\DI\Bridge\Symfony\Fixtures;

class Class1
{
    public $param1;

    public function __construct(Class2 $param1)
    {
        $this->param1 = $param1;
    }
}
