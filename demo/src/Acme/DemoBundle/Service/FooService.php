<?php

namespace Acme\DemoBundle\Service;

class FooService
{
    private $bar;

    public function __construct(BarService $bar)
    {
        $this->bar = $bar;
    }

    public function getBar()
    {
        return $this->bar;
    }
}
