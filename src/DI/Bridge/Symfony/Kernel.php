<?php
/**
 * PHP-DI
 *
 * @link      http://php-di.org/
 * @copyright Matthieu Napoli (http://mnapoli.fr/)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace DI\Bridge\Symfony;

use DI\Bridge\Symfony\CompilerPass\CheckExceptionOnInvalidReferenceBehaviorPass as ReplacementPass;
use Interop\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Compiler\CheckExceptionOnInvalidReferenceBehaviorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Customization of Symfony's kernel to setup PHP-DI.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
abstract class Kernel extends \Symfony\Component\HttpKernel\Kernel
{
    /**
     * @var ContainerInterface
     */
    private $phpdiContainer;

    /**
     * @return ContainerInterface
     */
    abstract protected function buildPHPDIContainer(\DI\ContainerBuilder $builder);

    protected function getContainerBaseClass()
    {
        return 'DI\Bridge\Symfony\SymfonyContainerBridge';
    }

    protected function buildContainer()
    {
        $containerBuilder = parent::buildContainer();

        $this->replaceInvalidReferenceBehaviorPass($containerBuilder);

        return $containerBuilder;
    }

    protected function initializeContainer()
    {
        parent::initializeContainer();

        /** @var SymfonyContainerBridge $rootContainer */
        $rootContainer = $this->getContainer();

        $rootContainer->setFallbackContainer($this->getPHPDIContainer());
    }

    /**
     * Replace the CheckExceptionOnInvalidReferenceBehaviorPass with our own:
     * the original was not looking into PHP-DI's entries and thus throwing exceptions.
     *
     * @param ContainerBuilder $container
     */
    private function replaceInvalidReferenceBehaviorPass(ContainerBuilder $container)
    {
        $passConfig = $container->getCompilerPassConfig();
        $compilationPasses = $passConfig->getRemovingPasses();

        foreach ($compilationPasses as $i => $pass) {
            if ($pass instanceof CheckExceptionOnInvalidReferenceBehaviorPass) {
                unset($compilationPasses[$i]);
                continue;
                // Replace the original class with our own replacement
                $compilationPasses[$i] = new ReplacementPass(array(
                    $container,
                    $this->getPHPDIContainer(),
                ));
            }
        }

        $passConfig->setRemovingPasses($compilationPasses);
    }

    /**
     * @return ContainerInterface
     */
    private function getPHPDIContainer()
    {
        if ($this->phpdiContainer === null) {
            $builder = new \DI\ContainerBuilder();
            $builder->wrapContainer($this->getContainer());

            $this->phpdiContainer = $this->buildPHPDIContainer($builder);
        }

        return $this->phpdiContainer;
    }
}
