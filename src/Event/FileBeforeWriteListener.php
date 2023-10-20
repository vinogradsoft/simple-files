<?php
declare(strict_types=1);

namespace Vinograd\SimpleFiles\Event;

use Vinograd\SimpleFiles\File;

interface FileBeforeWriteListener
{
    /**
     * @param File $file
     * @param string $keyOperation
     */
    public function beforeWrite(File $file, string $keyOperation): void;

}