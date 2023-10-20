<?php
declare(strict_types=1);

namespace Vinograd\SimpleFiles;

use BadMethodCallException;
use Vinograd\Support\Event\CallMethodEvent;
use Vinograd\Support\FunctionalitySupport;
use Vinograd\IO\Filesystem;

class FileFunctionalities extends FunctionalitySupport
{
    /** @var \Vinograd\IO\Filesystem */
    protected $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @return \Vinograd\IO\Filesystem
     */
    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * @param \Vinograd\IO\Filesystem $filesystem
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
     */
    public function fireCallMethodEvent($source, string $methodName, $arguments)
    {
        if (!isset($this->storage[$methodName])) {
            throw new BadMethodCallException('Calling unknown method ' . get_class($source) . '::' . $methodName . '(...))');
        }

        $evt = new CallMethodEvent($source, $methodName, $arguments);
        $support = $this->storage[$methodName];
        return $support->methodCalled($evt, $this->filesystem);
    }
}