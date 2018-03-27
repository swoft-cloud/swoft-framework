<?php
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace SwoftTest;

use Swoft\App;
use SwoftTest\Aop\AnnotationAop;
use SwoftTest\Aop\AopBean;
use SwoftTest\Aop\AopBean2;
use SwoftTest\Aop\RegBean;

/**
 *
 *
 * @uses      AopTest
 * @version   2017年12月24日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class AopTest extends AbstractTestCase
{
    public function testAllAdvice()
    {
        /* @var \SwoftTest\Aop\AopBean $aopBean*/
        $aopBean = App::getBean(AopBean::class);
        $result = $aopBean->doAop();
        $this->assertEquals('do aop around-before2  before2  around-after2  afterReturn2  around-before1  before1  around-after1  afterReturn1 ', $result);
    }


    /**
     * 验证问题:当切面不包含Around型通知时，不支持多层切面
     * @author Jiankang maijiankang@foxmail.com
     */
    public function testNestWithourRound()
    {
        /* @var \SwoftTest\Aop\AopBean2 $aopBean*/
        $aopBean = App::getBean(AopBean2::class);
        
        ob_start();
        $aopBean->doAop();
        $echoContent=ob_get_contents();
        ob_end_clean();
        
        $this->assertEquals(' before1withoutaround  before2withoutaround do aop after2withoutaround  afterReturn2withoutaround  after1withoutaround  afterReturn1withoutaround ', $echoContent);
    }
    
    public function testAnnotationAop()
    {
        /* @var AnnotationAop $annotationBean*/
        $annotationBean = App::getBean(AnnotationAop::class);
        $result = $annotationBean->cacheable();
        $this->assertEquals('cacheable around before  around after ', $result);

        $result = $annotationBean->cachePut();
        $this->assertEquals('cachePut around before  around after ', $result);
    }

    public function testRegAop()
    {
        /* @var RegBean $annotationBean*/
        $annotationBean = App::getBean(RegBean::class);
        $result = $annotationBean->regMethod();
        $this->assertEquals('regMethod RegAspect around before  RegAspect around after ', $result);

        $result = $annotationBean->regMethod2();
        $this->assertEquals('regMethod2 RegAspect around before  RegAspect around after ', $result);
    }

    public function testNewAopParams()
    {
        /* @var RegBean $annotationBean*/
        $annotationBean = App::getBean(RegBean::class);
        $result = $annotationBean->methodParams('a', 'b');
        $this->assertEquals('methodParams-a-new-b-new regAspect around before  regAspect around after ', $result);
    }
}
