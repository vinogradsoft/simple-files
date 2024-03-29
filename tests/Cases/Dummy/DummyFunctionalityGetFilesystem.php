<?php
declare(strict_types=1);

namespace Test\Cases\Dummy;

use Vinograd\IO\Filesystem;
use Vinograd\SimpleFiles\AbstractFilesystemObject;

class DummyFunctionalityGetFilesystem extends DummyFunctionality
{
    public function get(AbstractFilesystemObject $filesystemObject, Filesystem $filesystem)
    {
        return $filesystem;
    }
}