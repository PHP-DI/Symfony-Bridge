<?php
/**
 * PHP-DI
 *
 * @link      http://php-di.org/
 * @copyright Matthieu Napoli (http://mnapoli.fr/)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace DI\Bridge\Symfony\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Checks that all references are pointing to a valid service.
 *
 * Overridden so that this pass looks into several containers,
 * i.e. into PHP-DI too.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class CheckExceptionOnInvalidReferenceBehaviorPass
    extends \Symfony\Component\DependencyInjection\Compiler\CheckExceptionOnInvalidReferenceBehaviorPass
    implements CompilerPassInterface
{
    /**
     * @var \Interop\Container\ContainerInterface[]
     */
    private $containers;
    private $sourceId;

    public function __construct(array $containers)
    {
        $this->containers = $containers;
    }

    public function process(ContainerBuilder $container)
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            $this->sourceId = $id;
            $this->processDefinition($definition);
        }
    }

    private function processDefinition(Definition $definition)
    {
        $this->processReferences($definition->getArguments());
        $this->processReferences($definition->getMethodCalls());
        $this->processReferences($definition->getProperties());
    }

    private function processReferences(array $arguments)
    {
        foreach ($arguments as $argument) {
            if (is_array($argument)) {
                $this->processReferences($argument);
            } elseif ($argument instanceof Definition) {
                $this->processDefinition($argument);
            } elseif ($argument instanceof Reference && ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE === $argument->getInvalidBehavior()) {
                $destId = (string) $argument;

                $this->has($destId);
            }
        }
    }

    private function has($id)
    {
        foreach ($this->containers as $container) {
            if ($container->has($id)) {
                return;
            }
        }
        throw new ServiceNotFoundException($id, $this->sourceId);
    }
}
