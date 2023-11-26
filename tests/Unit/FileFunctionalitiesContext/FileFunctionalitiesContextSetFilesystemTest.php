<?php
declare(strict_types=1);

namespace Test\Unit\FileFunctionalitiesContext;

use PHPUnit\Framework\TestCase;
use Test\Cases\Dummy\DummyFunctionalityGetFilesystem;
use Test\Cases\Dummy\ErrorEmulatorFilesystem;
use Test\Cases\Helper\HelperForAbstractClasses;
use Vinograd\SimpleFiles\AbstractFilesystemObject;
use Vinograd\SimpleFiles\DefaultFilesystem;
use Vinograd\SimpleFiles\FileFunctionalitiesContext;

class FileFunctionalitiesContextSetFilesystemTest extends TestCase
{

    public function testSetFilesystemForGroup()
    {
        $group1 = FileFunctionalitiesContext::getGroupFunctionalitySupport('group1');
        $group2 = FileFunctionalitiesContext::getGroupFunctionalitySupport('group2');
        $group3 = FileFunctionalitiesContext::getGroupFunctionalitySupport('group3');
        $group4 = FileFunctionalitiesContext::getGroupFunctionalitySupport('group4');
        $fs1 = $group1->getFilesystem()->extractFilesystem();
        $fs2 = $group2->getFilesystem()->extractFilesystem();
        $fs3 = $group3->getFilesystem()->extractFilesystem();
        $fs4 = $group4->getFilesystem()->extractFilesystem();
        self::assertSame($fs1, $fs2);
        self::assertSame($fs1, $fs3);
        self::assertSame($fs1, $fs4);

        FileFunctionalitiesContext::setFilesystem($newFilesystem = new ErrorEmulatorFilesystem());

        $fs1control = $group1->getFilesystem()->extractFilesystem();
        $fs2control = $group2->getFilesystem()->extractFilesystem();
        $fs3control = $group3->getFilesystem()->extractFilesystem();
        $fs4control = $group4->getFilesystem()->extractFilesystem();
        self::assertSame($newFilesystem, $fs1control);
        self::assertSame($newFilesystem, $fs2control);
        self::assertSame($newFilesystem, $fs3control);
        self::assertSame($newFilesystem, $fs4control);

        self::assertNotSame($fs1, $newFilesystem);
        self::assertNotSame($fs2, $newFilesystem);
        self::assertNotSame($fs3, $newFilesystem);
        self::assertNotSame($fs4, $newFilesystem);

        if ($newFilesystem instanceof ErrorEmulatorFilesystem) {
            self::assertTrue(true);
        } else {
            self::fail();
        }
    }

    public function testSetFilesystemForGlobal()
    {
        $functionality = new  DummyFunctionalityGetFilesystem();
        FileFunctionalitiesContext::registerGlobalFunctionalityForDirectories($functionality, 'get');
        $fsObject = $this->getMockForAbstractClass(AbstractFilesystemObject::class);
        $result = FileFunctionalitiesContext::fireGlobalDirectoryMethod($fsObject, 'get', []);
        $realFilesystem = $result->extractFilesystem();

        if ($realFilesystem instanceof DefaultFilesystem) {
            self::assertTrue(true);
        } else {
            self::fail();
        }

        if ($realFilesystem instanceof ErrorEmulatorFilesystem) {
            self::fail();
        } else {
            self::assertTrue(true);
        }

        FileFunctionalitiesContext::setFilesystem($newFilesystem = new ErrorEmulatorFilesystem());

        $resultControl = FileFunctionalitiesContext::fireGlobalDirectoryMethod($fsObject, 'get', []);
        $newRealFilesystem = $resultControl->extractFilesystem();

        self::assertSame($newRealFilesystem, $newFilesystem);
    }

    public function testSetFilesystemForPersonal()
    {
        $file = HelperForAbstractClasses::mockFile('/');
        $functionality = new  DummyFunctionalityGetFilesystem();
        $functionality->install($file);
        $filesystem = $file->get();

        if ($filesystem instanceof DefaultFilesystem) {
            self::assertTrue(true);
        } else {
            self::fail();
        }

        if ($filesystem instanceof ErrorEmulatorFilesystem) {
            self::fail();
        } else {
            self::assertTrue(true);
        }

        FileFunctionalitiesContext::setFilesystem($newFilesystem = new ErrorEmulatorFilesystem());

        $fileControl = HelperForAbstractClasses::mockFile('/');
        $functionality->install($fileControl);
        $filesystemControl = $fileControl->get();
        self::assertSame($newFilesystem, $filesystemControl);
        self::assertNotSame($newFilesystem, $filesystem);

        $filesystemCheck = $file->get();
        self::assertNotSame($filesystemCheck, $newFilesystem);
        self::assertSame($filesystemCheck, $filesystem);
    }

    public function tearDown(): void
    {
        FileFunctionalitiesContext::reset();
    }
}