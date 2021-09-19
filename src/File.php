<?php

namespace Vinograd\SimpleFiles;

use Vinograd\Path\Path;
use Vinograd\SimpleFiles\Event\FileBeforeWriteListener;
use Vinograd\SimpleFiles\Functionality\FileFunctionality;

class File extends AbstractFile
{
    const WRITE = 'WRITE';
    const COPY = 'COPY';
    const MOVE = 'MOVE';
    const WRITE_TO = 'WRITE_TO';

    /** @var mixed */
    protected $content;

    /** @var bool */
    protected $synchronized = false;

    /** @var FileBeforeWriteListener[] */
    protected $listeners = [];

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->assertName($name);
        parent::__construct($name);
    }

    /**
     * @param string $name
     * @throws \LogicException
     */
    protected function assertName(string $name): void
    {
        if (empty($name)) {
            throw new \LogicException('The file name cannot be an empty string.');
        }
        if (false !== strpos($name, '/') || false !== strpos($name, '\\')) {
            throw new \LogicException('The file name cannot be a file path.');
        }
    }

    /**
     * @inheritDoc
     */
    protected function initFunctionalities(): void
    {
        $this->addFunctionality(FileFunctionality::create($this));
    }

    /**
     * @param string $path
     * @return File
     */
    public static function createBinded(string $path): File
    {
        static $prototype;
        if ($prototype === null) {
            $class = \get_called_class();
            $prototype = unserialize(sprintf('O:%d:"%s":0:{}', \strlen($class), $class));
        }
        /** @var File $file */
        $file = clone $prototype;
        $file->setPath(new Path($path));
        $file->initFunctionalities();
        $file->initBindWithFilesystem();
        return $file;
    }

    /**
     * @param string $path
     */
    public function bindWithFilesystem(string $path): void
    {
        if (!$this->isBinded()) {
            $resultPath = $path . $this->getLocalPath($this->pathObject->getSeparator());
            FileFunctionalitiesContext::getFunctionalitySupport($this)
                ->fireCallMethodEvent($this, 'sync', [
                    $resultPath,
                ]);
            $this->pathObject->setSource($resultPath);
            $this->setBindedFlag(true);
        }
    }

    /**
     * @param string $path
     */
    public function copy(string $path): void
    {
        $separator = $this->pathObject->getSeparator();
        if (!$this->isBinded()) {
            $this->fireBeforeWriteEvent(self::COPY);
            FileFunctionalitiesContext::getFunctionalitySupport($this)
                ->fireCallMethodEvent($this, 'write', [
                    $path . $this->getLocalPath($separator)
                ]);
            return;
        }
        FileFunctionalitiesContext::getFunctionalitySupport($this)
            ->fireCallMethodEvent($this, 'copy', [
                $path . $this->getLocalPath($separator),
            ]);
    }

    /**
     * @param string $path
     */
    public function move(string $path)
    {
        $resultPath = $path . $this->getLocalPath($this->pathObject->getSeparator());
        if (!$this->isBinded()) {
            $this->fireBeforeWriteEvent(self::MOVE);
            FileFunctionalitiesContext::getFunctionalitySupport($this)
                ->fireCallMethodEvent($this, 'write', [
                    $resultPath,
                ]);
        } else {
            FileFunctionalitiesContext::getFunctionalitySupport($this)
                ->fireCallMethodEvent($this, 'move', [
                    $resultPath,
                ]);
            $this->removeSelf();
        }

        $this->pathObject->setSource($resultPath);
        $this->setBindedFlag(true);
    }

    protected function initBindWithFilesystem()
    {
        FileFunctionalitiesContext::getFunctionalitySupport($this)
            ->fireCallMethodEvent($this, 'assertInitBind', [
                $this->pathObject->getSource(),
            ]);
        $this->setBindedFlag(true);
    }

    /**
     * @return bool
     */
    public function isBinded(): bool
    {
        return $this->synchronized;
    }

    /**
     * @param bool $value
     */
    protected function setBindedFlag(bool $value): void
    {
        $this->synchronized = $value;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->pathObject->getSource();
    }

    /**
     * @return Path
     */
    public function getPath(): Path
    {
        return clone $this->pathObject;
    }

    /**
     * @param $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @inheritDoc
     */
    public function write(): void
    {
        if (!$this->isBinded()) {
            throw new \LogicException('The file cannot be written because it is not tied to a directory. There is nowhere to write it down.');
        }
        $this->fireBeforeWriteEvent(self::WRITE);
        FileFunctionalitiesContext::getFunctionalitySupport($this)
            ->fireCallMethodEvent($this, 'write', [$this->pathObject->getSource()]);
    }

    /**
     * @param string $directoryPath
     */
    public function writeTo(string $directoryPath): void
    {
        $this->fireBeforeWriteEvent(self::WRITE_TO);
        FileFunctionalitiesContext::getFunctionalitySupport($this)
            ->fireCallMethodEvent($this, 'write', [
                $directoryPath . $this->getLocalPath($this->pathObject->getSeparator())
            ]);
    }

    /**
     *
     */
    public function read(): void
    {
        if (!$this->isBinded()) {
            throw new \LogicException(sprintf('The file %s could not be read. The file is not tied to the file system.', $this->pathObject->getSource()));
        }
        FileFunctionalitiesContext::getFunctionalitySupport($this)
            ->fireCallMethodEvent($this, 'read', [$this->pathObject->getSource()]);
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        if (!$this->isBinded()) {
            throw new \LogicException('The file is not associated with the file system there is no path to delete.');
        }
        parent::delete();
        $this->revokeAllSupports();
    }

    /**
     * @inheritDoc
     */
    protected function removeSelf(): void
    {
        $support = FileFunctionalitiesContext::getFunctionalitySupport($this);
        $support->fireCallMethodEvent($this, 'remove', []);
        $this->setBindedFlag(false);
    }

    /**
     * @param string $keyOperation
     */
    public function fireBeforeWriteEvent(string $keyOperation)
    {
        foreach ($this->listeners as $listener) {
            $listener->beforeWrite($this, $keyOperation);
        }
    }

    /**
     * @param FileBeforeWriteListener $listener
     */
    public function addFileBeforeWriteListener(FileBeforeWriteListener $listener): void
    {
        if (in_array($listener, $this->listeners, true)) {
            throw new \LogicException('Listener is already exists.');
        }
        $this->listeners[] = $listener;
    }

    /**
     * @param FileBeforeWriteListener $listener
     */
    public function removeFileBeforeWriteListener(FileBeforeWriteListener $listener): void
    {
        if (!in_array($listener, $this->listeners, true)) {
            throw new \LogicException('This listener does not exist.');
        }
        $idx = array_search($listener, $this->listeners, true);
        unset($this->listeners[$idx]);
    }

    /**
     * @return array
     */
    public function getFileBeforeWriteListener(): array
    {
        return $this->listeners;
    }

    /**
     *
     */
    public function clearFileBeforeWriteListener(): void
    {
        $this->listeners = [];
    }

    /**
     * @inheritDoc
     */
    public function revokeAllSupports(): void
    {
        parent::revokeAllSupports();
        $this->clearFileBeforeWriteListener();
        $this->content = null;
        $this->synchronized = false;
        $this->parent = null;
        $this->pathObject = null;
    }
}