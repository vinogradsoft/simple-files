<?php

namespace Test\Unit\DefaultFilesystem;

use Test\Cases\FileSystemCase;
use Vinograd\IO\Exception\IOException;
use Vinograd\SimpleFiles\DefaultFilesystem;

class DefaultFilesystemForDirectoriesTest extends FileSystemCase
{
    private $filesystem;
    private $outPath;

    public function setUp(): void
    {
        $this->createDirectory($this->outPath = $this->getRuntimePath() .  '/DefaultFilesystemForDirectoriesTest');
        $this->filesystem = new DefaultFilesystem();
    }

    public function testCreateDirectory()
    {
        $this->filesystem->createDirectory($directory = $this->outPath . '/directory');
        self::assertDirectoryExists($directory);
    }

    public function testCreateDirectoryExcept()
    {
        $this->expectException(IOException::class);
        $this->createFile($file = $this->outPath . '/file.test', 'test');
        self::assertFileExists($file);//chek
        $this->filesystem->createDirectory($file);
    }

    public function testsRemoveDirectory()
    {
        $this->createDirectory($directory = $this->outPath . '/directory');
        self::assertDirectoryExists($directory);//check
        $this->filesystem->removeDirectory($directory);
        self::assertDirectoryNotExists($directory);
    }

    public function testsRemoveDirectoryExcept()
    {
        $this->expectException(IOException::class);
        $this->createFile($file = $this->outPath . '/file.test', 'test');
        self::assertFileExists($file);//check
        $this->filesystem->removeDirectory($file);
    }

    public function testsRemoveDirectoryEmptyPath()
    {
        $this->expectException(IOException::class);
        $this->filesystem->removeDirectory('');
    }

    public function testExists()
    {
        self::assertTrue($this->filesystem->exists($this->outPath));
        self::assertFalse($this->filesystem->exists($this->outPath . '/notfound'));
    }

    public function testIsDirectory()
    {
        $this->createFile($file = $this->outPath . '/file.test', 'test');
        self::assertFileExists($file);//check

        self::assertTrue($this->filesystem->isDirectory($this->outPath));
        self::assertFalse($this->filesystem->isDirectory($file));
    }

    public function tearDown(): void
    {
        $this->delete($this->outPath);
    }
}
