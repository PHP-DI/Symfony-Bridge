<?php
/**
 * PHP-DI
 *
 * @link      http://php-di.org/
 * @copyright Matthieu Napoli (http://mnapoli.fr/)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace DI\Bridge\Symfony\Test\UnitTest;

use DI\Bridge\Symfony\SymfonyContainerBridge;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as SfContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class SymfonyContainerBridgeTest extends TestCase
{
    public function testHasFallback()
    {
        $wrapper = new SymfonyContainerBridge();

        $fallback = $this->getMockForAbstractClass(ContainerInterface::class);
        $fallback->expects(self::once())
            ->method('has')
            ->with('foo')
            ->will(self::returnValue(false));

        $wrapper->setFallbackContainer($fallback);

        $this::assertFalse($wrapper->has('foo'));
    }

    public function testGetFallback()
    {
        $wrapper = new SymfonyContainerBridge();

        $fallback = $this->getMockForAbstractClass(ContainerInterface::class);
        $fallback->expects(self::once())
            ->method('get')
            ->with('foo')
            ->will(self::returnValue('bar'));

        $wrapper->setFallbackContainer($fallback);

        $this::assertEquals('bar', $wrapper->get('foo'));
    }

    public function testGetNotFoundReturnNull()
    {
        $wrapper = new SymfonyContainerBridge();

        $wrapper->setFallbackContainer(ContainerBuilder::buildDevContainer());

        $this::assertNull($wrapper->get('foo', SfContainerInterface::NULL_ON_INVALID_REFERENCE));
    }

    public function testGetNotFoundException()
    {
        $this->expectException(ServiceNotFoundException::class);

        $wrapper = new SymfonyContainerBridge();

        $wrapper->setFallbackContainer(ContainerBuilder::buildDevContainer());

        $this::assertNull($wrapper->get('foo'));
    }
}
