<?php

namespace Swoft\Event\Events;

use Swoft\Event\Event;

/**
 * TransactionEvent
 */
class TransactionReleaseEvent extends Event
{
    /**
     * @var \SplStack[]
     */
    private $tsStacks;

    /**
     * @var []
     */
    private $connections;

    public function __construct($name = null, array $tsStacks, array &$connections)
    {
        parent::__construct($name);
        $this->tsStacks = $tsStacks;
        $this->connections = $connections;
    }

    /**
     * @return \SplStack[]
     */
    public function getTsStacks(): array
    {
        return $this->tsStacks;
    }

    /**
     * @return mixed
     */
    public function getConnections()
    {
        return $this->connections;
    }
}