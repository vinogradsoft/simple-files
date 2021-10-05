<?php

namespace Test\Cases\Dummy;

use PHPUnit\Framework\TestCase;
use Vinograd\IO\Exception\IOException;
use Vinograd\IO\Filesystem;

class DummyFilesystem implements Filesystem
{
    /** @var TestCase */
    private $testCase;

    /**
     * @param TestCase $testCase
     */
    public function __construct(TestCase $testCase)
    {
        $this->testCase = $testCase;
    }

    public function getAbsolutePath(string $path): string
    {
        return $path;
    }

    public function exists(string $filename): bool
    {
        return $filename === 'affected';
    }

    public function createDirectory(string $directory, int $permissions = 0777): void
    {
        $this->testCase->assertTrue($directory === 'affected');
    }

    public function removeDirectory(string $directory): void
    {
        $this->testCase->assertTrue($directory === 'affected');
    }

    public function isDirectory(string $filename): bool
    {
        return $filename === 'affected';
    }

    public function isFile(string $filename): bool
    {
        return $filename === 'affected';
    }

    public function fileGetContents(string $filename): string
    {
        return $filename;
    }

    public function filePutContents(string $filename, $data, int $flags = 0): void
    {
        $this->testCase->assertTrue($filename === 'affected');
    }

    public function removeFile(string $filename): void
    {
        $this->testCase->assertTrue($filename === 'affected');
    }

    public function yamlParseFile($filename, $pos = 0, &$ndocs = null, array $callbacks = [])
    {
        return $filename;
    }

    public function scanDirectory(string $path): array
    {
        return [$path];
    }
}