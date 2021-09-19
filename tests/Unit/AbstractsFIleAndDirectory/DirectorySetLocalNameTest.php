<?php

namespace Test\Unit\AbstractsFIleAndDirectory;

use Test\Cases\CommonCase;
use Vinograd\IO\Exception\AlreadyExistException;
use Vinograd\IO\Exception\NotFoundException;
use Vinograd\SimpleFiles\AbstractDirectory;
use Vinograd\SimpleFiles\AbstractFile;
use Vinograd\SimpleFiles\Exception\TreeException;

class DirectorySetLocalNameTest extends CommonCase
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

        $controlPathBase = 'directory0/directory1';
        $controlPath = $controlPathBase . '/directory3';
        $directory0
            ->addDirectory($directory1)
            ->addDirectory($directory2);
        $directory1->addDirectory($directory3);

        self::assertEquals($controlPath, $directory3->getLocalPath());

        $directory3->setLocalName($newName);

        $directories = $directory1->getDirectories();
        $directoryControl = $directory1->getDirectoryBy($newName);

        self::assertEquals($controlPathBase . '/' . $newName, $directory3->getLocalPath());
        self::assertEquals($newName, $directory3->getLocalName());
        self::assertEquals('directory3', $directory3->getName());
        self::assertArrayHasKey($newName, $directories);
        self::assertSame($directoryControl, $directory3);

        self::assertEquals('directory2', $directory2->getLocalName());
    }
 /**
     * @dataProvider getCasesLocalNames
     */
    public function testSetLocalNameNotAdded($newName)
    {
        $directory = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory3']);
        $directory->setLocalName($newName);

        self::assertEquals($newName, $directory->getLocalPath());
        self::assertEquals($newName, $directory->getLocalName());
        self::assertEquals('directory3', $directory->getName());
    }

    public function getCasesLocalNames(): array
    {
        return [
            ['newSuperDirectory'],
            ['directory3'],
        ];
    }

    public function testSetLocalNameNullNotAdded()
    {
        $directory3 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory3']);

        $directory3->setLocalName('test');
        $directory3->setLocalName(null);

        self::assertEquals($directory3->getName(), $directory3->getLocalPath());
        self::assertEquals($directory3->getName(), $directory3->getLocalName());
        self::assertEquals('directory3', $directory3->getName());
    }

    public function testSetLocalNameNull()
    {
        $directory0 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory0']);
        $directory1 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory1']);
        $directory2 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory2']);
        $directory3 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory3']);

        $controlPathBase = 'directory0/directory1';
        $controlPath = $controlPathBase . '/directory3';
        $directory0
            ->addDirectory($directory1)
            ->addDirectory($directory2);
        $directory1->addDirectory($directory3);

        self::assertEquals($controlPath, $directory3->getLocalPath());

        $directory3->setLocalName('test');
        $directory3->setLocalName(null);

        $directories = $directory1->getDirectories();
        $directoryControl = $directory1->getDirectoryBy($directory3->getName());

        self::assertEquals($controlPathBase . '/' . $directory3->getName(), $directory3->getLocalPath());
        self::assertEquals($directory3->getName(), $directory3->getLocalName());
        self::assertEquals('directory3', $directory3->getName());
        self::assertArrayHasKey($directory3->getName(), $directories);
        self::assertSame($directoryControl, $directory3);
    }

    public function testSetLocalNameAlreadyExist()
    {
        $this->expectException(TreeException::class);
        $directory0 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory0']);
        $directory1 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory1']);
        $directory2 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory2']);
        $directory3 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory3']);

        $directory0
            ->addDirectory($directory1)
            ->addDirectory($directory2);
        $directory1->addDirectory($directory3);

        $directory3->setLocalName('directory2');
    }

}
