<?php

namespace Swoft\Pipeline;

/**
 * @uses      PipelineInterface
 * @version   2017年11月15日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
interface PipelineInterface
{

    /**
     * Set the traveler object being sent on the pipeline.
     *
     * @param  mixed $passable
     * @return $this
     */
    public function send($passable);

    /**
     * Set the stops of the pipeline.
     *
     * @param  dynamic|array $stops
     * @return $this
     */
    public function through($stops);

    /**
     * Set the method to call on the stops.
     *
     * @param  string $method
     * @return $this
     */
    public function via($method);

    /**
     * Run the pipeline with a final destination callback.
     *
     * @param  \Closure $destination
     * @return mixed
     */
    public function then(\Closure $destination);
}
