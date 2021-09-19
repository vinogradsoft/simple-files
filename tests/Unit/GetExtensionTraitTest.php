<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Test\Cases\Helper\HelperForAbstractClasses;

class GetExtensionTraitTest extends TestCase
{
    /**
     * @dataProvider getCasesGetExtension
     */
    public function testGetExtension($ext, $filePath)
    {
        $trait = HelperForAbstractClasses::mockGetExtensionTrait($filePath);
        self::assertEquals($ext, $trait->getExtension());
    }

    public function getCasesGetExtension()
    {
        return [
            ['test', 'path/to/file.test'],
            ['test', 'path/to/.test'],
            ['', 'path/to/test'],
        ];
    }
}
