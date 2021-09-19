<?php

namespace Vinograd\SimpleFiles;

trait GetExtensionTrait
{
    /**
     * @return string
     */
    public function getExtension(): string
    {
        $name = $this->pathObject->getName();
        $n = strrpos($name, ".");
        return ($n === false) ? "" : substr($name, $n + 1);
    }
}