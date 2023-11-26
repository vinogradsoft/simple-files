<?php
declare(strict_types=1);

namespace Test\Cases\Dummy;

use Vinograd\IO\Exception\IOException;
use Vinograd\SimpleFiles\DefaultFilesystem;

class ErrorEmulatorFilesystem extends DefaultFilesystem
{
    public function filePutContents(string $filename, $data, int $flags = 0): void
    {
        throw new IOException(sprintf('Unable to write to file "%s".', $filename));
    }

    public function createDirectory(string $directory, int $permissions = 0777): void
    {
        throw new IOException(sprintf('Directory "%s" was not created.', $directory));
    }
}