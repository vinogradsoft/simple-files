<?php
declare(strict_types=1);

namespace Test\Unit\AbstractDirectory;

use Test\Cases\CommonCase;
use Vinograd\IO\Exception\AlreadyExistException;
use Vinograd\IO\Exception\NotFoundException;
use Vinograd\SimpleFiles\AbstractDirectory;
use Vinograd\SimpleFiles\AbstractFile;
use Vinograd\SimpleFiles\Exception\TreeException;

class AbstractDirectoryWorkFilesTest extends CommonCase
{
    public function testAddFile()
    {
        $root = $this->getMockForAbstractClass(AbstractDirectory::class, ['root']);
        $file1 = $this->getMockForAbstractClass(AbstractFile::class, ['path/to/file1']);
        $file2 = $this->getMockForAbstractClass(AbstractFile::class, ['the/path/to/file2']);

        $root->addFile($file1);
        $root->addFile($file2);
        self::assertSame($root, $file1->getParent());
        self::assertSame($root, $file2->getParent());
    }

    public function testAddFileConsistency()
    {
        $directory1 = $this->getMockForAbstractClass(AbstractDirectory::class, ['root1']);
        $directory2 = $this->getMockForAbstractClass(AbstractDirectory::class, ['path/to/root']);
        $file = $this->getMockForAbstractClass(AbstractFile::class, ['the/path/to/child.file']);

        $directory1->addFile($file);
        $directory2->addFile($file);

        $files1 = $directory1->getFiles();
        $files2 = $directory2->getFiles();
        self::assertCount(0, $files1);
        self::assertCount(1, $files2);
        self::assertSame($files2[$file->getName()], $file);

        $directory1->addFile($file);

        $files1 = $directory1->getFiles();
        $files2 = $directory2->getFiles();

        self::assertCount(1, $files1);
        self::assertCount(0, $files2);
        self::assertSame($files1[$file->getName()], $file);
    }

    public function testAddFileAlreadyExist()
    {
        $this->expectException(AlreadyExistException::class);
        $root = $this->getMockForAbstractClass(AbstractDirectory::class, ['root']);
        $file = $this->getMockForAbstractClass(AbstractFile::class, ['path/to/file']);

        $root->addFile($file);
        $root->addFile($file);
    }

    public function testGetFiles()
    {
        $directory = $this->getMockForAbstractClass(AbstractDirectory::class, ['root']);
        $file1 = $this->getMockForAbstractClass(AbstractFile::class, ['path/to/file1']);
        $file2 = $this->getMockForAbstractClass(AbstractFile::class, ['the/path/to/file2']);

        $directory->addFile($file1);
        $directory->addFile($file2);
        $files = $directory->getFiles();
        self::assertCount(2, $files);
        self::assertArrayHasKey($file1->getName(), $files);
        self::assertArrayHasKey($file2->getName(), $files);

        self::assertSame($files[$file1->getName()], $file1);
        self::assertSame($files[$file2->getName()], $file2);
    }

    public function testGetFileBy()
    {
        $directory = $this->getMockForAbstractClass(AbstractDirectory::class, ['root']);
        $file1 = $this->getMockForAbstractClass(AbstractFile::class, ['path/to/file1']);
        $file2 = $this->getMockForAbstractClass(AbstractFile::class, ['the/path/to/file2']);
        $directory->addFile($file1);
        $directory->addFile($file2);
        $file1Control = $directory->getFileBy('file1');
        $file2Control = $directory->getFileBy('file2');
        self::assertSame($file1, $file1Control);
        self::assertSame($file2, $file2Control);
    }

    public function testGetFileByNotFound()
    {
        $this->expectException(NotFoundException::class);
        $root = $this->getMockForAbstractClass(AbstractDirectory::class, ['root']);
        $file = $this->getMockForAbstractClass(AbstractFile::class, ['path/to/file']);

        $root->addFile($file);
        $root->getFileBy('give me a non-existent file');
    }

    public function testRemoveFile()
    {
        $directory = $this->getMockForAbstractClass(AbstractDirectory::class, ['root']);
        $file1 = $this->getMockForAbstractClass(AbstractFile::class, ['path/to/File.file']);
        $file2 = $this->getMockForAbstractClass(AbstractFile::class, ['the/path/to/File2.file']);
        $directory->addFile($file1);
        $directory->addFile($file2);
        $directory->removeFile($file2);
        $files = $directory->getFiles();
        self::assertCount(1, $files);
        self::assertSame($file1, $files[$file1->getName()]);

        self::assertEmpty($file2->getParent());
    }

    public function testRemoveDirectoryExcept()
    {
        $this->expectException(TreeException::class);

        $directory = $this->getMockForAbstractClass(AbstractDirectory::class, ['root']);
        $file = $this->getMockForAbstractClass(AbstractFile::class, ['path/to/File.file']);

        $directory->removeFile($file);
    }

    public function testContainsFile()
    {
        $directory = $this->getMockForAbstractClass(AbstractDirectory::class, ['root']);
        $file = $this->getMockForAbstractClass(AbstractFile::class, ['path/to/File.file']);

        $directory->addFile($file);

        self::assertTrue($directory->containsFile($file->getName()));
        self::assertFalse($directory->containsFile('non-existent directory'));
    }
}
