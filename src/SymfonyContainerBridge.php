<?php
/**
 * PHP-DI
 *
 * @link      http://php-di.org/
 * @copyright Matthieu Napoli (http://mnapoli.fr/)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace DI\Bridge\Symfony;

use DI\NotFoundException;
use Symfony\Component\DependencyInjection\Container as SymfonyContainer;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as SymfonyContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Interop\Container\ContainerInterface;

/**
 * Replacement for the Symfony service container.
 *
 * This container extends Symfony's container with a fallback container when an entry is not found.
 * That way, we can put PHP-DI's container as a fallback to Symfony's.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class SymfonyContainerBridge extends SymfonyContainer implements SymfonyContainerInterface, ContainerInterface
{
    /**
     * @var ContainerInterface|null
     */
    private $fallbackContainer;

    /**
     * @param ContainerInterface $container
     */
    public function setFallbackContainer(ContainerInterface $container)
    {
        $this->fallbackContainer = $container;
    }

    /**
     * @return ContainerInterface
     */
    public function getFallbackContainer()
    {
        return $this->fallbackContainer;
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        if (parent::has($id)) {
            return true;
        }

        if (! $this->fallbackContainer) {
            return false;
        }

        return $this->fallbackContainer->has($id);
    }

    /**
     * {@inheritdoc}
     */
    public function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
    {
        if (parent::has($id)) {
            return parent::get($id, $invalidBehavior);
        }

        if (! $this->fallbackContainer) {
            return false;
        }

        try {
            $entry = $this->fallbackContainer->get($id);

            // Stupid hack for Symfony's ContainerAwareInterface
            if ($entry instanceof ContainerAwareInterface) {
                $entry->setContainer($this);
            }

            return $entry;
        } catch (NotFoundException $e) {
            if ($invalidBehavior === self::EXCEPTION_ON_INVALID_REFERENCE) {
                throw new ServiceNotFoundException($id);
            }
        }

        return null;
    }
}
