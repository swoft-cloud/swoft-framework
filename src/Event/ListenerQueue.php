<?php

namespace Swoft\Event;

/**
 * Class ListenerQueue
 * @package Swoft\Event
 * @use 监听器队列存储管理类 @link [windwalker framework](https://github.com/ventoviro/windwalker)
 * @version   2017年08月30日
 * @author    inhere <in.798@qq.com>
 * @copyright Copyright 2010-2016 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ListenerQueue implements \IteratorAggregate, \Countable
{
    /**
     * 对象存储器
     * @var \SplObjectStorage
     */
    protected $store;

    /**
     * 优先级队列
     * @var \SplPriorityQueue
     */
    protected $queue;

    /**
     * 计数器
     * 设定最大值为 PHP_INT_MAX == 300
     * @var int
     */
    private $counter = PHP_INT_MAX;

    public function __construct()
    {
        $this->store = new \SplObjectStorage();
        $this->queue = new \SplPriorityQueue();
    }

    /**
     * 添加一个监听器, 增加了添加 callback(string|array)
     * @param $listener \Closure|callable|\stdClass 监听器
     * @param $priority integer 优先级
     * @return $this
     */
    public function add($listener, $priority)
    {
        if (!$this->has($listener)) {
            // Compute the internal priority as an array. 计算内部优先级为一个数组。
            $priorityData = [(int)$priority, $this->counter--];

            // a Callback(string|array)
            if (!\is_object($listener) && \is_callable($listener)) {
                $callback = $listener;
                $listener = new \stdClass;
                $listener->callback = $callback;
            }

            $this->store->attach($listener, $priorityData);
            $this->queue->insert($listener, $priorityData);
        }

        return $this;
    }

    /**
     * 删除一个监听器
     * @param $listener
     * @return $this
     */
    public function remove($listener)
    {
        if ($this->has($listener)) {
            $this->store->detach($listener);
            $this->store->rewind();

            $queue = new \SplPriorityQueue();

            foreach ($this->store as $otherListener) {
                // 优先级
                $priority = $this->store->getInfo();
                $queue->insert($otherListener, $priority);
            }

            $this->queue = $queue;
        }

        return $this;
    }

    /**
     * Get the priority of the given listener. 得到指定监听器的优先级
     * @param   mixed $listener The listener.
     * @param   mixed $default The default value to return if the listener doesn't exist.
     * @return  mixed  The listener priority if it exists, null otherwise.
     */
    public function getPriority($listener, $default = null)
    {
        if ($this->store->contains($listener)) {
            return $this->store[$listener][0];
        }

        return $default;
    }

    /**
     * getPriority() alias method
     * @param $listener
     * @param null $default
     * @return mixed
     */
    public function getLevel($listener, $default = null)
    {
        return $this->getPriority($listener, $default);
    }

    /**
     * Get all listeners contained in this queue, sorted according to their priority.
     * @return  mixed[]  An array of listeners.
     */
    public function getAll()
    {
        $listeners = [];

        // Get a clone of the queue.
        $queue = $this->getIterator();

        foreach ($queue as $listener) {
            $listeners[] = $listener;
        }

        unset($queue);

        return $listeners;
    }

    /**
     * @param $listener
     * @return bool
     */
    public function has($listener)
    {
        return $this->store->contains($listener);
    }

    /**
     * @param $listener
     * @return bool
     */
    public function exists($listener)
    {
        return $this->store->contains($listener);
    }

    /**
     * Get the inner queue with its cursor on top of the heap.
     * @return  \SplPriorityQueue  The inner queue.
     */
    public function getIterator()
    {
        // SplPriorityQueue queue is a heap.
        $queue = clone $this->queue;

        if (!$queue->isEmpty()) {
            $queue->top();
        }

        return $queue;
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return \count($this->queue);
    }

}
