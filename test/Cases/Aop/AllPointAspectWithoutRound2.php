<?php
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace SwoftTest\Aop;

use Swoft\Aop\JoinPoint;
use Swoft\Aop\ProceedingJoinPoint;
use Swoft\Bean\Annotation\After;
use Swoft\Bean\Annotation\AfterReturning;
use Swoft\Bean\Annotation\AfterThrowing;
use Swoft\Bean\Annotation\Around;
use Swoft\Bean\Annotation\Aspect;
use Swoft\Bean\Annotation\Before;
use Swoft\Bean\Annotation\PointBean;

/**
 *
 * @Aspect()
 * @PointBean(
 *     include={AopBean2::class},
 * )
 *
 * @uses      AllPointAspectWithoutRound2
 * @version   2018年03月27日
 * @author    maijiankang <maijiankang@foxmail.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class AllPointAspectWithoutRound2
{
    /**
     * @Before()
     */
    public function before()
    {
        echo ' before2withoutaround ';
    }

    /**
     * @After()
     */
    public function after()
    {
        echo ' after2withoutaround ';
    }

    /**
     * @AfterReturning()
     */
    public function afterReturn()
    {
        echo ' afterReturn2withoutaround ';
    }


}