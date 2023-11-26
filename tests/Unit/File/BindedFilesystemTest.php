<?php
declare(strict_types=1);

namespace Test\Unit\File;

use Test\Cases\AdditionalAssertCase;
use Vinograd\IO\Exception\IOException;
use Vinograd\SimpleFiles\File;

class BindedFilesystemTest extends AdditionalAssertCase
{
    protected $outDirectoryName = 'BindedFilesystemTest';

    public function testBindWithFilesystem()
    {
        $outPath = $this->outPath;
        $file = new File('file1.txt');
        self::assertFalse($file->isBinded());

        $file->bindWithFilesystem($outPath);

        self::assertFileExists($outPath . '/file1.txt');
        self::assertTrue($file->isBinded());
        self::assertEmpty(file_get_contents($outPath . '/file1.txt'));
        self::assertEquals($outPath . '/file1.txt', $file->getPath()->getSource());
    }

    public function testBindWithFilesystemWithSetLocalName()
    {
        $outPath = $this->outPath;
        $file = new File('file1.txt');
        self::assertFalse($file->isBinded());
        $file->setLocalName('file1renamed.txt');
        $file->bindWithFilesystem($outPath);

        self::assertFileExists($outPath . '/file1renamed.txt');
        self::assertTrue($file->isBinded());
        self::assertEmpty(file_get_contents($outPath . '/file1renamed.txt'));
        self::assertEquals($outPath . '/file1renamed.txt', $file->getPath()->getSource());
    }

    public function testBindWithFilesystemBadPath()
    {
        $this->expectException(IOException::class);
        $outPath = $this->outPath;
        $file = new File('test.file');

        $file->bindWithFilesystem($outPath . '/bad/path');
    }

}
