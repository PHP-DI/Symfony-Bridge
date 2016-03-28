<?php
/**
 * PHP-DI
 *
 * @link      http://php-di.org/
 * @copyright Matthieu Napoli (http://mnapoli.fr/)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace FunctionalTest\DI\Bridge\Symfony;

use FunctionalTest\DI\Bridge\Symfony\Fixtures\ContainerAwareController;

/**
 * @coversNothing
 */
class ContainerAwareInterfaceTest extends AbstractFunctionalTest
{
    /**
     * @link https://github.com/PHP-DI/Symfony2-Bridge/issues/2
     */
    public function testContainerAware()
    {
        $kernel = $this->createKernel();
        $container = $kernel->getContainer();

        /** @var ContainerAwareController $class */
        $class = $container->get('FunctionalTest\DI\Bridge\Symfony\Fixtures\ContainerAwareController');

        $this->assertSame($container, $class->container);
    }
}
