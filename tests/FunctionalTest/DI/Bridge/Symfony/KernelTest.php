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
use FunctionalTest\DI\Bridge\Symfony\Fixtures\Class1;
use FunctionalTest\DI\Bridge\Symfony\Fixtures\Class2;

/**
 * @coversNothing
 */
class KernelTest extends AbstractFunctionalTest
{
    /**
     * @test
     */
    public function kernel_should_boot()
    {
        $kernel = $this->createKernel();

        $this->assertTrue($kernel->getContainer() instanceof SymfonyContainerBridge);
    }

    /**
     * @test
     */
    public function phpdi_should_resolve_classes()
    {
        $kernel = $this->createKernel();

        $object = $kernel->getContainer()->get('FunctionalTest\DI\Bridge\Symfony\Fixtures\Class1');
        $this->assertTrue($object instanceof Class1);
    }

    /**
     * @test
     */
    public function symfony_should_resolve_classes()
    {
        $kernel = $this->createKernel('class2.yml');

        $object = $kernel->getContainer()->get('class2');
        $this->assertTrue($object instanceof Class2);
    }
}
