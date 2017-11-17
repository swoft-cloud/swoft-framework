<?php

namespace Swoft\Web\Pipeline;


/**
 * @uses      PipelineBuilder
 * @version   2017年11月15日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class PipelineBuilder
{

    /**
     * @var array
     */
    protected $stages = [];

    /**
     * Add an stage.
     *
     * @param \Swoft\Web\Pipeline\ProcessorInterface $stage
     * @return $this
     */
    public function add($stage)
    {
        $this->stages[] = $stage;
        return $this;
    }

    /**
     * Build a new Pipeline object
     *
     * @param  ProcessorInterface|null $processor
     * @return Pipeline
     */
    public function build(ProcessorInterface $processor = null)
    {
        return new Pipeline($this->stages, $processor);
    }

}