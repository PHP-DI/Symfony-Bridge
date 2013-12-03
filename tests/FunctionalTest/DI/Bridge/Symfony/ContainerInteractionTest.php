<?php
/**
 * PHP-DI
 *
 * @link      http://php-di.org/
 * @copyright Matthieu Napoli (http://mnapoli.fr/)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace FunctionalTest\DI\Bridge\Symfony;

use DI\Bridge\Symfony\SymfonyContainerBridge;
use DI\ContainerBuilder;
use FunctionalTest\DI\Bridge\Symfony\Fixtures\Class1;
use FunctionalTest\DI\Bridge\Symfony\Fixtures\Class2;

class ContainerInteractionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test Get a PHP-DI entry from Symfony's container
     */
    public function symfonyGetInPHPDI()
    {
        $wrapper = new SymfonyContainerBridge();
//        $wrapper->set('FunctionalTest\DI\Bridge\Symfony\Fixtures\Class1', );

        $builder = new ContainerBuilder();
        $builder->wrapContainer($wrapper);
        $wrapper->setFallbackContainer($builder->build());

        // TODO
    }
    /**
     * @test Get a Symfony entry from PHP-DI's container
     */
    public function phpdiGetInSymfony()
    {
        $wrapper = new SymfonyContainerBridge();

        $class2 = new Class2();
        $wrapper->set('IntegrationTests\DI\Fixtures\Class2', $class2);

        $this->assertSame($class2, $wrapper->get('IntegrationTests\DI\Fixtures\Class2'));

        $builder = new ContainerBuilder();
        $builder->wrapContainer($wrapper);
        $wrapper->setFallbackContainer($builder->build());

        /** @var Class1 $class1 */
        $class1 = $wrapper->get('FunctionalTest\DI\Bridge\Symfony\Fixtures\Class1');

        $this->assertSame($class2, $class1->param1);
    }
}
