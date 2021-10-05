<?php

namespace Test\Cases\Helper;

use Vinograd\IO\Exception\IOException;
use Vinograd\IO\Filesystem;
use Vinograd\Path\Path;
use Vinograd\SimpleFiles\AbstractDirectory;
use Vinograd\SimpleFiles\AbstractFile;
use Vinograd\SimpleFiles\GetExtensionTrait;

class HelperForAbstractClasses
{

    public static function mockFilesystem()
    {
        return new class() implements Filesystem {

            public function testSomeMethod(string $arg)
            {
                return $arg;
            }

            public function exists(string $filename): bool
            {
                throw new \Exception('assert');
            }

            public function createDirectory(string $directory, int $permissions = 0777): void
            {
                throw new \Exception('assert');
            }

            public function removeDirectory(string $directory): void
            {
                throw new \Exception('assert');
            }

            public function isDirectory(string $filename): bool
            {
                throw new \Exception('assert');
            }

            public function fileGetContents(string $filename): string
            {
                throw new \Exception('assert');
            }

            public function filePutContents(string $filename, $data, int $flags = 0): void
            {
                throw new \Exception('assert');
            }

            public function removeFile(string $filename): void
            {
                throw new \Exception('assert');
            }

            public function yamlParseFile($filename, $pos = 0, &$ndocs = null, array $callbacks = [])
            {
                throw new \Exception('assert');
            }

            public function getAbsolutePath(string $path): string
            {
                throw new \Exception('assert');
            }

            public function isFile(string $filename): bool
            {
                throw new \Exception('assert');
            }

            public function scanDirectory(string $path): array
            {
                throw new \Exception('assert');
            }
        };
    }

    public static function mockGetExtensionTrait(string $path)
    {
        return new class($path) {
            /**@var Path */
            protected $pathObject;

            public function __construct(string $path)
            {
                $this->pathObject = new Path($path);
            }

            public function getPath()
            {
                return $this->pathObject;
            }

            use GetExtensionTrait;
        };
    }

    public static function mockDirectory(string $path): AbstractDirectory
    {
        return new class($path) extends AbstractDirectory {
            protected function removeSelf(): void
            {
                $path = $this->pathObject->getSource();
                rmdir($path);
            }

            protected function writeSelf(): void
            {
                $path = $this->pathObject->getSource();
                mkdir($path);
            }
        };
    }

    public static function mockFile(string $path): AbstractFile
    {
        return new class($path) extends AbstractFile {
            public function write(): void
            {
                $path = $this->pathObject->getSource();
                file_put_contents($path, $path);
            }

            protected function removeSelf(): void
            {
                $path = $this->pathObject->getSource();
                unlink($path);
            }
        };
    }

}