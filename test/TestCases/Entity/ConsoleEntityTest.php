<?php

namespace Swoft\Test\Entity;

use Swoft\App;
use Swoft\Test\AbstractTestCase;
use Swoft\Console\Command\EntityController;
use Swoft\Console\Input\Input;
use Swoft\Console\Output\Output;

/**
 * Console-Entity test
 *
 * @uses      ConsoleEntityTest
 * @version   2017年12月25日
 * @author    caiwh <471113744@qq.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ConsoleEntityTest extends AbstractTestCase
{
    public function testInitEntityConsole()
    {
        $input = new Input();
        $output = new Output();
        $console = new EntityController($input, $output);
        $ret = $console->init();
        $this->assertEquals($ret, true);
    }
}
