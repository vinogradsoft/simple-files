<?php
declare(strict_types=1);

namespace Vinograd\SimpleFiles;

use Vinograd\IO\Exception\AlreadyExistException;
use Vinograd\IO\Exception\NotFoundException;
use Vinograd\SimpleFiles\Exception\TreeException;

abstract class AbstractDirectory extends NestedObject
{

    protected const ALREADY_EXISTS_MESSAGE = 'A directory or file named "%s" already exists.';

    /**@var AbstractDirectory[] */
    protected array $directories = [];

    /**@var AbstractFile[] */
    protected array $files = [];

    /**
     * @return AbstractDirectory[]
     */
    public function getDirectories(): array
    {
        return $this->directories;
    }

    /**
     * @param AbstractDirectory $directory
     * @return AbstractDirectory added directory
     * @throws AlreadyExistException
     */
    public function addDirectory(AbstractDirectory $directory): AbstractDirectory
    {
        $name = $directory->getLocalName();
        if (array_key_exists($name, $this->directories) || array_key_exists($name, $this->files)) {
            throw new AlreadyExistException(sprintf(static::ALREADY_EXISTS_MESSAGE, $name));
        }
        $parent = $directory->getParent();

        if ($this->containsParent($directory)) {
            $this->removeFromParent();
            if (!empty($parent)) {
                $parent->removeDirectory($directory);
                $parent->addDirectory($this);
            }
        } else {
            if (!empty($parent)) {
                $parent->removeDirectory($directory);
            }
        }

        $directory->setParent($this);

        $this->directories[$name] = $directory;
        return $directory;
    }

    /**
     * @param AbstractDirectory $directory
     * @return bool
     */
    public function containsParent(AbstractDirectory $directory): bool
    {
        return $this->isOneOfTheParents($this->parent, $directory);
    }

    /**
     * @param AbstractDirectory|null $parent
     * @param AbstractDirectory $directory
     * @return bool
     */
    protected function isOneOfTheParents(?AbstractDirectory $parent = null, AbstractDirectory $directory): bool
    {
        if ($parent === $directory) {
            return true;
        }
        if (empty($parent)) {
            return false;
        }

        return $parent->isOneOfTheParents($parent->getParent(), $directory);
    }

    /**
     * @param AbstractDirectory $directory
     * @return void
     * @throws TreeException
     */
    public function removeDirectory(AbstractDirectory $directory): void
    {
        $name = $directory->getLocalName();
        if (!array_key_exists($name, $this->directories)) {
            throw new TreeException(sprintf('Directory "%s" is not a child.', $name));
        }
        $directory->setParent(null);
        unset($this->directories[$name]);
    }

    /**
     * @param string $name
     * @return AbstractDirectory
     * @throws NotFoundException
     */
    public function getDirectoryBy(string $name): AbstractDirectory
    {
        if (!array_key_exists($name, $this->directories)) {
            throw new NotFoundException('Directory not found.');
        }
        return $this->directories[$name];
    }

    /**
     * @return void
     * @throws TreeException
     */
    public function removeFromParent(): void
    {
        if (empty($this->parent)) {
            return;
        }
        $this->parent->removeDirectory($this);
    }

    /**
     * @return bool
     */
    public function isRoot(): bool
    {
        return empty($this->parent);
    }

    /**
     * @param $name
     * @return bool
     */
    public function containsDirectory($name): bool
    {
        return array_key_exists($name, $this->directories);
    }

    /**
     * @return AbstractFile[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param string $name
     * @return AbstractFile
     * @throws NotFoundException
     */
    public function getFileBy(string $name): AbstractFile
    {
        if (!array_key_exists($name, $this->files)) {
            throw new NotFoundException('File not found.');
        }
        return $this->files[$name];
    }

    /**
     * @param AbstractFile $file
     * @return void
     * @throws AlreadyExistException
     */
    public function addFile(AbstractFile $file): void
    {
        $name = $file->getLocalName();
        if (array_key_exists($name, $this->directories) || array_key_exists($name, $this->files)) {
            throw new AlreadyExistException(sprintf(static::ALREADY_EXISTS_MESSAGE, $name));
        }
        $parent = $file->getParent();
        if (!empty($parent)) {
            $parent->removeFile($file);
        }
        $file->setParent($this);
        $this->files[$name] = $file;
    }

    /**
     * @param AbstractFile $file
     * @return void
     * @throws TreeException
     */
    public function removeFile(AbstractFile $file): void
    {
        $name = $file->getLocalName();
        if (!array_key_exists($name, $this->files)) {
            throw new TreeException(sprintf('File "%s" is not a child.', $name));
        }
        $file->setParent(null);
        unset($this->files[$name]);
    }

    /**
     * @param string|null $name
     * @throws TreeException
     */
    public function setLocalName(?string $name = null): void
    {
        $oldName = $this->getLocalName();
        if ($name === $oldName) {
            return;
        }

        parent::setLocalName($name);

        if (empty($this->parent)) {
            return;
        }

        $this->parent->updateDirectoryName($oldName);
    }

    /**
     * @param string $oldName
     * @return void
     * @throws TreeException
     */
    public function updateDirectoryName(string $oldName): void
    {
        if (!array_key_exists($oldName, $this->directories)) {
            throw new TreeException(sprintf('Directory "%s" is not a child.', $oldName));
        }
        $directory = $this->directories[$oldName];
        $newName = $directory->getLocalName();

        if ($oldName === $newName) {
            return;
        }

        if (array_key_exists($newName, $this->directories)) {
            throw new TreeException(sprintf('A directory named "%s" already exists in this directory.', $newName));
        }

        unset($this->directories[$oldName]);
        $this->directories[$newName] = $directory;
    }

    /**
     * @param string $oldName
     * @return void
     * @throws TreeException
     */
    public function updateFileName(string $oldName): void
    {
        if (!array_key_exists($oldName, $this->files)) {
            throw new TreeException(sprintf('File "%s" is not a child.', $oldName));
        }
        $file = $this->files[$oldName];
        $newName = $file->getLocalName();
        if ($oldName === $newName) {
            return;
        }
        if (array_key_exists($newName, $this->files)) {
            throw new TreeException(sprintf('A file named "%s" already exists in this directory.', $newName));
        }
        unset($this->files[$oldName]);
        $this->files[$newName] = $file;
    }

    /**
     * @param $name
     * @return bool
     */
    public function containsFile($name): bool
    {
        return array_key_exists($name, $this->files);
    }

}