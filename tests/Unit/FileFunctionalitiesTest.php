<?php

namespace Test\Unit;

use BadMethodCallException;
use stdClass;
use Vinograd\IO\Filesystem;
use Vinograd\SimpleFiles\FileFunctionalities;
use PHPUnit\Framework\TestCase;
use Vinograd\Support\Event\CallMethodEvent;
use Vinograd\Support\Event\MethodCallListener;

class FileFunctionalitiesTest extends TestCase
{
    public function testGetFilesystem()
    {
        $filesystem = $this->getMockForAbstractClass(Filesystem::class);
        $fileFunctionalities = new FileFunctionalities($filesystem);
        self::assertSame($filesystem, $fileFunctionalities->getFilesystem());
    }

    public function testSetFilesystem()
    {
        $filesystem = $this->getMockForAbstractClass(Filesystem::class);
        $filesystem2 = $this->getMockForAbstractClass(Filesystem::class);
        $fileFunctionalities = new FileFunctionalities($filesystem);
        $fileFunctionalities->setFilesystem($filesystem2);
        self::assertSame($filesystem2, $fileFunctionalities->getFilesystem());
    }

    public function testFireCallMethodEvent()
    {
        $filesystem = $this->getMockForAbstractClass(Filesystem::class);
        $fileFunctionalities = new FileFunctionalities($filesystem);

        $class = new class() implements MethodCallListener {
            public function methodCalled(CallMethodEvent $evt, $filesystem = null)
            {
                if ($filesystem instanceof Filesystem) {
                    FileFunctionalitiesTest::assertTrue(true);
                } else {
                    FileFunctionalitiesTest::fail();
                }
                if ($evt->getMethod() === 'method') {
                    FileFunctionalitiesTest::assertTrue(true);
                } else {
                    FileFunctionalitiesTest::fail();
                }
                FileFunctionalitiesTest::assertEquals(['arguments'], $evt->getArguments());
            }
        };
        $fileFunctionalities->installMethod($class, 'method');
        $fileFunctionalities->fireCallMethodEvent(new StdClass(), 'method', ['arguments']);
    }

    public function testFireCallMethodEventBadMethodCallException()
    {
        $this->expectException(BadMethodCallException::class);
        $filesystem = $this->getMockForAbstractClass(Filesystem::class);
        $fileFunctionalities = new FileFunctionalities($filesystem);

        $class = new class() implements MethodCallListener {
            public function methodCalled(CallMethodEvent $evt, $filesystem = null)
            {
            }
        };
        $fileFunctionalities->installMethod($class, 'method');
        $fileFunctionalities->fireCallMethodEvent(new StdClass(), 'badMethodName', ['arguments']);
    }

}
