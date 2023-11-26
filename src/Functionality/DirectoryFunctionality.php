<?php
declare(strict_types=1);

namespace Vinograd\SimpleFiles\Functionality;

use Vinograd\IO\Exception\IOException;
use Vinograd\IO\Exception\NotFoundException;
use Vinograd\IO\Filesystem;
use Vinograd\SimpleFiles\AbstractFilesystemFunctionality;
use Vinograd\SimpleFiles\Directory;
use Vinograd\Support\SupportedFunctionalities;
use Vinograd\Support\Functionality;

class DirectoryFunctionality extends AbstractFilesystemFunctionality
{

    private static DirectoryFunctionality|null $self = null;

    /**
     * @inheritDoc
     */
    protected function installMethods(SupportedFunctionalities $component): void
    {
        $this->assignMethod($component, 'sync');
        $this->assignMethod($component, 'copy');
        $this->assignMethod($component, 'delete');
        $this->assignMethod($component, 'assertInitBind');
    }

    /**
     * @param SupportedFunctionalities $component
     */
    protected function uninstallMethods(SupportedFunctionalities $component): void
    {
        $this->revokeMethod($component, 'sync');
        $this->revokeMethod($component, 'copy');
        $this->revokeMethod($component, 'delete');
        $this->revokeMethod($component, 'assertInitBind');
    }

    /**
     * @param $method
     * @param $arguments
     * @return bool
     */
    protected function checkArguments($method, $arguments): bool
    {
        if ($method === 'copy' || $method === 'sync') {
            if (count($arguments) === 1 && is_string($arguments[0]) && !empty($arguments[0])) {
                return true;
            }
        }
        if ($method === 'assertInitBind') {
            if (count($arguments) === 1 && is_string($arguments[0])) {
                return true;
            }
        }

        if (count($arguments) !== 1) {
            return false;
        }
        return is_array($arguments[0]);
    }

    /**
     * @param Directory $directory
     * @param Filesystem $filesystem
     * @param string $path
     * @return void
     * @throws NotFoundException
     */
    public function assertInitBind(Directory $directory, Filesystem $filesystem, string $path): void
    {
        try {
            $filesystem->getAbsolutePath($path);
        } catch (IOException $e) {
            throw new NotFoundException(sprintf('The linked directory %s was not found.', $path), 0, $e);
        }
    }

    /**
     * @param Directory $directory
     * @param Filesystem $filesystem
     * @param string $path
     * @return void
     * @throws IOException
     */
    public function sync(Directory $directory, Filesystem $filesystem, string $path): void
    {
        if (!$filesystem->exists($path)) {
            $this->createDirectory($directory, $filesystem, $path);
        }
    }

    /**
     * @param Directory $directory
     * @param Filesystem $filesystem
     * @param string $path
     * @return void
     * @throws IOException
     */
    protected function createDirectory(Directory $directory, Filesystem $filesystem, string $path): void
    {
        $filesystem->createDirectory($path);
    }

    /**
     * @param Directory $directory
     * @param Filesystem $filesystem
     * @param string $path
     * @return void
     * @throws IOException
     */
    public function copy(Directory $directory, Filesystem $filesystem, string $path): void
    {
        if (!$filesystem->exists($path)) {
            $filesystem->createDirectory($path);
        }
    }

    /**
     * @param Directory $directory
     * @param Filesystem $filesystem
     * @param array $deletePaths
     * @return void
     * @throws IOException
     */
    public function delete(Directory $directory, Filesystem $filesystem, array $deletePaths): void
    {
        arsort($deletePaths);
        foreach ($deletePaths as $path) {
            $filesystem->removeDirectory($path);
        }
    }

    /**
     * @param SupportedFunctionalities $component
     * @return Functionality
     */
    public static function create(SupportedFunctionalities $component): Functionality
    {
        if (static::$self === null) {
            static::$self = new self();
            return static::$self;
        }
        return static::$self;
    }

}