<?php
declare(strict_types=1);

namespace Vinograd\SimpleFiles;

trait GetExtensionTrait
{

    /**
     * @return string
     */
    public function getExtension(): string
    {
        $name = $this->pathObject->getLast();
        $n = strrpos($name, ".");
        return ($n === false) ? "" : substr($name, $n + 1);
    }

}