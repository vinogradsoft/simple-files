<?php
declare(strict_types=1);

namespace Vinograd\SimpleFiles;

use Compass\Path;

class FilesystemObject extends AbstractFilesystemObject
{
    /**@var Path */
    protected $pathObject;

    public function __construct(string $path)
    {
        $this->setPath(new Path($path));
        $this->initFunctionalities();
    }

    /**
     *
     */
    protected function initFunctionalities(): void
    {

    }

    /**
     * @return Path
     */
    public function getPath(): Path
    {
        return $this->pathObject;
    }

    /**
     * @param Path $path
     */
    protected function setPath(Path $path)
    {
        $this->pathObject = $path;
    }

    /**
     *
     * @param $data
     */
    protected function setData($data): void
    {
        try {
            $this->setPath(new Path($data));
        } catch (\TypeError $e) {
            throw new \InvalidArgumentException(
                'Argument is not of type string. File system path expected.', 0, $e
            );
        }
    }
}