<?php
declare(strict_types=1);

namespace Vinograd\SimpleFiles;

use Vinograd\IO\Exception\IOException;
use Vinograd\IO\Exception\NotFoundException;
use Compass\Path;
use Vinograd\SimpleFiles\Functionality\DirectoryFunctionality;

class Directory extends AbstractDirectory
{

    /** @var bool */
    protected $synchronized = false;

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
            throw new \LogicException('The directory name cannot be an empty string.');
        }
        if (false !== strpos($name, '/') || false !== strpos($name, '\\')) {
            throw new \LogicException('The directory name cannot be a file path.');
        }
    }

    /**
     * @inheritDoc
     */
    protected function initFunctionalities(): void
    {
        $this->addFunctionality(DirectoryFunctionality::create($this));
    }

    /**
     * @param string $directoryPath
     * @return Directory
     * @throws NotFoundException
     */
    public static function createBinded(string $directoryPath): Directory
    {
        static $prototype;
        if ($prototype === null) {
            $class = \get_called_class();
            $prototype = unserialize(sprintf('O:%d:"%s":0:{}', \strlen($class), $class));
        }
        /** @var Directory $directory */
        $directory = clone $prototype;
        $directory->setPath(new Path($directoryPath));
        $directory->initFunctionalities();
        $directory->initBindWithFilesystem();
        return $directory;
    }

    /**
     * @param string $path
     * @throws IOException
     */
    public function bindWithFilesystem(string $path): void
    {
        if (!$this->isBinded()) {
            $separator = $this->pathObject->getSeparator();
            $localPath = $this->getLocalPath();
            $resultPth = $path . $separator . $localPath;
            FileFunctionalitiesContext::getFunctionalitySupport($this)
                ->fireCallMethodEvent($this, 'sync', [
                    $resultPth
                ]);
            $this->pathObject->setSource($resultPth);
            $this->setBindedFlag(true);
        }

        /** @var Directory $directory */
        foreach ($this->directories as $directory) {
            $directory->bindWithFilesystem($path);
        }
        /** @var File $file */
        foreach ($this->files as $file) {
            $file->bindWithFilesystem($path);
        }
    }

    /**
     * @param string $path
     */
    public function copy(string $path)
    {
        $separator = $this->pathObject->getSeparator();
        $localPath = $this->getLocalPath();

        if (!$this->isBinded()) {
            FileFunctionalitiesContext::getFunctionalitySupport($this)
                ->fireCallMethodEvent($this, 'sync', [
                    $path . $separator . $localPath,
                ]);
        } else {
            FileFunctionalitiesContext::getFunctionalitySupport($this)
                ->fireCallMethodEvent($this, 'copy', [
                    $path . $separator . $localPath,
                ]);
        }

        /** @var Directory $directory */
        foreach ($this->directories as $directory) {
            $directory->copy($path);
        }
        /** @var File $file */
        foreach ($this->files as $file) {
            $file->copy($path);
        }
    }

    /**
     * @param string $directoryPath
     */
    public function writeTo(string $directoryPath): void
    {
        FileFunctionalitiesContext::getFunctionalitySupport($this)
            ->fireCallMethodEvent($this, 'sync', [
                $directoryPath . $this->getLocalPath($this->pathObject->getSeparator()),
            ]);

        /** @var Directory $directory */
        foreach ($this->directories as $directory) {
            $directory->writeTo($directoryPath);
        }
        /** @var File $file */
        foreach ($this->files as $file) {
            $file->writeTo($directoryPath);
        }
    }

    /**
     * @return string
     */
    public function getSourcePath(): string
    {
        return $this->pathObject->getSource();
    }

    /**
     *
     */
    public function delete(): void
    {
        $deletePaths = $this->getPathsAllDirectories();
        $this->deleteAllFiles();
        FileFunctionalitiesContext::getFunctionalitySupport($this)
            ->fireCallMethodEvent($this, 'delete', [$deletePaths]);
        $this->removeAllDirectories();
    }

    protected function removeAllDirectories(): void
    {
        if (!empty($this->parent)) {
            $this->removeFromParent();
        }
        /** @var Directory $directory */
        foreach ($this->directories as $directory) {
            $directory->removeAllDirectories();
        }
        $this->revokeAllSupports();
    }

    /**
     * @param string $path
     */
    public function move(string $path)
    {
        $deletePaths = $this->getPathsAllDirectories();
        $this->moveAll($path);
        FileFunctionalitiesContext::getFunctionalitySupport($this)
            ->fireCallMethodEvent($this, 'delete', [$deletePaths]);

        $this->bindWithFilesystem($path);
    }

    /**
     * @param string $path
     */
    protected function moveAll(string $path): void
    {
        $resultPath = $path . $this->getLocalPath($this->pathObject->getSeparator());

        if (!$this->isBinded()) {
            FileFunctionalitiesContext::getFunctionalitySupport($this)
                ->fireCallMethodEvent($this, 'sync', [
                    $resultPath,
                ]);
            $this->pathObject->setSource($resultPath);
            $this->setBindedFlag(true);
        } else {
            FileFunctionalitiesContext::getFunctionalitySupport($this)
                ->fireCallMethodEvent($this, 'copy', [
                    $resultPath,
                ]);
            $this->setBindedFlag(false);
        }

        /** @var Directory $directory */
        foreach ($this->directories as $directory) {
            $directory->moveAll($path);
        }
        /** @var File $file */
        foreach ($this->files as $file) {
            $file->move($path);
        }
    }

    protected function deleteAllFiles(): void
    {
        /** @var File $file */
        foreach ($this->files as $file) {
            if ($file->isBinded()) {
                $this->removeFile($file);
                $file->delete();
            }
        }
        /** @var Directory $directory */
        foreach ($this->directories as $directory) {
            $directory->deleteAllFiles();
        }
    }

    /**
     * @return string[]
     */
    public function getPathsAllDirectories(): array
    {
        $result = $this->isBinded() ? [$this->pathObject->getSource()] : [];
        return array_merge($result, $this->getPathsOfChildDirectoriesRecursive());
    }

    /**
     * @return string[]
     */
    protected function getPathsOfChildDirectoriesRecursive(): array
    {
        $result = [];
        /** @var Directory $directory */
        foreach ($this->directories as $directory) {
            if ($directory->isBinded()) {
                $result[] = $directory->getSourcePath();
            }
            $result = array_merge($result, $directory->getPathsOfChildDirectoriesRecursive());
        }
        return $result;
    }

    /**
     * @return string[]
     */
    public function getPathsAllFiles(): array
    {
        $result = $this->getFilesPaths();
        /** @var Directory $directory */
        foreach ($this->directories as $directory) {
            $result = array_merge($result, $directory->getPathsAllFiles());
        }
        return $result;
    }

    /**
     * @return string[]
     */
    protected function getFilesPaths(): array
    {
        $result = [];
        /** @var File $file */
        foreach ($this->files as $file) {
            if ($file->isBinded()) {
                $result[] = $file->getSourcePath();
            }
        }
        return $result;
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
     *
     */
    public function revokeAllSupports(): void
    {
        parent::revokeAllSupports();
        $this->directories = [];
        $this->files = [];
        $this->parent = null;
        $this->pathObject = null;
        $this->synchronized = false;
    }
}