<?php
declare(strict_types=1);

namespace Vinograd\SimpleFiles;

use Compass\Path;

class FilesystemObject extends AbstractFilesystemObject
{

    protected Path|null $pathObject = null;

    public function __construct(string $path)
    {
        $this->setPath(new Path($path));
        $this->initFunctionalities();
    }

    /**
     * @return void
     */
    protected function initFunctionalities(): void
    {

    }

    /**
     * @return Path|null
     */
    public function getPath(): ?Path
    {
        return $this->pathObject;
    }

    /**
     * @param Path $path
     * @return void
     */
    protected function setPath(Path $path): void
    {
        $this->pathObject = $path;
    }

    /**
     * @inheritDoc
     */
    protected function setData(mixed $data): void
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