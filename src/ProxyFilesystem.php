<?php
declare(strict_types=1);

namespace Vinograd\SimpleFiles;

use BadMethodCallException;
use Vinograd\IO\Exception\IOException;
use Vinograd\IO\Filesystem;

class ProxyFilesystem implements Filesystem
{
    /** @var Filesystem */
    protected $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem): void
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @return Filesystem
     */
    public function extractFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * @param string $path
     * @return string
     * @throws IOException
     */
    public function getAbsolutePath(string $path): string
    {
        return $this->filesystem->getAbsolutePath($path);
    }

    /**
     * @param string $path
     * @return array
     * @throws IOException
     */
    public function scanDirectory(string $path): array
    {
        return $this->filesystem->scanDirectory($path);
    }

    /**
     * @param string $filename
     * @return bool
     */
    public function exists(string $filename): bool
    {
        return $this->filesystem->exists($filename);
    }

    /**
     * @param string $directory
     * @param int $permissions
     * @throws IOException
     */
    public function createDirectory(string $directory, int $permissions = 0777): void
    {
        $this->filesystem->createDirectory($directory, $permissions);
    }

    /**
     * @param string $directory
     * @throws IOException
     */
    public function removeDirectory(string $directory): void
    {
        $this->filesystem->removeDirectory($directory);
    }

    /**
     * @param string $filename
     * @return bool
     */
    public function isDirectory(string $filename): bool
    {
        return $this->filesystem->isDirectory($filename);
    }

    /**
     * @param string $filename
     * @return bool
     */
    public function isFile(string $filename): bool
    {
        return $this->filesystem->isFile($filename);
    }

    /**
     * @param string $filename
     * @return string
     * @throws IOException
     */
    public function fileGetContents(string $filename): string
    {
        return $this->filesystem->fileGetContents($filename);
    }

    /**
     * @param string $filename
     * @param mixed $data
     * @param int $flags
     * @throws IOException
     */
    public function filePutContents(string $filename, $data, int $flags = 0): void
    {
        $this->filesystem->filePutContents($filename, $data, $flags);
    }

    /**
     * @param string $filename
     * @throws IOException
     */
    public function removeFile(string $filename): void
    {
        $this->filesystem->removeFile($filename);
    }

    /**
     * @param $filename
     * @param int $pos
     * @param null|mixed $ndocs
     * @param array $callbacks
     * @return mixed
     * @throws IOException
     */
    public function yamlParseFile($filename, $pos = 0, &$ndocs = null, array $callbacks = [])
    {
        return $this->filesystem->yamlParseFile($filename, $pos, $ndocs, $callbacks);
    }

    /**
     * @param $method
     * @param $args
     * @return false|mixed
     */
    public function __call($method, $args)
    {
        if (method_exists($this->filesystem, $method)) {
            return call_user_func_array([$this->filesystem, $method], $args);
        }
        throw new BadMethodCallException('Calling unknown method ' . get_class($this->filesystem) . '::' . $method . '(...))');
    }
}