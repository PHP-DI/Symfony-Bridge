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
use FunctionalTest\DI\Bridge\Symfony\Fixtures\ContainerAwareController;

class ContainerAwareInterfaceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @link https://github.com/PHP-DI/Symfony2-Bridge/issues/2
     */
    public function testContainerAware()
    {
        $wrapper = new SymfonyContainerBridge();
        $builder = new ContainerBuilder();
        $builder->wrapContainer($wrapper);
        $wrapper->setFallbackContainer($builder->build());

        /** @var ContainerAwareController $class */
        $class = $wrapper->get('FunctionalTest\DI\Bridge\Symfony\Fixtures\ContainerAwareController');

        $this->assertSame($wrapper, $class->container);
    }
}
