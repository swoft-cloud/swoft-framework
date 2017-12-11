<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-10-16
 * Time: 16:47
 */

namespace Swoft\Event;

/**
 * Class EventManager
 * @package Swoft\Event
 */
class EventManager implements EventManagerInterface
{
    /**
     * 1.事件存储
     * @var EventInterface[]
     * [
     *     'event name' => (object)EventInterface -- event description
     * ]
     */
    protected $events = [];

    /**
     * 2.监听器存储
     * @var ListenerQueue[] array
     */
    protected $listeners = [];

    public function __destruct()
    {
        $this->clear();
    }

    public function clear()
    {
        $this->events = $this->listeners = [];
    }

    /*******************************************************************************
     * Event manager
     ******************************************************************************/

    /**
     * 添加一个不存在的事件
     * @param Event|string $event | event name
     * @param array $params
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function addEvent($event, array $params = [])
    {
        if (\is_string($event)) {
            $event = new Event(trim($event), $params);
        }

        /** @var $event Event */
        if (($event instanceof EventInterface) && !isset($this->events[$event->getName()])) {
            $this->events[$event->getName()] = $event;
        }

        return $this;
    }

    /**
     * 设定一个事件处理
     * @param string|EventInterface $event
     * @param array $params
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setEvent($event, array $params = [])
    {
        if (\is_string($event)) {
            $event = new Event(trim($event), $params);
        }

        if ($event instanceof EventInterface) {
            $this->events[$event->getName()] = $event;
        }

        return $this;
    }

    /**
     * @param string $name
     * @param null $default
     * @return mixed|null
     */
    public function getEvent($name, $default = null)
    {
        return $this->events[$name] ?? $default;
    }


    public function removeEvent($event)
    {
        if ($event instanceof EventInterface) {
            $event = $event->getName();
        }

        if (isset($this->events[$event])) {
            unset($this->events[$event]);
        }

        return $this;
    }

    /**
     * @param $event
     * @return bool
     */
    public function hasEvent($event)
    {
        if ($event instanceof EventInterface) {
            $event = $event->getName();
        }

        return isset($this->events[$event]);
    }


    /**
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param $events
     * @throws \InvalidArgumentException
     */
    public function setEvents(array $events)
    {
        foreach ($events as $key => $event) {
            $this->setEvent($event);
        }
    }

    /**
     * @return int
     */
    public function countEvents()
    {
        return \count($this->events);
    }

    /*******************************************************************************
     * Listener manager
     ******************************************************************************/

    /**
     * Attaches a listener to an event
     * @param string $event the event to attach too
     * @param callable|EventHandlerInterface|mixed $callback a callable listener function
     * @param int $priority the priority at which the $callback executed
     * @return bool true on success false on failure
     * @throws \InvalidArgumentException
     */
    public function attach($event, $callback, $priority = 0)
    {
        $this->addListener($callback, [$event => $priority]);

        return true;
    }

    /**
     * Detaches a listener from an event
     * @param string $event the event to attach too
     * @param callable $callback a callable function
     * @return bool true on success false on failure
     */
    public function detach($event, $callback)
    {
        return $this->removeListener($callback, $event);
    }

    /**
     * 添加监听器 并关联到 某一个(多个)事件
     * @param \Closure|callback|mixed $listener 监听器
     * @param array|string|int $definition 事件名，优先级设置
     * Allowed:
     *     $definition = [
     *        'event name' => priority(int),
     *        'event name1' => priority(int),
     *     ]
     * OR
     *     $definition = [
     *        'event name','event name1',
     *     ]
     * OR
     *     $definition = 'event name'
     * OR
     *     // The priority of the listener 监听器的优先级
     *     // 此时若 $listener 是个正常的类，会自动将所有的 以 `on` 开头的公共方法作为事件名称，关联到 $listener
     *     $definition = 1
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function addListener($listener, $definition = null)
    {
        // func
        if (\is_string($listener)) {
            $callback = $listener;
            $listener = new \stdClass();
            $listener->callback = $callback;
        }

        if (!\is_object($listener)) {
            throw new \InvalidArgumentException('The given listener must is: object or Closure.');
        }

        $defaultPriority = ListenerPriority::NORMAL;

        if (is_numeric($definition)) {
            $defaultPriority = (int)$definition;
            $definition = null;
        } elseif (\is_string($definition)) { // 仅是个 事件名称
            $definition = [$definition => $defaultPriority];
        } elseif ($definition instanceof EventInterface) { // 仅是个 事件对象,取出名称
            $definition = [$definition->getName() => $defaultPriority];
        }

        // 1. an Array
        if ($definition) {
            // 循环: 将 监听器 关联到 各个事件
            foreach ($definition as $name => $priority) {
                if (\is_int($name)) {
                    if (!$priority || !\is_string($priority)) {
                        continue;
                    }

                    $name = $priority;
                    $priority = $defaultPriority;
                }

                $name = trim($name);

                if (!isset($this->listeners[$name])) {
                    $this->listeners[$name] = new ListenerQueue;
                }

                $this->listeners[$name]->add($listener, $priority);
            }

            return $this;
        }

        // if (!\is_object($listener) || $listener instanceof \Closure) {
        //     return $this;
        // }

        // 2. is an Object.

        // 得到要绑定的监听器中所有方法名(only public methods)
        // $methods = get_class_methods($listener);

        // 循环: 将 监听器 关联到 各个事件
        // foreach ($methods as $name) {
        //     if (strpos($name, 'on') !== 0) {
        //         continue;
        //     }
        //
        //     if (!isset($this->listeners[$name])) {
        //         $this->listeners[$name] = new ListenerQueue;
        //     }
        //
        //     $this->listeners[$name]->add($listener, $definition[$name] ?? $defaultPriority);
        // }

        return $this;
    }

    /**
     * 是否存在 对事件的 监听队列
     * @param  EventInterface|string $event
     * @return boolean
     */
    public function hasListenerQueue($event)
    {
        if ($event instanceof EventInterface) {
            $event = $event->getName();
        }

        return isset($this->listeners[$event]);
    }

    /**
     * @see self::hasListenerQueue() alias method
     * @param  EventInterface|string $event
     * @return boolean
     */
    public function hasListeners($event)
    {
        return $this->hasListenerQueue($event);
    }

    /**
     * 是否存在(对事件的)监听器
     * @param $listener
     * @param  EventInterface|string $event
     * @return bool
     */
    public function hasListener($listener, $event = null)
    {
        if ($event) {
            if ($event instanceof EventInterface) {
                $event = $event->getName();
            }

            if (isset($this->listeners[$event])) {
                return $this->listeners[$event]->has($listener);
            }
        } else {
            foreach ($this->listeners as $queue) {
                if ($queue->has($listener)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 获取事件的一个监听器的优先级别
     * @param $listener
     * @param  string|EventInterface $event
     * @return int|null
     */
    public function getListenerPriority($listener, $event)
    {
        if ($event instanceof EventInterface) {
            $event = $event->getName();
        }

        if (isset($this->listeners[$event])) {
            return $this->listeners[$event]->getPriority($listener);
        }

        return null;
    }

    /**
     * 获取事件的所有监听器
     * @param  string|EventInterface $event
     * @return ListenerQueue|null
     */
    public function getListenerQueue($event)
    {
        if ($event instanceof EventInterface) {
            $event = $event->getName();
        }

        if (isset($this->listeners[$event])) {
            return $this->listeners[$event];
        }

        return null;
    }

    /**
     * 获取事件的所有监听器
     * @param  string|EventInterface $event
     * @return array
     */
    public function getListeners($event)
    {
        if ($event instanceof EventInterface) {
            $event = $event->getName();
        }

        if (isset($this->listeners[$event])) {
            return $this->listeners[$event]->getAll();
        }

        return [];
    }

    /**
     * 统计获取事件的监听器数量
     * @param  string|EventInterface $event
     * @return int
     */
    public function countListeners($event)
    {
        if ($event instanceof EventInterface) {
            $event = $event->getName();
        }

        return isset($this->listeners[$event]) ? \count($this->listeners[$event]) : 0;
    }

    /**
     * 移除对某个事件的监听
     * @param $listener
     * @param null|string|EventInterface $event
     * 为空时，移除监听者队列中所有名为 $listener 的监听者
     * 否则， 则移除对事件 $event 的监听者
     * @return bool
     */
    public function removeListener($listener, $event = null)
    {
        if ($event) {
            if ($event instanceof EventInterface) {
                $event = $event->getName();
            }

            // 存在对这个事件的监听队列
            if (isset($this->listeners[$event])) {
                $this->listeners[$event]->remove($listener);
            }
        } else {
            foreach ($this->listeners as $queue) {
                /**  @var $queue ListenerQueue */
                $queue->remove($listener);
            }
        }

        return true;
    }

    /**
     * Clear all listeners for a given event
     * @param  string|EventInterface $event
     * @return void
     */
    public function clearListeners($event)
    {
        if ($event) {
            if ($event instanceof EventInterface) {
                $event = $event->getName();
            }

            // 存在对这个事件的监听队列
            if (isset($this->listeners[$event])) {
                unset($this->listeners[$event]);
            }
        } else {
            $this->listeners = [];
        }
    }

    /**
     * Trigger an event
     * Can accept an EventInterface or will create one if not passed
     * @param  string|EventInterface $event 'app.start' 'app.stop'
     * @param  mixed|string $target
     * @param  array|mixed $args
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function trigger($event, $target = null, array $args = [])
    {
        if (!($event instanceof EventInterface)) {
            if (isset($this->events[$event])) {
                $event = $this->events[$event];
            } else {
                $event = new Event($event);
            }
        }

        /** @var EventInterface $event */
        $name = $event->getName();
        $event->addParams($args);
        $event->setTarget($target);

        if (isset($this->listeners[$name])) {
            $this->fireListeners($this->listeners[$name], $event, $name);

            if ($event->isPropagationStopped()) {
                return $event;
            }
        }

        // like 'app.start'
        if (\strpos($name, '.')) {
            list($name, $method) = explode('.', $name, 2);

            if (isset($this->listeners[$name])) {
                $this->fireListeners($this->listeners[$name], $event, $method);
            }
        }

        return $event;
    }

    /**
     * @param array|ListenerQueue $listeners
     * @param EventInterface $event
     * @param null $method
     */
    protected function fireListeners($listeners, EventInterface $event, $method = null)
    {
        // 循环调用监听器，处理事件
        foreach ($listeners as $listener) {
            if ($event->isPropagationStopped()) {
                break;
            }

            if (\is_object($listener)) {
                if ($listener instanceof \stdClass) {
                    $cb = $listener->callback;
                    $cb($event);
                } elseif ($method && method_exists($listener, $method)) {
                    $listener->$method($event);
                } elseif ($listener instanceof EventHandlerInterface) {
                    $listener->handle($event);
                } elseif (method_exists($listener, '__invoke')) {
                    $listener($event);
                }
            } elseif (\is_callable($listener)) {
                $listener($event);
            }
        }
    }
}
