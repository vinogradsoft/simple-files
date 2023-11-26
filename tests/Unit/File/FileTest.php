<?php
declare(strict_types=1);

namespace Test\Unit\File;

use Test\Cases\AdditionalAssertCase;
use Vinograd\SimpleFiles\Event\FileBeforeWriteListener;
use Vinograd\SimpleFiles\File;
use Vinograd\SimpleFiles\FileFunctionalitiesContext;

class FileTest extends AdditionalAssertCase
{

    public function setUp(): void
    {

    }

    public function testGetSourcePath()
    {
        $file = new File('tests');
        self::assertEquals('tests', $file->getSourcePath());
    }

    public function testGetSourcePathWithBinded()
    {
        $file = File::createBinded(__FILE__);
        self::assertEquals(__FILE__, $file->getSourcePath());
    }

    public function testSetContent()
    {
        $file = new File('tests');
        $file->setContent('content');
        self::assertEquals('content', $file->getContent());
    }

    public function testAddFileBeforeWriteListener()
    {
        $file = new File('tests');
        $file->addFileBeforeWriteListener($listener1 = $this->getMockForAbstractClass(
            FileBeforeWriteListener::class
        ));
        $file->addFileBeforeWriteListener($listener2 = $this->getMockForAbstractClass(
            FileBeforeWriteListener::class
        ));
        $file->addFileBeforeWriteListener($listener3 = $this->getMockForAbstractClass(
            FileBeforeWriteListener::class
        ));

        $listeners = $file->getFileBeforeWriteListener();
        self::assertContains($listener1, $listeners);
        self::assertContains($listener2, $listeners);
        self::assertContains($listener3, $listeners);
        self::assertCount(3, $listeners);
    }

    public function testAddFileBeforeWriteListenerAlreadyExistsException()
    {
        $this->expectException(\LogicException::class);
        $file = new File('tests');
        $listener1 = $this->getMockForAbstractClass(
            FileBeforeWriteListener::class
        );
        $file->addFileBeforeWriteListener($listener1);
        $file->addFileBeforeWriteListener($listener1);
    }

    public function testRemoveFileBeforeWriteListener()
    {
        $file = new File('tests');
        $file->addFileBeforeWriteListener($listener1 = $this->getMockForAbstractClass(
            FileBeforeWriteListener::class
        ));
        $file->addFileBeforeWriteListener($listener2 = $this->getMockForAbstractClass(
            FileBeforeWriteListener::class
        ));
        $file->addFileBeforeWriteListener($listener3 = $this->getMockForAbstractClass(
            FileBeforeWriteListener::class
        ));
        $file->removeFileBeforeWriteListener($listener1);
        self::assertCount(2, $file->getFileBeforeWriteListener());

        $file->removeFileBeforeWriteListener($listener2);
        self::assertCount(1, $file->getFileBeforeWriteListener());

        $file->removeFileBeforeWriteListener($listener3);
        self::assertEmpty($file->getFileBeforeWriteListener());
    }

    public function testRemoveFileBeforeWriteListenerDoesNotExistException()
    {
        $this->expectException(\LogicException::class);
        $file = new File('tests');
        $listener1 = $this->getMockForAbstractClass(
            FileBeforeWriteListener::class
        );
        $file->addFileBeforeWriteListener($listener1);
        $file->removeFileBeforeWriteListener($listener1);
        $file->removeFileBeforeWriteListener($listener1);
    }

    public function testClearFileBeforeWriteListener()
    {
        $file = new File('tests');
        $file->addFileBeforeWriteListener($listener1 = $this->getMockForAbstractClass(
            FileBeforeWriteListener::class
        ));
        $file->addFileBeforeWriteListener($listener2 = $this->getMockForAbstractClass(
            FileBeforeWriteListener::class
        ));
        $file->addFileBeforeWriteListener($listener3 = $this->getMockForAbstractClass(
            FileBeforeWriteListener::class
        ));

        $file->clearFileBeforeWriteListener();
        self::assertEmpty($file->getFileBeforeWriteListener());
    }

    public function testRevokeAllSupports()
    {
        $file = new File('tests');
        $file->revokeAllSupports();
        $this->assertEmptyObjectFile($file);
    }

    public function testGetPath()
    {
        $file = new File('tests');
        $path = $file->getPath();
        $reflection = new \ReflectionObject($file);
        $property = $reflection->getProperty('pathObject');
        $property->setAccessible(true);
        $pathObjectValue = $property->getValue($file);
        self::assertNotSame($path, $pathObjectValue);
    }

    public function tearDown(): void
    {
        FileFunctionalitiesContext::reset();
    }
}
