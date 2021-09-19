<?php

namespace Vinograd\SimpleFiles;

class NestedObject extends FilesystemObject
{
    /**@var NestedObject */
    protected $parent;

    /** @var null|string */
    protected $localName = null;

    /**
     * @param NestedObject|null $parent
     */
    public function setParent(?NestedObject $parent = null): void
    {
        $this->parent = $parent;
    }

    /**
     * @return NestedObject|null
     */
    public function getParent(): ?NestedObject
    {
        return $this->parent;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->pathObject->getName();
    }

    /**
     * @return string
     */
    public function getLocalName(): string
    {
        if (empty($this->localName)) {
            return $this->pathObject->getName();
        }
        return $this->localName;
    }

    /**
     * @param string|null $name
     */
    public function setLocalName(?string $name = null): void
    {
        $this->localName = $name;
    }

    /**
     * @param string $prefix
     * @return string
     */
    public function getLocalPath(string $prefix = ''): string
    {
        return $prefix . implode(DIRECTORY_SEPARATOR, $this->getLocalArrayPath());
    }

    /**
     * @return string[]
     */
    public function getLocalArrayPath(): array
    {
        $path = [$this->getLocalName()];
        $parent = $this->getParent();
        while (!empty($parent)) {
            array_unshift($path, $parent->getLocalName());
            $parent = $parent->getParent();
        }
        return $path;
    }
}