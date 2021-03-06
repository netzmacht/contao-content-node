<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\ContentNode\Node;

use Netzmacht\Contao\ContentNode\Event\CreateNodeEvent;
use Netzmacht\Contao\ContentNode\Event\InitializeNodeEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Factory creates the node types by parsing the given configuration.
 *
 * If also dispatches Events so it's possible to hook into the factory process.
 *
 * @see CreateNodeEvent::NAME
 * @see InitializeNodeEvent::NAME
 */
class Factory
{
    /**
     * The event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * The configuration.
     *
     * @var array
     */
    private $configs;

    /**
     * Factory constructor.
     *
     * @param EventDispatcherInterface $dispatcher The event dispatcher.
     * @param array                    $configs    The node configs.
     */
    public function __construct(EventDispatcherInterface $dispatcher, array $configs)
    {
        $this->dispatcher = $dispatcher;
        $this->configs    = $configs;
    }

    /**
     * Create a node.
     *
     * @param string $type The node type.
     *
     * @return Node
     *
     * @throws \InvalidArgumentException When node type is not configured.
     * @throws \RuntimeException         When no node type could be created.
     */
    public function create($type)
    {
        if (!isset($this->configs[$type])) {
            throw new \InvalidArgumentException(sprintf('Unknown node type "%s"', $type));
        }

        $event = new CreateNodeEvent($type, $this->configs[$type]);
        $this->dispatcher->dispatch($event::NAME, $event);

        if ($event->getFactory()) {
            $node = call_user_func($event->getFactory(), $event->getType(), $event->getConfig());
        } elseif ($event->getClassName()) {
            $className = $event->getClassName();
            $node      = new $className($type, $event->getConfigValue('children'));
        } else {
            throw new \RuntimeException(sprintf('Could not create node "%s"', $type));
        }

        $event = new InitializeNodeEvent($node, $event->getConfig());
        $this->dispatcher->dispatch($event::NAME, $event);

        return $node;
    }

    /**
     * Get all supported node types.
     *
     * @return array
     */
    public function getNodeTypes()
    {
        return array_keys($this->configs);
    }

    /**
     * Check if a node type is supported.
     *
     * @param string $type The node type.
     *
     * @return bool
     */
    public function supportsNodeType($type)
    {
        return array_key_exists($type, $this->configs);
    }
}
