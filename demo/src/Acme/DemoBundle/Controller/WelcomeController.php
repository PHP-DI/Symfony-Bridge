<?php

namespace Acme\DemoBundle\Controller;

use Acme\DemoBundle\Service\FooService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WelcomeController extends Controller
{
    public function indexAction()
    {
        /** @var FooService $foo */
        $foo = $this->get('foo');

        $result = $foo->getBar()->compute();

        return $this->render('AcmeDemoBundle:Welcome:index.html.twig', array(
            'result' => $result,
        ));
    }
}
