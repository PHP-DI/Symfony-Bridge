<?php
/**
 * PHP-DI
 *
 * @link      http://php-di.org/
 * @copyright Matthieu Napoli (http://mnapoli.fr/)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace DI\Bridge\Symfony;

use Psr\Container\ContainerInterface;
use Symfony\Component\Debug\DebugClassLoader;
use Symfony\Component\DependencyInjection\Compiler\CheckExceptionOnInvalidReferenceBehaviorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Customization of Symfony's kernel to setup any PSR-11 container.
 *
 * Extend this class instead of Symfony's base kernel.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
abstract class KernelWithPsr11Container extends \Symfony\Component\HttpKernel\Kernel
{
    public function __construct($environment, $debug)
    {
        parent::__construct($environment, $debug);
        $this->disableDebugClassLoader();
    }

    protected function getContainerBaseClass()
    {
        return SymfonyContainerBridge::class;
    }

    protected function buildContainer()
    {
        $containerBuilder = parent::buildContainer();

        $this->removeInvalidReferenceBehaviorPass($containerBuilder);

        return $containerBuilder;
    }

    protected function initializeContainer()
    {
        parent::initializeContainer();

        /** @var SymfonyContainerBridge $rootContainer */
        $rootContainer = $this->getContainer();

        $rootContainer->setFallbackContainer($this->getFallbackContainer());
    }

    /**
     * Remove the CheckExceptionOnInvalidReferenceBehaviorPass because
     * it was not looking into PHP-DI's entries and thus throwing exceptions.
     *
     * @todo Replace it by an alternative that can search into PHP-DI too
     *       Problem: PHP-DI is not initialized when Symfony's container is compiled, because
     *       it depends on Symfony's container for fallback (cycle…)
     *
     * @param ContainerBuilder $container
     */
    private function removeInvalidReferenceBehaviorPass(ContainerBuilder $container)
    {
        $passConfig = $container->getCompilerPassConfig();
        $compilationPasses = $passConfig->getRemovingPasses();

        foreach ($compilationPasses as $i => $pass) {
            if ($pass instanceof CheckExceptionOnInvalidReferenceBehaviorPass) {
                unset($compilationPasses[$i]);
                break;
            }
        }

        $passConfig->setRemovingPasses($compilationPasses);
    }

    private function disableDebugClassLoader()
    {
        if (!class_exists(DebugClassLoader::class)) {
            return;
        }

        DebugClassLoader::disable();
    }

    /**
     * @return ContainerInterface
     */
    abstract protected function getFallbackContainer();
}
