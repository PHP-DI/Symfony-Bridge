<?php
/**
 * PHP-DI
 *
 * @link      http://php-di.org/
 * @copyright Matthieu Napoli (http://mnapoli.fr/)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace UnitTest\DI\Bridge\Symfony;

use DI\Bridge\Symfony\SymfonyContainerBridge;

class SymfonyContainerBridgeTest extends \PHPUnit_Framework_TestCase
{
    public function testHasCallsFallbackContainer()
    {
        $wrapper = new SymfonyContainerBridge();

        $fallback = $this->getMockForAbstractClass('DI\ContainerInterface');
        $fallback->expects($this->once())
            ->method('has')
            ->with('foo')
            ->will($this->returnValue(false));

        $wrapper->setFallbackContainer($fallback);

        $this->assertFalse($wrapper->has('foo'));
    }
}
