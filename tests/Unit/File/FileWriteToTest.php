<?php
declare(strict_types=1);

namespace Test\Unit\File;

use Test\Cases\AdditionalAssertCase;
use Vinograd\IO\Exception\IOException;
use Compass\Exception\InvalidPathException;
use Vinograd\SimpleFiles\File;

class FileWriteToTest extends AdditionalAssertCase
{
    protected $outDirectoryName = 'FileWriteToTest';

    public function testWriteToBinded()
    {
        $this->createDirectory($outDir = $this->outPath . '/outDir');
        $this->createFile($filePath = $this->outPath . '/test.file', 'content');
        $file = File::createBinded($filePath);
        $file->setContent('change content');
        $file->writeTo($outDir);

        self::assertFileExists($outDir . '/test.file');
        self::assertEquals('change content', file_get_contents($outDir . '/test.file'));
    }

    public function testWriteToBindedWithSetLocalName()
    {
        $this->createDirectory($outDir = $this->outPath . '/outDir');
        $this->createFile($filePath = $this->outPath . '/test.file', 'content');
        $file = File::createBinded($filePath);
        $file->setContent('change content');
        $file->setLocalName('fileRenamed.txt');
        $file->writeTo($outDir);

        self::assertFileExists($outDir . '/fileRenamed.txt');
        self::assertEquals('change content', file_get_contents($outDir . '/fileRenamed.txt'));
    }

    public function testWriteToNotBinded()
    {
        $this->createDirectory($outDir = $this->outPath . '/outDir');
        $file = new File('test.file');
        $file->setContent('content');
        $file->writeTo($outDir);

        self::assertFileExists($outDir . '/test.file');
        self::assertEquals('content', file_get_contents($outDir . '/test.file'));
    }
    public function testWriteToNotBindedWithSetLocalName()
    {
        $this->createDirectory($outDir = $this->outPath . '/outDir');
        $file = new File('test.file');
        $file->setContent('content');
        $file->setLocalName('fileRenamed.txt');
        $file->writeTo($outDir);

        self::assertFileExists($outDir . '/fileRenamed.txt');
        self::assertEquals('content', file_get_contents($outDir . '/fileRenamed.txt'));
    }

    public function testWriteToInvalidPath()
    {
        $this->expectException(InvalidPathException::class);
        $this->createDirectory($dir = $this->outPath . '/outPath');
        $this->createFile($filePath = $dir . '/test.file', 'content');
        $file = File::createBinded($filePath);
        $file->setContent('change content');
        $file->writeTo('bad/path');
    }

}