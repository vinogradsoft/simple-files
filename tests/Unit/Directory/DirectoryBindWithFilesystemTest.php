<?php

namespace Test\Unit\Directory;

use Test\Cases\FileSystemCase;
use Vinograd\IO\Exception\IOException;
use Vinograd\SimpleFiles\Directory;
use Vinograd\SimpleFiles\File;
use Vinograd\SimpleFiles\FileFunctionalitiesContext;

class DirectoryBindWithFilesystemTest extends FileSystemCase
{
    private $outPath;

    public function setUp(): void
    {
        $this->createDirectory($this->outPath = $this->getRuntimePath() .  '/DirectoryBindWithFilesystemTest');
    }

    public function testBindWithFilesystem()
    {
        $rootLocalPath = '/root';
        $directory1LocalPath = '/root/directory1';
        $file1LocalPath = '/root/directory1/file1.txt';
        $directory2LocalPath = '/root/directory1/directory2';
        $file2LocalPath = '/root/directory1/directory2/file2.txt';

        $file1PathControl = $this->outPath . $file1LocalPath;
        $file2PathControl = $this->outPath . $file2LocalPath;
        $rootPathControl = $this->outPath . $rootLocalPath;
        $directory1PathControl = $this->outPath . $directory1LocalPath;
        $directory2PathControl = $this->outPath . $directory2LocalPath;

        $root = new Directory('root');
        $directory1 = new  Directory('directory1');
        $directory2 = new  Directory('directory2');
        $file1 = new  File('file1.txt');
        $file2 = new  File('file2.txt');

        $directory1->addFile($file1);
        $directory1->addDirectory($directory2);
        $directory2->addFile($file2);
        $root->addDirectory($directory1);


        self::assertEquals($file1LocalPath, $file1->getLocalPath('/'));
        self::assertEquals($file2LocalPath, $file2->getLocalPath('/'));
        self::assertEquals($rootLocalPath, $root->getLocalPath('/'));
        self::assertEquals($directory1LocalPath, $directory1->getLocalPath('/'));
        self::assertEquals($directory2LocalPath, $directory2->getLocalPath('/'));

        $root->bindWithFilesystem($this->outPath);

        self::assertEquals($file1PathControl, $file1->getPath()->getSource());
        self::assertEquals($file2PathControl, $file2->getPath()->getSource());
        self::assertEquals($rootPathControl, $root->getPath()->getSource());
        self::assertEquals($directory1PathControl, $directory1->getPath()->getSource());
        self::assertEquals($directory2PathControl, $directory2->getPath()->getSource());

        self::assertFileExists($file1->getPath()->getSource());
        self::assertFileExists($file2->getPath()->getSource());
        self::assertDirectoryExists($root->getPath()->getSource());
        self::assertDirectoryExists($directory1->getPath()->getSource());
        self::assertDirectoryExists($directory2->getPath()->getSource());
    }

    public function testBindWithFilesystemBadPath()
    {
        $this->expectException(IOException::class);
        $root = new Directory('root');
        $directory1 = new Directory('directory1');
        $directory2 = new Directory('directory2');
        $file1 = new File('file1.txt');
        $file2 = new File('file2.txt');

        $directory1->addFile($file1);
        $directory1->addDirectory($directory2);
        $directory2->addFile($file2);
        $root->addDirectory($directory1);
        $root->bindWithFilesystem('bad/path');
    }

    public function testIsBinded()
    {
        $root = new  Directory('root');
        $directory1 = new  Directory('directory1');
        $directory2 = new  Directory('directory2');
        $file1 = new  File('file1.txt');
        $file2 = new  File('file2.txt');

        $directory1->addFile($file1);
        $directory1->addDirectory($directory2);
        $directory2->addFile($file2);
        $root->addDirectory($directory1);

        self::assertFalse($root->isBinded());
        self::assertFalse($directory1->isBinded());
        self::assertFalse($directory2->isBinded());
        self::assertFalse($file1->isBinded());
        self::assertFalse($file2->isBinded());

        $root->bindWithFilesystem($this->outPath);

        self::assertTrue($root->isBinded());
        self::assertTrue($directory1->isBinded());
        self::assertTrue($directory2->isBinded());
        self::assertTrue($file1->isBinded());
        self::assertTrue($file2->isBinded());
    }

    public function tearDown(): void
    {
        FileFunctionalitiesContext::reset();
        $this->delete($this->outPath);
    }
}
