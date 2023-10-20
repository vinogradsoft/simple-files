<?php
declare(strict_types=1);

namespace Test\Unit\File;

use Test\Cases\AdditionalAssertCase;
use Vinograd\IO\Exception\IOException;
use Compass\Exception\InvalidPathException;
use Vinograd\SimpleFiles\File;

class FileMoveTest extends AdditionalAssertCase
{
    protected $outDirectoryName = 'FileMoveTest';

    public function testMove()
    {
        $this->createDirectory($moveDir = $this->outPath . '/move');
        $this->createFile($filePath = $this->outPath . '/test.file', 'content');

        $file = File::createBinded($filePath);
        $file->move($moveDir);

        self::assertFileExists($moveDir . '/test.file');
        self::assertFileDoesNotExist($filePath);

        self::assertEquals('content', file_get_contents($moveDir . '/test.file'));
    }

    public function testMoveWithSetLocalName()
    {
        $this->createDirectory($moveDir = $this->outPath . '/move');
        $this->createFile($filePath = $this->outPath . '/test.file', 'content');

        $file = File::createBinded($filePath);
        $file->setLocalName('fileRenamed.txt');
        $file->move($moveDir);

        self::assertFileExists($moveDir . '/fileRenamed.txt');
        self::assertFileDoesNotExist($filePath);

        self::assertEquals('content', file_get_contents($moveDir . '/fileRenamed.txt'));
    }

    public function testMoveToNotExistsDirectory()
    {
        $this->expectException(IOException::class);
        $this->createFile($filePath = $this->outPath . '/test.file', 'content');

        $file = File::createBinded($filePath);
        $file->move('not/exist/directory/path');
    }

    public function testMoveFromNonExistentSource()
    {
        $this->expectException(IOException::class);
        $this->createDirectory($dir = $this->outPath . '/outPath');
        $this->createFile($filePath = $this->outPath . '/test.file', 'content');

        $file = File::createBinded($filePath);
        $this->delete($filePath);//что-то пошло не так
        $file->move($dir);
    }

    public function testMoveNotBinded()
    {
        $this->createDirectory($moveDir = $this->outPath . '/move');
        $file = new File('test.file');
        $file->setContent('content');
        $file->move($moveDir);

        self::assertFileExists($moveDir . '/test.file');
        self::assertTrue($file->isBinded());
        self::assertEquals('content', file_get_contents($moveDir . '/test.file'));
    }

    public function testMoveNotBindedSetLocalName()
    {
        $this->createDirectory($moveDir = $this->outPath . '/move');
        $file = new File('test.file');
        $file->setContent('content');
        $file->setLocalName('fileRenamed.txt');
        $file->move($moveDir);

        self::assertFileExists($moveDir . '/fileRenamed.txt');
        self::assertTrue($file->isBinded());
        self::assertEquals('content', file_get_contents($moveDir . '/fileRenamed.txt'));
    }

    public function testMoveNotBindedBadPath()
    {
        $this->expectException(InvalidPathException::class);
        $file = new File('test.file');
        $file->setContent('content');
        $file->move('bad/path1');
    }
}