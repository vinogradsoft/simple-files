<?php

namespace Test\Unit\Functionality;

use Test\Cases\Dummy\DummyAbstractFilesystemObject;
use Vinograd\SimpleFiles\FileFunctionalitiesContext;
use Vinograd\SimpleFiles\Functionality\FileFunctionality;
use PHPUnit\Framework\TestCase;
use Vinograd\Support\SupportedFunctionalities;

class FileFunctionalityTest extends TestCase
{
    public function testCreate()
    {
        $fileFunctionality = FileFunctionality::create($this->getMockForAbstractClass(SupportedFunctionalities::class));
        $fileFunctionality2 = FileFunctionality::create($this->getMockForAbstractClass(SupportedFunctionalities::class));
        self::assertSame($fileFunctionality, $fileFunctionality2);
    }

    public function testUninstallMethods()
    {
        $abstractFilesystemObject = new DummyAbstractFilesystemObject();
        $abstractFilesystemObject->installFunctionality($functionality = FileFunctionality::create($abstractFilesystemObject));
        $support = FileFunctionalitiesContext::getFunctionalitySupport($abstractFilesystemObject);
        self::assertTrue($support->has('sync'));
        self::assertTrue($support->has('copy'));
        self::assertTrue($support->has('remove'));
        self::assertTrue($support->has('read'));
        self::assertTrue($support->has('write'));
        self::assertTrue($support->has('move'));
        self::assertTrue($support->has('assertInitBind'));
        $abstractFilesystemObject->uninstallFunctionality($functionality);
        self::assertFalse($support->has('sync'));
        self::assertFalse($support->has('copy'));
        self::assertFalse($support->has('remove'));
        self::assertFalse($support->has('read'));
        self::assertFalse($support->has('write'));
        self::assertFalse($support->has('move'));
        self::assertFalse($support->has('assertInitBind'));
    }

}
