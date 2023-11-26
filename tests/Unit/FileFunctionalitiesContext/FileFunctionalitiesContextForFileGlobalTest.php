<?php
declare(strict_types=1);

namespace Test\Unit\FileFunctionalitiesContext;

use BadMethodCallException;
use Test\Cases\Dummy\DummyFunctionality;
use Vinograd\SimpleFiles\AbstractFilesystemObject;
use Vinograd\SimpleFiles\FileFunctionalitiesContext;
use PHPUnit\Framework\TestCase;

class FileFunctionalitiesContextForFileGlobalTest extends TestCase
{
    public function testRegisterGlobalFunctionalityForFiles()
    {
        $functionality = new  DummyFunctionality();
        FileFunctionalitiesContext::registerGlobalFunctionalityForFiles($functionality, 'get');
        self::assertTrue(FileFunctionalitiesContext::hasGlobalFunctionalityForFiles('get'));
    }

    public function testUnregisterGlobalFunctionalityForFiles()
    {
        $functionality = new  DummyFunctionality();
        FileFunctionalitiesContext::registerGlobalFunctionalityForFiles($functionality, 'get');
        FileFunctionalitiesContext::unregisterGlobalFunctionalityForFiles('get');
        self::assertFalse(FileFunctionalitiesContext::hasGlobalFunctionalityForFiles('get'));
    }

    public function testUnregisterGlobalFunctionalityForFileExcept()
    {
        $this->expectException(\LogicException::class);
        FileFunctionalitiesContext::unregisterGlobalFunctionalityForFiles('notInstallAnyMethod');
    }

    public function testHasGlobalFunctionalityForFiles()
    {
        self::assertFalse(FileFunctionalitiesContext::hasGlobalFunctionalityForFiles('get'));
        $functionality = new  DummyFunctionality();
        FileFunctionalitiesContext::registerGlobalFunctionalityForFiles($functionality, 'get');
        self::assertTrue(FileFunctionalitiesContext::hasGlobalFunctionalityForFiles('get'));
        self::assertFalse(FileFunctionalitiesContext::hasGlobalFunctionalityForFiles('AnyMethod'));
    }

    public function testFireGlobalFileMethod()
    {
        $functionality = new  DummyFunctionality();
        FileFunctionalitiesContext::registerGlobalFunctionalityForFiles($functionality, 'get');
        $fsObject = $this->getMockForAbstractClass(AbstractFilesystemObject::class);
        $result = FileFunctionalitiesContext::fireGlobalFileMethod($fsObject, 'get', []);
        self::assertSame($fsObject, $result);
    }

    public function testFireGlobalFileMethodExceptNonexistentMethod()
    {
        $this->expectException(BadMethodCallException::class);
        $functionality = new  DummyFunctionality();
        FileFunctionalitiesContext::registerGlobalFunctionalityForFiles($functionality, 'get');
        $fsObject = $this->getMockForAbstractClass(AbstractFilesystemObject::class);
        FileFunctionalitiesContext::fireGlobalFileMethod($fsObject, 'Nonexistent', []);
    }

    public function testFireGlobalFileMethodExceptNotInitialAnyMethods()
    {
        $this->expectException(BadMethodCallException::class);
        $fsObject = $this->getMockForAbstractClass(AbstractFilesystemObject::class);
        FileFunctionalitiesContext::fireGlobalFileMethod($fsObject, 'Nonexistent', []);
    }

    public function tearDown(): void
    {
        FileFunctionalitiesContext::reset();
    }
}
