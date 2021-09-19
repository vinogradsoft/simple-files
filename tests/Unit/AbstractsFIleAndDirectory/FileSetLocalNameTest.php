<?php

namespace Test\Unit\AbstractsFIleAndDirectory;

use Test\Cases\CommonCase;
use Vinograd\IO\Exception\AlreadyExistException;
use Vinograd\IO\Exception\NotFoundException;
use Vinograd\SimpleFiles\AbstractDirectory;
use Vinograd\SimpleFiles\AbstractFile;
use Vinograd\SimpleFiles\Exception\TreeException;

class FileSetLocalNameTest extends CommonCase
{
    /**
     * @dataProvider getCasesLocalNames
     */
    public function testSetLocalName($newName)
    {
        $directory0 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory0']);
        $directory1 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory1']);
        $directory2 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory2']);
        $directory3 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory3']);
        $file = $this->getMockForAbstractClass(AbstractFile::class, ['file.txt']);

        $controlPathBase = 'directory0/directory1/directory2/directory3';
        $controlPath = $controlPathBase . '/file.txt';
        $directory0
            ->addDirectory($directory1)
            ->addDirectory($directory2)
            ->addDirectory($directory3)
            ->addFile($file);
        self::assertEquals($controlPath, $file->getLocalPath());

        $file->setLocalName($newName);

        $files = $directory3->getFiles();
        $fileControl = $directory3->getFileBy($newName);

        self::assertEquals($controlPathBase . '/' . $newName, $file->getLocalPath());
        self::assertEquals($newName, $file->getLocalName());
        self::assertEquals('file.txt', $file->getName());
        self::assertArrayHasKey($newName, $files);
        self::assertSame($fileControl, $file);
    }

    /**
     * @dataProvider getCasesLocalNames
     */
    public function testSetLocalNameNotAdded($newName)
    {
        $file = $this->getMockForAbstractClass(AbstractFile::class, ['file.txt']);
        $file->setLocalName($newName);

        self::assertEquals($newName, $file->getLocalPath());
        self::assertEquals($newName, $file->getLocalName());
        self::assertEquals('file.txt', $file->getName());
    }

    public function getCasesLocalNames(): array
    {
        return [
            ['ImMegaPhp.file'],
            ['file.txt'],
        ];
    }

    public function testSetLocalNameNullNotAdded()
    {
        $file = $this->getMockForAbstractClass(AbstractFile::class, ['file.txt']);

        $file->setLocalName('test');
        $file->setLocalName(null);

        self::assertEquals($file->getName(), $file->getLocalPath());
        self::assertEquals($file->getName(), $file->getLocalName());
        self::assertEquals('file.txt', $file->getName());
    }

    public function testSetLocalNameNull()
    {
        $directory0 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory0']);
        $directory1 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory1']);
        $directory2 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory2']);
        $directory3 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory3']);
        $file = $this->getMockForAbstractClass(AbstractFile::class, ['file.txt']);

        $controlPathBase = 'directory0/directory1/directory2/directory3';
        $controlPath = $controlPathBase . '/file.txt';
        $directory0
            ->addDirectory($directory1)
            ->addDirectory($directory2)
            ->addDirectory($directory3)
            ->addFile($file);
        self::assertEquals($controlPath, $file->getLocalPath());

        $file->setLocalName('test');
        $file->setLocalName(null);

        $files = $directory3->getFiles();
        $fileControl = $directory3->getFileBy($file->getName());

        self::assertEquals($controlPathBase . '/' . $file->getName(), $file->getLocalPath());
        self::assertEquals($file->getName(), $file->getLocalName());
        self::assertEquals('file.txt', $file->getName());
        self::assertArrayHasKey($file->getName(), $files);
        self::assertSame($fileControl, $file);
    }

    public function testSetLocalNameAlreadyExist()
    {
        $this->expectException(TreeException::class);
        $directory0 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory0']);
        $directory1 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory1']);
        $directory2 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory2']);
        $directory3 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory3']);
        $file1 = $this->getMockForAbstractClass(AbstractFile::class, ['file1.txt']);
        $file2 = $this->getMockForAbstractClass(AbstractFile::class, ['file2.txt']);

        $directory0
            ->addDirectory($directory1)
            ->addDirectory($directory2)
            ->addDirectory($directory3)
            ->addFile($file1);

        $directory3->addFile($file2);
        $file2->setLocalName('file1.txt');
    }

}
