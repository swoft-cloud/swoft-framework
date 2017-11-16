<?php

namespace Swoft\Test\Base;

use Swoft\Test\AbstractTestCase;
use Swoft\Test\Base\Pipeline\ExceptionProcessor;
use Swoft\Test\Base\Pipeline\TestProcessor;
use Swoft\Web\Pipeline\Pipeline;


/**
 * @uses      PipelineTest
 * @version   2017年11月15日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class PipelineTest extends AbstractTestCase
{

    /**
     * @test
     */
    public function process()
    {
        // 一般流程测试
        $pipeline = new Pipeline();
        $result = $pipeline->add(TestProcessor::class)->add([TestProcessor::class, 'process'])->add(function ($payload
        ) {
            return $payload + 1;
        })->process(1);
        $this->assertEquals(4, $result);

        // add 方法隔离测试
        $result = $pipeline->add(TestProcessor::class)->process(1);
        $this->assertEquals(2, $result);

        // 批量设置 Processors 测试
        $pipeline = new Pipeline([
            TestProcessor::class,
            [TestProcessor::class, 'process'],
            function ($payload) {
                return $payload + 1;
            },
        ]);
        $result = $pipeline->process(1);
        $this->assertEquals(4, $result);
        $result = $pipeline->add(TestProcessor::class)->process(1);
        $this->assertEquals(5, $result);
    }

    /**
     * 异常测试
     *
     * @test
     * @expectedException \RuntimeException
     */
    public function exceptionProcess()
    {
        $pipeline = new Pipeline();
        $pipeline->add(ExceptionProcessor::class)->process(1);
    }


}