<?php

namespace Test\Unit\File;

use Test\Cases\AdditionalAssertCase;
use Vinograd\IO\Exception\NotFoundException;
use Vinograd\SimpleFiles\File;

class FileDeleteTest extends AdditionalAssertCase
{
    protected $outDirectoryName = 'FileDeleteTest';

    public function testDelete()
    {
        $this->createFile($filePath = $this->outPath . '/test.file', 'content');
        $file = File::createBinded($filePath);
        $file->delete();
        self::assertFileNotExists($filePath);
    }

    public function testDeleteWithSetLocalName()
    {
        $this->createFile($filePath = $this->outPath . '/test.file', 'content');
        $file = File::createBinded($filePath);
        $file->setLocalName('fileRenamed.txt');
        $file->delete();
        self::assertFileNotExists($filePath);
    }

    public function testDeleteNotFoundException()
    {
        $this->expectException(NotFoundException::class);
        $this->createFile($filePath = $this->outPath . '/test.file', 'content');
        $file = File::createBinded($filePath);
        $this->delete($filePath);//что-то пошло не так
        $file->delete();
    }

    public function testDeleteNotBinded()
    {
        $this->expectException(\LogicException::class);
        $file = new File('file.test');
        $file->delete();
    }

}