<?php

namespace Test\Unit\Functionality;

use BadMethodCallException;
use Test\Cases\Dummy\DummyAbstractFilesystemObject;
use Vinograd\IO\Filesystem;
use Vinograd\SimpleFiles\FileFunctionalitiesContext;
use Vinograd\SimpleFiles\Functionality\DirectoryFunctionality;
use PHPUnit\Framework\TestCase;
use Vinograd\Support\Exception\NotSupportedArgumentException;
use Vinograd\Support\SupportedFunctionalities;

class DirectoryFunctionalityTest extends TestCase
{
    public function testCreate()
    {
        $functionality = DirectoryFunctionality::create($this->getMockForAbstractClass(SupportedFunctionalities::class));
        $functionality2 = DirectoryFunctionality::create($this->getMockForAbstractClass(SupportedFunctionalities::class));
        self::assertSame($functionality, $functionality2);
    }

    public function testUninstallMethods()
    {
        $abstractFilesystemObject = new DummyAbstractFilesystemObject();
        $abstractFilesystemObject->installFunctionality($functionality = DirectoryFunctionality::create($abstractFilesystemObject));
        $support = FileFunctionalitiesContext::getFunctionalitySupport($abstractFilesystemObject);
        self::assertTrue($support->has('sync'));
        self::assertTrue($support->has('copy'));
        self::assertTrue($support->has('delete'));
        self::assertTrue($support->has('assertInitBind'));
        $abstractFilesystemObject->uninstallFunctionality($functionality);
        self::assertFalse($support->has('sync'));
        self::assertFalse($support->has('copy'));
        self::assertFalse($support->has('delete'));
        self::assertFalse($support->has('assertInitBind'));
    }

    public function testCheckArguments()
    {
        $this->expectException(NotSupportedArgumentException::class);
        FileFunctionalitiesContext::setFilesystem($this->getMockForAbstractClass(Filesystem::class));
        $abstractFilesystemObject = new DummyAbstractFilesystemObject();
        $abstractFilesystemObject->installFunctionality(DirectoryFunctionality::create($abstractFilesystemObject));
        $abstractFilesystemObject->delete('path/to','path2/to');
    }

    public function tearDown(): void
    {
        FileFunctionalitiesContext::reset();
    }
}
