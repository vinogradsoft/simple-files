<?php
declare(strict_types=1);

namespace Test\Unit\File;

use Test\Cases\AdditionalAssertCase;
use Vinograd\IO\Exception\NotFoundException;
use Vinograd\SimpleFiles\File;
use Vinograd\SimpleFiles\FileFunctionalitiesContext;

class FileInitialTest extends AdditionalAssertCase
{
    protected $outDirectoryName = 'FileInitialTest';

    public function testConstruct()
    {
        $file = new File($name = 'name');
        self::assertFalse($file->isBinded());
        self::assertEquals($name, $file->getName());
        self::assertEquals($name, $file->getPath()->getSource());

        $functionality = FileFunctionalitiesContext::getFunctionalitySupport($file);

        self::assertTrue($functionality->has('sync'));
        self::assertTrue($functionality->has('copy'));
        self::assertTrue($functionality->has('remove'));
        self::assertTrue($functionality->has('read'));
        self::assertTrue($functionality->has('write'));
        self::assertTrue($functionality->has('move'));
        self::assertTrue($functionality->has('assertInitBind'));
        self::assertFileDoesNotExist($file->getPath()->getSource());
    }

    public function testConstructEmpty()
    {
        $this->expectException(\LogicException::class);
        new File('');
    }

    public function testConstructPathException()
    {
        $this->expectException(\LogicException::class);
        new File($this->outPath . '/file.test');
    }

    public function testCreateBinded()
    {
        $this->createFile($filePath = $this->outPath . '/file.test', 'test content');
        $file = File::createBinded($filePath);

        self::assertTrue($file->isBinded());
        self::assertEquals('file.test', $file->getName());
        self::assertEquals($filePath, $file->getPath()->getSource());
        $functionality = FileFunctionalitiesContext::getFunctionalitySupport($file);

        self::assertTrue($functionality->has('sync'));
        self::assertTrue($functionality->has('copy'));
        self::assertTrue($functionality->has('remove'));
        self::assertTrue($functionality->has('read'));
        self::assertTrue($functionality->has('write'));
        self::assertTrue($functionality->has('move'));
        self::assertTrue($functionality->has('assertInitBind'));
        self::assertFileExists($file->getPath()->getSource());
    }

    /**
     * @dataProvider getCasesCreateBinded
     */
    public function testCreateBindedFileNotExist($badPath)
    {
        $this->expectException(NotFoundException::class);
        File::createBinded($badPath);
    }

    public function getCasesCreateBinded()
    {
        return [
            1 => ['bad'],
            2 => ['http://bad/path'],
            3 => ['~/notexists/path'],
            4 => ['/notexists/path'],
        ];
    }
}
