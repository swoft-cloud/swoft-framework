<?php
if (!function_exists('get_last_sql')) {

    /**
     * @return string
     */
    function get_last_sql(): string
    {
        $contextSqlKey = \Swoft\Db\Helper\DbHelper::getContextSqlKey();
        /* @var \SplStack $stack */
        $stack = \Swoft\Core\RequestContext::getContextDataByKey($contextSqlKey, new \SplStack());

        if($stack->isEmpty()){
            return '';
        }
        return $stack->pop();
    }
}