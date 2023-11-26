<?php
declare(strict_types=1);

namespace Vinograd\SimpleFiles;

use Vinograd\Support\Functionality;
use Vinograd\Support\SupportedFunctionalities;

abstract class AbstractFilesystemObject implements SupportedFunctionalities
{

    /**
     * @param Functionality $functionality
     */
    protected function addFunctionality(Functionality $functionality): void
    {
        $functionality->install($this);
    }

    /**
     * @param Functionality $functionality
     */
    protected function removeFunctionality(Functionality $functionality): void
    {
        $functionality->uninstall($this);
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args): mixed
    {
        return FileFunctionalitiesContext::getFunctionalitySupport($this)->fireCallMethodEvent($this, $method, $args);
    }

    /**
     * @return void
     */
    public function revokeAllSupports(): void
    {
        FileFunctionalitiesContext::removeFunctionalitySupport($this);
    }

    /**
     * @param AbstractFilesystemObject $filesystemObject
     * @return bool
     */
    public function equals(AbstractFilesystemObject $filesystemObject): bool
    {
        return $this === $filesystemObject;
    }

    /**
     * @param mixed $data
     * @return void
     */
    abstract protected function setData(mixed $data): void;

    /**
     * @param mixed $data
     * @return $this
     */
    public function cloneWithData(mixed $data): AbstractFilesystemObject
    {
        $support = FileFunctionalitiesContext::getFunctionalitySupport($this);
        $copyStorage = $support->copyStorage();
        $clone = clone $this;
        $clone->setData($data);
        $cloneSupport = FileFunctionalitiesContext::getFunctionalitySupport($clone);
        $cloneSupport->setStorage($copyStorage);
        return $clone;
    }

}