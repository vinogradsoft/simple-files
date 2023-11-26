<?php
declare(strict_types=1);

namespace Test\Cases\Dummy;

use Vinograd\IO\Filesystem;
use Vinograd\SimpleFiles\AbstractFile;
use Vinograd\SimpleFiles\AbstractFilesystemFunctionality;
use Vinograd\SimpleFiles\AbstractFilesystemObject;
use Vinograd\Support\Functionality;
use Vinograd\Support\SupportedFunctionalities;

class DummyFunctionality extends AbstractFilesystemFunctionality
{

    protected function checkArguments($method, $arguments): bool
    {
        return empty($arguments);
    }

    protected function installMethods(SupportedFunctionalities $component): void
    {
        $this->assignMethod($component, 'get');
    }

    protected function uninstallMethods(SupportedFunctionalities $component): void
    {
        $this->revokeMethod($component, 'get');
    }

    public static function create(SupportedFunctionalities $component): Functionality
    {
        return new self();
    }

    public function get(AbstractFilesystemObject $filesystemObject, Filesystem $filesystem)
    {
        return $filesystemObject;
    }
}