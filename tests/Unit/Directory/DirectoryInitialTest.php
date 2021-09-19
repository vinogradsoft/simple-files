<?php

namespace Test\Unit\Directory;

use Test\Cases\FileSystemCase;
use Vinograd\IO\Exception\NotFoundException;
use Vinograd\SimpleFiles\Directory;
use Vinograd\SimpleFiles\FileFunctionalitiesContext;

class DirectoryInitialTest extends FileSystemCase
{
    private $outPath;

    public function setUp(): void
    {
        $this->createDirectory($this->outPath = $this->getRuntimePath() . '/DirectoryTest');
    }

    public function testConstruct()
    {
        $directory = new Directory($name = 'name');
        self::assertFalse($directory->isBinded());
        self::assertEquals($name, $directory->getName());
        self::assertEquals($name, $directory->getPath()->getSource());

        $functionality = FileFunctionalitiesContext::getFunctionalitySupport($directory);

        self::assertTrue($functionality->has('sync'));
        self::assertTrue($functionality->has('copy'));
        self::assertTrue($functionality->has('delete'));
        self::assertTrue($functionality->has('assertInitBind'));
        self::assertDirectoryNotExists($directory->getPath()->getSource());
    }

    public function testConstructEmpty()
    {
        $this->expectException(\LogicException::class);
        new Directory('');
    }

    public function testConstructPathException()
    {
        $this->expectException(\LogicException::class);
        new Directory($this->outPath);
    }

    public function testCreateBinded()
    {
        $directory = Directory::createBinded($this->outPath);

        self::assertTrue($directory->isBinded());
        self::assertEquals('DirectoryTest', $directory->getName());
        self::assertEquals($this->outPath, $directory->getPath()->getSource());
        $functionality = FileFunctionalitiesContext::getFunctionalitySupport($directory);

        self::assertTrue($functionality->has('sync'));
        self::assertTrue($functionality->has('copy'));
        self::assertTrue($functionality->has('delete'));
        self::assertTrue($functionality->has('assertInitBind'));
        self::assertDirectoryExists($directory->getPath()->getSource());
    }

    /**
     * @dataProvider getCasesCreateBinded
     */
    public function testCreateBindedDirectoryNotExist($badPath)
    {
        $this->expectException(NotFoundException::class);
        Directory::createBinded($badPath);
    }

    public function getCasesCreateBinded()
    {
        return [
            1 => ['bad'],
            2 => ['http://bad/path'],
            3 => ['^'],
            4 => ['@#LFKHGJK'],
            5 => ['~/notexists/path'],
            6 => ['/notexists/path'],
            7 => ['2'],
            12 => ['runtime'],
        ];
    }

    public function tearDown(): void
    {
        FileFunctionalitiesContext::reset();
        $this->delete($this->outPath);
    }
}
