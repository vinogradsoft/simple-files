<?php

namespace Test\Unit\AbstractDirectory;

use Test\Cases\CommonCase;
use Vinograd\IO\Exception\AlreadyExistException;
use Vinograd\IO\Exception\NotFoundException;
use Vinograd\SimpleFiles\AbstractDirectory;
use Vinograd\SimpleFiles\AbstractFile;
use Vinograd\SimpleFiles\Exception\TreeException;

class AbstractDirectoryUpdateNamesTest extends CommonCase
{

    public function testUpdateFileNameNotFound()
    {
        $this->expectException(TreeException::class);
        $directory = $this->getMockForAbstractClass(AbstractDirectory::class, ['root1']);
        $file1 = $this->getMockForAbstractClass(AbstractFile::class, ['file1.txt']);
        $file2 = $this->getMockForAbstractClass(AbstractFile::class, ['file2.txt']);
        $file3 = $this->getMockForAbstractClass(AbstractFile::class, ['file3.txt']);
        $file4 = $this->getMockForAbstractClass(AbstractFile::class, ['file4.txt']);

        $directory->addFile($file1);
        $directory->addFile($file2);
        $directory->addFile($file3);
        $directory->addFile($file4);
        $directory->updateFileName('newName');
    }

    public function testUpdateFileNameSameName()
    {
        $directory = $this->getMockForAbstractClass(AbstractDirectory::class, ['root1']);
        $file1 = $this->getMockForAbstractClass(AbstractFile::class, ['file1.txt']);
        $file2 = $this->getMockForAbstractClass(AbstractFile::class, ['file2.txt']);
        $file3 = $this->getMockForAbstractClass(AbstractFile::class, ['file3.txt']);
        $file4 = $this->getMockForAbstractClass(AbstractFile::class, ['file4.txt']);

        $directory->addFile($file1);
        $directory->addFile($file2);
        $directory->addFile($file3);
        $directory->addFile($file4);
        $directory->updateFileName('file4.txt');
        $files = $directory->getFiles();
        self::assertArrayHasKey('file4.txt', $files);
    }

    public function testUpdateDirectoryNameNotFound()
    {
        $this->expectException(TreeException::class);
        $directory = $this->getMockForAbstractClass(AbstractDirectory::class, ['root1']);
        $directory1 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory1']);
        $directory2 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory2']);
        $directory3 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory3']);
        $directory4 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory4']);

        $directory->addDirectory($directory1);
        $directory->addDirectory($directory2);
        $directory->addDirectory($directory3);
        $directory->addDirectory($directory4);
        $directory->updateDirectoryName('newName');
    }

    public function testUpdateDirectoryNameSameName()
    {
        $directory = $this->getMockForAbstractClass(AbstractDirectory::class, ['root1']);
        $directory1 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory1']);
        $directory2 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory2']);
        $directory3 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory3']);
        $directory4 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory4']);

        $directory->addDirectory($directory1)->addDirectory($directory2);
        $directory2->addDirectory($directory3);
        $directory2->addDirectory($directory4);

        $directory2->updateDirectoryName('directory4');
        $directories = $directory2->getDirectories();
        self::assertArrayHasKey('directory4', $directories);
        self::assertArrayHasKey('directory3', $directories);
    }

}
