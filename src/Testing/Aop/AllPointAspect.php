<?php

namespace Swoft\Testing\Aop;

use Swoft\Bean\Annotation\After;
use Swoft\Bean\Annotation\AfterReturning;
use Swoft\Bean\Annotation\AfterThrowing;
use Swoft\Bean\Annotation\Around;
use Swoft\Bean\Annotation\Aspect;
use Swoft\Bean\Annotation\Before;
use Swoft\Bean\Annotation\PointAnnotation;
use Swoft\Bean\Annotation\PointBean;
use Swoft\Bean\Annotation\PointExecution;

/**
 * the test of aspcet
 *
 * @Aspect()
 * @PointBean(
 *     include={"name1", "name2"},
 *     exclude={"ename1", "ename2"}
 * )
 *
 * @PointAnnotation(
 *     include={"a1", "a2"},
 *     exclude={"ea1", "ea2"}
 * )
 *
 * @PointExecution(
 *     include={"exe1","exe12"},
 *     exclude={"eexe1","eexe12"}
 * )
 *
 * @uses      AllPointAspect
 * @version   2017年12月24日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class AllPointAspect
{
    /**
     * @Before()
     */
    public function before()
    {
        $aspects = [
            'class' =>[
                'order' => 12,
                'points' =>[
                    "bean"       => [
                        "include" => [],
                        "exclude" => [],
                    ],
                    "annotation" => [
                        "include" => [],
                        "exclude" => [],
                    ],
                    "execution"  => [
                        "include" => [],
                        "exclude" => [],
                    ],
                ],
                'advice' => [
                    'before' => 'method',
                    'after'  => 'method',
                ]
            ],
        ];

        $map = [
            'class' => [
                'method' => [
                    [
                        'before' => 'method',
                        'after'  => 'method',
                    ],
                    [
                        'before' => 'method',
                        'after'  => 'method',
                    ],
                ],
            ],
        ];
    }

    /**
     * @After()
     */
    public function after()
    {

    }

    /**
     * @AfterReturning()
     */
    public function afterReturn()
    {

    }

    /**
     * @Around()
     */
    public function around()
    {

    }

    /**
     * @AfterThrowing()
     */
    public function afterReturning()
    {

    }
}