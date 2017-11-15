<?php

namespace Swoft\Web\Pipeline;


/**
 * @uses      Pipeline
 * @version   2017年11月15日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Pipeline
{

    /**
     * @var callable[]
     */
    protected $stages = [];

    /**
     * @var ProcessorInterface
     */
    protected $processor;

    /**
     * @var string
     */
    protected $method = 'process';

    /**
     * @var mixed
     */
    protected $traveler;

    /**
     * Constructor.
     *
     * @param callable[] $stages
     * @param ProcessorInterface $processor
     * @throws InvalidArgumentException
     */
    public function __construct(array $stages = [], ProcessorInterface $processor = null)
    {
        foreach ($stages as $stage) {
            if (! is_callable($stage) && ! is_object($stage) && $stage instanceof ProcessorInterface) {
                throw new \InvalidArgumentException('All stages should be callable or implement ProcessorInterface.');
            }
        }
        $this->stages = $stages;
        $this->processor = $processor ? : new FingersCrossedProcessor($stages);
    }

    /**
     * Add an stage.
     *
     * @param ProcessorInterface $stage
     * @return $this
     */
    public function add($stage)
    {
        $clone = clone $this;
        $clone->stages[] = $stage;
        return $clone;
    }

    /**
     * @param mixed $traveler
     * @return $this
     */
    public function send($traveler)
    {
        $this->traveler = $traveler;
        return $this;
    }

    /**
     * Process the payload.
     *
     * @param $traveler
     * @return mixed
     */
    public function process($traveler = null)
    {
        is_null($traveler) && $traveler = $this->traveler;
        $method = $this->method;
        $this->processor->stages = $this->stages;
        return $this->processor->$method($traveler);
    }

}