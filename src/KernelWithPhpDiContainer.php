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

/**
 * Customization of Symfony's kernel to setup PHP-DI.
 *
 * Extend this class instead of Symfony's base kernel.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
abstract class KernelWithPhpDiContainer extends KernelWithPsr11Container
{
    /**
     * @var ContainerInterface
     */
    private $phpdiContainer;

    /**
     * Implement this method to configure PHP-DI.
     *
     * @return ContainerInterface
     */
    abstract protected function buildPHPDIContainer(\DI\ContainerBuilder $builder);

    /**
     * @return ContainerInterface
     */
    protected function getFallbackContainer()
    {
        if ($this->phpdiContainer === null) {
            $builder = new \DI\ContainerBuilder();
            $builder->wrapContainer($this->getContainer());

            $this->phpdiContainer = $this->buildPHPDIContainer($builder);
        }

        return $this->phpdiContainer;
    }
}
