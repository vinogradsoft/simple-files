<?php

namespace Test\Unit;

use BadMethodCallException;
use Test\Cases\Dummy\DummyFilesystem;
use Test\Cases\Helper\HelperForAbstractClasses;
use Vinograd\SimpleFiles\ProxyFilesystem;
use PHPUnit\Framework\TestCase;

class ProxyFilesystemTest extends TestCase
{
    public function testExtractFilesystem()
    {
        $proxyFilesystem = new ProxyFilesystem($filesystem = HelperForAbstractClasses::mockFilesystem());
        $fs = $proxyFilesystem->extractFilesystem();
        self::assertSame($filesystem, $fs);
    }

    public function testMethodList()
    {
        $proxyFilesystem = new ProxyFilesystem(new DummyFilesystem($this));

        $proxyFilesystem->removeFile('affected');
        $proxyFilesystem->removeDirectory('affected');
        $proxyFilesystem->createDirectory('affected');
        $proxyFilesystem->filePutContents('affected', '');

        self::assertTrue($proxyFilesystem->exists('affected'));
        self::assertEquals('affected', $proxyFilesystem->fileGetContents('affected'));
        self::assertTrue($proxyFilesystem->isDirectory('affected'));
        self::assertEquals('affected', $proxyFilesystem->yamlParseFile('affected'));
        self::assertTrue($proxyFilesystem->isFile('affected'));
        self::assertEquals('affected', $proxyFilesystem->getAbsolutePath('affected'));

    }

    public function testSetFilesystem()
    {
        $proxyFilesystem = new ProxyFilesystem($filesystem = HelperForAbstractClasses::mockFilesystem());
        $filesystem2 = HelperForAbstractClasses::mockFilesystem();
        $proxyFilesystem->setFilesystem($filesystem2);
        self::assertSame($filesystem2, $proxyFilesystem->extractFilesystem());
    }

    public function test__call()
    {
        $proxyFilesystem = new ProxyFilesystem($filesystem = HelperForAbstractClasses::mockFilesystem());
        $assert = 'assert';
        self::assertEquals($assert, $proxyFilesystem->testSomeMethod($assert));
    }

    public function test__callBadMethod()
    {
        $this->expectException(BadMethodCallException::class);
        $proxyFilesystem = new ProxyFilesystem(HelperForAbstractClasses::mockFilesystem());
        $proxyFilesystem->badMethod();
    }
}
