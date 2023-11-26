<?php
declare(strict_types=1);

namespace Test\Unit\File;

use Test\Cases\AdditionalAssertCase;
use Vinograd\IO\Exception\NotFoundException;
use Vinograd\SimpleFiles\File;

class FileReadTest extends AdditionalAssertCase
{
    protected $outDirectoryName = 'FileReadTest';

    public function testRead()
    {
        $this->createFile($filePath = $this->outPath . '/test.file', 'content');
        $file = File::createBinded($filePath);
        $file->read();
        self::assertEquals('content', $file->getContent());
    }

    public function testReadWithSetLocalName()
    {
        $this->createFile($filePath = $this->outPath . '/test.file', 'content');
        $file = File::createBinded($filePath);
        $file->setLocalName('fileRenamed.txt');
        $file->read();
        self::assertEquals('content', $file->getContent());
    }

    public function testReadNotFoundException()
    {
        $this->expectException(NotFoundException::class);
        $this->createFile($filePath = $this->outPath . '/test.file', 'content');
        $file = File::createBinded($filePath);
        $this->delete($filePath);//что-то пошло не так
        $file->read();
    }

    public function testReadNotBinded()
    {
        $this->expectException(\LogicException::class);
        $file = new File('file.test');
        $file->read();
    }

}