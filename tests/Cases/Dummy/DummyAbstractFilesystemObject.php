<?php
declare(strict_types=1);

namespace Test\Cases\Dummy;

use Vinograd\SimpleFiles\AbstractFilesystemObject;
use Vinograd\Support\Functionality;

class DummyAbstractFilesystemObject extends AbstractFilesystemObject
{

    public function installFunctionality(Functionality $functionality)
    {
        $this->addFunctionality($functionality);
    }

    public function uninstallFunctionality(Functionality $functionality)
    {
        $this->removeFunctionality($functionality);
    }

    protected function setData($data): void
    {

    }
}