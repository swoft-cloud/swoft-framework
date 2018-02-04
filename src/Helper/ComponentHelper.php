<?php

namespace Swoft\Helper;

/**
 * the helper of component
 *
 * @uses      ComponentHelper
 * @version   2018年01月09日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ComponentHelper
{
    /**
     * Get the default namespace of component
     *
     * @param string $component
     * @return string
     */
    public static function getComponentNs(string $component): string
    {
        if ($component == 'framework') {
            return '';
        }

        $namespace = '';
        $nsAry = explode('-', $component);
        foreach ($nsAry as $ns) {
            $namespace .= "\\" . ucfirst($ns);
        }

        return $namespace;
    }
}