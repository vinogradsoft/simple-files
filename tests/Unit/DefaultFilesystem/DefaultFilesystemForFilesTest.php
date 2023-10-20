<?php
declare(strict_types=1);

namespace Test\Unit\DefaultFilesystem;

use Test\Cases\FileSystemCase;
use Vinograd\IO\Exception\IOException;
use Vinograd\SimpleFiles\DefaultFilesystem;

class DefaultFilesystemForFilesTest extends FileSystemCase
{
    private $filesystem;
    private $outPath;
    private $testFile;
    private $testContent;

    public function setUp(): void
    {
        $this->createDirectory($this->outPath = $this->getRuntimePath() . '/DefaultFilesystemForFilesTest');
        $this->createFile($this->testFile = $this->outPath . '/test.file', $this->testContent = 'test content');
        $this->filesystem = new DefaultFilesystem();
    }

    public function testExists()
    {
        self::assertTrue($this->filesystem->exists($this->testFile));
        self::assertFalse($this->filesystem->exists($this->outPath . '/not.found'));
    }

    public function testFileGetContents()
    {
        $content = $this->filesystem->fileGetContents($this->testFile);
        self::assertEquals($this->testContent, $content);
    }

    public function testFileGetContentsExcept()
    {
        $this->expectException(IOException::class);
        $this->filesystem->fileGetContents($this->testFile . 'bad');
    }

    public function testFilePutContents()
    {
        $this->filesystem->filePutContents($newFile = $this->outPath . '/new.file', 'test');
        self::assertFileExists($newFile);
    }

    public function testFilePutContentsExcept()
    {
        $this->expectException(IOException::class);
        $this->filesystem->filePutContents($this->outPath, 'test');
    }

    public function testRemoveFile()
    {
        $this->filesystem->removeFile($this->testFile);
        $this->assertFileDoesNotExist($this->testFile);
    }

    public function testRemoveFileExcept()
    {
        $this->expectException(IOException::class);
        $this->filesystem->removeFile($this->testFile . '4');
    }

    public function testYamlParseFile()
    {
        $this->createFile($testFile = $this->outPath . '/test.yml', 'test : 3');
        $result = $this->filesystem->yamlParseFile($testFile);
        self::assertCount(1, $result);
        self::assertEquals(3, $result['test']);
    }

    public function testYamlParseFileExcept()
    {
        $this->expectException(IOException::class);
        $this->filesystem->yamlParseFile($this->outPath);
    }

    public function testIsFile()
    {
        $this->createFile($file = $this->outPath . '/file.test', 'test');
        self::assertFileExists($file);//check

        self::assertTrue($this->filesystem->isFile($file));
        self::assertFalse($this->filesystem->isFile($this->outPath));
    }

    public function testGetAbsolutePath()
    {
        self::assertEquals(dirname(__DIR__, 3), $this->filesystem->getAbsolutePath('.'));
        self::assertEquals(dirname(__DIR__, 3).'/composer.json', $this->filesystem->getAbsolutePath('./composer.json'));
    }

    public function testGetAbsolutePathExcept()
    {
        $this->expectException(IOException::class); //работает по правила realpath только все false есть IOException
        $this->filesystem->getAbsolutePath('./bad/path');
    }

    public function tearDown(): void
    {
        $this->delete($this->outPath);
    }
}
