<?php
declare(strict_types=1);

namespace Test\Unit\File;

use Test\Cases\AdditionalAssertCase;
use Compass\Exception\InvalidPathException;
use Vinograd\SimpleFiles\File;

class FileWriteTest extends AdditionalAssertCase
{
    protected $outDirectoryName = 'FileWriteTest';

    public function testWrite()
    {
        $this->createFile($filePath = $this->outPath . '/test.file', 'content');
        $file = File::createBinded($filePath);
        $file->setContent('change content');
        $file->write();

        self::assertFileExists($filePath);
        self::assertEquals('change content', file_get_contents($filePath));
    }

    public function testWriteWithSetLocalName()
    {
        $this->createFile($filePath = $this->outPath . '/test.file', 'content');
        $file = File::createBinded($filePath);
        $file->setContent('change content');
        $file->setLocalName('fileRenamed.txt');
        $file->write();

        self::assertFileExists($filePath);
        self::assertEquals('change content', file_get_contents($filePath));
    }

    public function testWriteBinded()
    {
        $file = new File('test.file');
        $file->bindWithFilesystem($this->outPath);
        $file->setContent('change content');
        $file->write();

        self::assertFileExists($this->outPath . '/test.file');
        self::assertEquals('change content', file_get_contents($this->outPath . '/test.file'));
    }

    public function testWriteBindedWithSetLocalName()
    {
        $file = new File('test.file');
        $file->bindWithFilesystem($this->outPath);
        $file->setContent('change content');
        $file->setLocalName('fileRenamed.txt');
        $file->write();

        self::assertFileExists($this->outPath . '/test.file');
        self::assertEquals('change content', file_get_contents($this->outPath . '/test.file'));
    }

    public function testWriteNotBinded()
    {
        $this->expectException(\LogicException::class);
        $file = new  File('test.file');
        $file->setContent('change content');
        $file->write();
    }

    public function testWriteInvalidPath()
    {
        $this->expectException(InvalidPathException::class);
        $this->createDirectory($dir = $this->outPath . '/outPath');
        $this->createFile($filePath = $dir . '/test.file', 'content');
        $file = File::createBinded($filePath);
        $file->setContent('change content');

        $this->delete($dir);//что-то пошло не так
        $file->write();
    }

}