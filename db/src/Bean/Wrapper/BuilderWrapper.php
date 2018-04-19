<?php

namespace Swoft\Db\Bean\Wrapper;

use Swoft\Bean\Wrapper\AbstractWrapper;
use Swoft\Db\Bean\Annotation\Builder;

/**
 * BuilderWrapper
 */
class BuilderWrapper extends AbstractWrapper
{
    /**
     * @var array
     */
    protected $classAnnotations
        = [
            Builder::class,
        ];

    /**
     * @param array $annotations
     *
     * @return bool
     */
    public function isParseClassAnnotations(array $annotations): bool
    {
        return isset($annotations[Builder::class]);
    }

    /**
     * @param array $annotations
     *
     * @return bool
     */
    public function isParsePropertyAnnotations(array $annotations): bool
    {
        return false;
    }

    /**
     * @param array $annotations
     *
     * @return bool
     */
    public function isParseMethodAnnotations(array $annotations): bool
    {
        return false;
    }
}