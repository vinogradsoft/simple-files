<?php
declare(strict_types=1);

namespace Vinograd\SimpleFiles;

use Vinograd\SimpleFiles\Exception\TreeException;

abstract class AbstractFile extends NestedObject
{

    /**
     * @return void
     */
    public function removeFromParent(): void
    {
        if (empty($this->parent)) {
            throw new TreeException('Parent Directory not found.');
        }
        $this->parent->removeFile($this);
    }

    /**
     * @param string|null $name
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
        $this->parent->updateFileName($oldName);
    }

    /**
     * @return bool
     */
    public function isChild(): bool
    {
        return !empty($this->parent);
    }

    /**
     * @return void
     */
    abstract public function write(): void;

    /**
     * @return void
     */
    public function delete(): void
    {
        $this->removeSelf();
        $this->setParent();
    }

    /**
     * @return void
     */
    abstract protected function removeSelf(): void;

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->getExtensionImpl($this->getLocalName());
    }

    /**
     * @param string $name
     * @return false|string
     */
    private function getExtensionImpl(string $name): false|string
    {
        $n = strrpos($name, ".");
        return ($n === false) ? "" : substr($name, $n + 1);
    }

    /**
     * @return string
     */
    public function getSourceExtension(): string
    {
        return $this->getExtensionImpl((string)$this->pathObject->getLast());
    }

}