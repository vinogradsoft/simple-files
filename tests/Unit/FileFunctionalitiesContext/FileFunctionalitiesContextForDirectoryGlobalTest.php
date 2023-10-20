<?php
declare(strict_types=1);

namespace Test\Unit\FileFunctionalitiesContext;

use BadMethodCallException;
use Test\Cases\Dummy\DummyFunctionality;
use Vinograd\SimpleFiles\AbstractFilesystemObject;
use Vinograd\SimpleFiles\FileFunctionalitiesContext;
use PHPUnit\Framework\TestCase;

class FileFunctionalitiesContextForDirectoryGlobalTest extends TestCase
{
    public function testRegisterGlobalFunctionalityForDirectories()
    {
        $functionality = new  DummyFunctionality();
        FileFunctionalitiesContext::registerGlobalFunctionalityForDirectories($functionality, 'get');
        self::assertTrue(FileFunctionalitiesContext::hasGlobalFunctionalityForDirectories('get'));
    }

    public function testUnregisterGlobalFunctionalityForDirectories()
    {
        $functionality = new  DummyFunctionality();
        FileFunctionalitiesContext::registerGlobalFunctionalityForDirectories($functionality, 'get');
        FileFunctionalitiesContext::unregisterGlobalFunctionalityForDirectories('get');
        self::assertFalse(FileFunctionalitiesContext::hasGlobalFunctionalityForDirectories('get'));
    }

    public function testUnregisterGlobalFunctionalityForDirectoriesExcept()
    {
        $this->expectException(\LogicException::class);
        FileFunctionalitiesContext::unregisterGlobalFunctionalityForDirectories('notInstallAnyMethod');
    }

    public function testHasGlobalFunctionalityForDirectories()
    {
        self::assertFalse(FileFunctionalitiesContext::hasGlobalFunctionalityForDirectories('get'));
        $functionality = new  DummyFunctionality();
        FileFunctionalitiesContext::registerGlobalFunctionalityForDirectories($functionality, 'get');
        self::assertTrue(FileFunctionalitiesContext::hasGlobalFunctionalityForDirectories('get'));
        self::assertFalse(FileFunctionalitiesContext::hasGlobalFunctionalityForDirectories('AnyMethod'));
    }

    public function testFireGlobalDirectoryMethod()
    {
        $functionality = new  DummyFunctionality();
        FileFunctionalitiesContext::registerGlobalFunctionalityForDirectories($functionality, 'get');
        $fsObject = $this->getMockForAbstractClass(AbstractFilesystemObject::class);
        $result = FileFunctionalitiesContext::fireGlobalDirectoryMethod($fsObject, 'get', []);
        self::assertSame($fsObject, $result);
    }

    public function testFireGlobalDirectoryMethodExceptNonexistentMethod()
    {
        $this->expectException(BadMethodCallException::class);
        $functionality = new  DummyFunctionality();
        FileFunctionalitiesContext::registerGlobalFunctionalityForDirectories($functionality, 'get');
        $fsObject = $this->getMockForAbstractClass(AbstractFilesystemObject::class);
        FileFunctionalitiesContext::fireGlobalDirectoryMethod($fsObject, 'Nonexistent', []);
    }

    public function testFireGlobalDirectoryMethodExceptNotInitialAnyMethods()
    {
        $this->expectException(BadMethodCallException::class);
        $fsObject = $this->getMockForAbstractClass(AbstractFilesystemObject::class);
        FileFunctionalitiesContext::fireGlobalDirectoryMethod($fsObject, 'Nonexistent', []);
    }

    public function tearDown(): void
    {
        FileFunctionalitiesContext::reset();
    }
}
