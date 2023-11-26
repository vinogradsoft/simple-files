<?php
declare(strict_types=1);

namespace Test\Cases;

use PHPUnit\Framework\TestCase;

abstract class CommonCase extends TestCase
{
    /**
     * @return string runtime path
     */
    protected function getRuntimePath(): string
    {
        return dirname((new \ReflectionClass(CommonCase::class))->getFileName(),2) . '/runtime';
    }
}