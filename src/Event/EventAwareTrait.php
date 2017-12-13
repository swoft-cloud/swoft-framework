<?php

namespace Swoft\Event;

/**
 * Trait EventAwareTrait
 * @package Swoft\Event
 * @version   2017年08月30日
 * @author    inhere <in.798@qq.com>
 * @copyright Copyright 2010-2016 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
trait EventAwareTrait
{
    /**
     * @var EventManager|EventManagerInterface
     */
    protected $eventManager;

    /**
     * @return EventManager|EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * @param EventManager|EventManagerInterface $eventManager
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * @param  string|EventInterface $event 'app.start' 'app.stop'
     * @param  mixed|string $target
     * @param  array|mixed $args
     * @return mixed
     */
    public function trigger($event, $target = null, array $args = [])
    {
        if ($this->eventManager) {
            return $this->eventManager->trigger($event, $target, $args);
        }

        return $event;
    }
}
