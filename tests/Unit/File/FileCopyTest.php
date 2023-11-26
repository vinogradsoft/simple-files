<?php
declare(strict_types=1);

namespace Test\Unit\File;

use Test\Cases\AdditionalAssertCase;
use Vinograd\IO\Exception\IOException;
use Compass\Exception\InvalidPathException;
use Vinograd\SimpleFiles\File;

class FileCopyTest extends AdditionalAssertCase
{
    protected $outDirectoryName = 'FileCopyTest';

    public function testCopy()
    {
        $this->createDirectory($copyDir1 = $this->outPath . '/copy1');
        $this->createDirectory($copyDir2 = $this->outPath . '/copy2');
        $this->createDirectory($copyDir3 = $this->outPath . '/copy3');

        $this->createFile($filePath = $this->outPath . '/test.file', 'content');

        $file = File::createBinded($filePath);
        $file->copy($copyDir1);
        $file->copy($copyDir2);
        $file->copy($copyDir3);

        self::assertFileExists($copyDir1 . '/test.file');
        self::assertFileExists($copyDir2 . '/test.file');
        self::assertFileExists($copyDir3 . '/test.file');

        self::assertEquals('content', file_get_contents($copyDir1 . '/test.file'));
        self::assertEquals('content', file_get_contents($copyDir2 . '/test.file'));
        self::assertEquals('content', file_get_contents($copyDir3 . '/test.file'));
    }

    public function testCopyWithSetLocalName()
    {
        $this->createDirectory($copyDir1 = $this->outPath . '/copy1');
        $this->createDirectory($copyDir2 = $this->outPath . '/copy2');
        $this->createDirectory($copyDir3 = $this->outPath . '/copy3');

        $this->createFile($filePath = $this->outPath . '/test.file', 'content');

        $file = File::createBinded($filePath);
        $file->copy($copyDir1);
        $file->copy($copyDir2);
        $file->setLocalName('fileRenamed.txt');
        $file->copy($copyDir3);

        self::assertFileExists($copyDir1 . '/test.file');
        self::assertFileExists($copyDir2 . '/test.file');
        self::assertFileExists($copyDir3 . '/fileRenamed.txt');

        self::assertEquals('content', file_get_contents($copyDir1 . '/test.file'));
        self::assertEquals('content', file_get_contents($copyDir2 . '/test.file'));
        self::assertEquals('content', file_get_contents($copyDir3 . '/fileRenamed.txt'));
    }

    public function testCopyToNotExistsDirectory()
    {
        $this->expectException(IOException::class);
        $this->createFile($filePath = $this->outPath . '/test.file', 'content');

        $file = File::createBinded($filePath);
        $file->copy('not/exist/directory/path');
    }

    public function testCopyFromNonExistentSource()
    {
        $this->expectException(IOException::class);
        $this->createDirectory($dir = $this->outPath . '/outPath');
        $this->createFile($filePath = $this->outPath . '/test.file', 'content');

        $file = File::createBinded($filePath);
        $this->delete($filePath);//что-то пошло не так
        $file->copy($dir);
    }

    public function testCopyNotBinded()
    {
        $this->createDirectory($copyDir1 = $this->outPath . '/copy1');
        $this->createDirectory($copyDir2 = $this->outPath . '/copy2');
        $this->createDirectory($copyDir3 = $this->outPath . '/copy3');

        $file = new File('test.file');
        $file->setContent('content');
        $file->copy($copyDir1);
        $file->copy($copyDir2);
        $file->copy($copyDir3);
        self::assertFileExists($copyDir1 . '/test.file');
        self::assertFileExists($copyDir2 . '/test.file');
        self::assertFileExists($copyDir3 . '/test.file');

        self::assertEquals('content', file_get_contents($copyDir1 . '/test.file'));
        self::assertEquals('content', file_get_contents($copyDir2 . '/test.file'));
        self::assertEquals('content', file_get_contents($copyDir3 . '/test.file'));
    }

    public function testCopyNotBindedWithSetLocalName()
    {
        $this->createDirectory($copyDir1 = $this->outPath . '/copy1');
        $this->createDirectory($copyDir2 = $this->outPath . '/copy2');
        $this->createDirectory($copyDir3 = $this->outPath . '/copy3');

        $file = new File('test.file');
        $file->setContent('content');
        $file->copy($copyDir1);
        $file->copy($copyDir2);
        $file->setLocalName('fileRenamed.txt');
        $file->copy($copyDir3);
        self::assertFileExists($copyDir1 . '/test.file');
        self::assertFileExists($copyDir2 . '/test.file');
        self::assertFileExists($copyDir3 . '/fileRenamed.txt');

        self::assertEquals('content', file_get_contents($copyDir1 . '/test.file'));
        self::assertEquals('content', file_get_contents($copyDir2 . '/test.file'));
        self::assertEquals('content', file_get_contents($copyDir3 . '/fileRenamed.txt'));
    }

    public function testCopyNotBindedBadPath()
    {
        $this->expectException(InvalidPathException::class);
        $file = new File('test.file');
        $file->setContent('content');
        $file->copy('bad/path1');
    }
}