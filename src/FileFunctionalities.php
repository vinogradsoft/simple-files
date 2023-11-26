<?php
declare(strict_types=1);

namespace Vinograd\SimpleFiles;

use BadMethodCallException;
use Vinograd\Support\Event\CallMethodEvent;
use Vinograd\Support\FunctionalitySupport;
use Vinograd\IO\Filesystem;

class FileFunctionalities extends FunctionalitySupport
{

    protected Filesystem $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * @param Filesystem $filesystem
     * @return void
     */
    public function setFilesystem(Filesystem $filesystem): void
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param $source
     * @param string $methodName
     * @param $arguments
     * @return mixed
     * @throws BadMethodCallException
     */
    public function fireCallMethodEvent($source, string $methodName, $arguments): mixed
    {
        if (!isset($this->storage[$methodName])) {
            throw new BadMethodCallException('Calling unknown method ' . get_class($source) . '::' . $methodName . '(...))');
        }

        $evt = new CallMethodEvent($source, $methodName, $arguments);
        $support = $this->storage[$methodName];
        return $support->methodCalled($evt, $this->filesystem);
    }

}