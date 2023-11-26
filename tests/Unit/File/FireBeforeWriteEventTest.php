<?php
declare(strict_types=1);

namespace Test\Unit\File;

use Test\Cases\AdditionalAssertCase;
use Vinograd\SimpleFiles\Event\FileBeforeWriteListener;
use Vinograd\SimpleFiles\File;

class FireBeforeWriteEventTest extends AdditionalAssertCase
{
    protected $outDirectoryName = 'FireBeforeWriteEventTest';

    public function testWrite()
    {
        $this->createFile($filePath = $this->outPath . '/test.file', 'content');
        $file = File::createBinded($filePath);
        $file->read();
        $file->addFileBeforeWriteListener(new class() implements FileBeforeWriteListener {
            public function beforeWrite(File $file, string $keyOperation): void
            {
                if ($keyOperation === File::WRITE) {
                    $file->setContent('modified ' . $file->getContent());
                }
            }
        });
        $file->write();
        self::assertEquals('modified content', file_get_contents($filePath));
    }

    public function testWriteTo()
    {
        $this->createFile($filePath = $this->outPath . '/test.file', 'content');
        $file = File::createBinded($filePath);
        $file->read();
        $file->addFileBeforeWriteListener(new class() implements FileBeforeWriteListener {
            public function beforeWrite(File $file, string $keyOperation): void
            {
                if ($keyOperation === File::WRITE_TO) {
                    $file->setContent('modified ' . $file->getContent());
                }
            }
        });
        $file->setLocalName('file.txt');
        $file->writeTo($this->outPath);
        self::assertEquals('modified content', file_get_contents($this->outPath . '/file.txt'));
    }

    public function testWriteToNotBinded()
    {
        $file =new File('file.txt');
        $file->setContent('content');
        $file->addFileBeforeWriteListener(new class() implements FileBeforeWriteListener {
            public function beforeWrite(File $file, string $keyOperation): void
            {
                if ($keyOperation === File::WRITE_TO) {
                    $file->setContent('modified ' . $file->getContent());
                }
            }
        });

        $file->writeTo($this->outPath);
        self::assertEquals('modified content', file_get_contents($this->outPath . '/file.txt'));
    }

    public function testCopy()
    {
        $this->createDirectory($copyDir = $this->outPath . '/copy');
        $this->createFile($filePath = $this->outPath . '/test.file', 'content');
        $file = File::createBinded($filePath);

        $file->addFileBeforeWriteListener(new class() implements FileBeforeWriteListener {
            public function beforeWrite(File $file, string $keyOperation): void
            {
                if ($keyOperation === File::COPY) {
                    $file->setContent('modified ' . $file->getContent());
                }
            }
        });
        $file->copy($copyDir);
        self::assertEquals('modified content', file_get_contents($copyDir . '/test.file'));
    }

    public function testCopyNotBinded()
    {
        $this->createDirectory($copyDir = $this->outPath . '/copy');
        $file = new File('test.file');
        $file->setContent('content');
        $file->addFileBeforeWriteListener(new class() implements FileBeforeWriteListener {
            public function beforeWrite(File $file, string $keyOperation): void
            {
                if ($keyOperation === File::COPY) {
                    $file->setContent('modified ' . $file->getContent());
                }
            }
        });
        $file->copy($copyDir);
        self::assertEquals('modified content', file_get_contents($copyDir . '/test.file'));
    }

    public function testMove()
    {
        $this->createDirectory($moveDir = $this->outPath . '/move');
        $this->createFile($filePath = $this->outPath . '/test.file', 'content');

        $file = File::createBinded($filePath);

        $file->addFileBeforeWriteListener(new class() implements FileBeforeWriteListener {
            public function beforeWrite(File $file, string $keyOperation): void
            {
                if ($keyOperation === File::MOVE) {
                    $file->setContent('modified ' . $file->getContent());
                }
            }
        });
        $file->move($moveDir);
        self::assertEquals('modified content', file_get_contents($moveDir . '/test.file'));
    }

    public function testMoveNotBinded()
    {
        $this->createDirectory($moveDir = $this->outPath . '/move');
        $file = new File('test.file');
        $file->setContent('content');
        $file->addFileBeforeWriteListener(new class() implements FileBeforeWriteListener {
            public function beforeWrite(File $file, string $keyOperation): void
            {
                if ($keyOperation === File::MOVE) {
                    $file->setContent('modified ' . $file->getContent());
                }
            }
        });
        $file->move($moveDir);
        self::assertEquals('modified content', file_get_contents($moveDir . '/test.file'));
    }

}
