<?php
declare(strict_types=1);

namespace Vinograd\SimpleFiles\Functionality;

use Vinograd\IO\Exception\IOException;
use Vinograd\IO\Exception\NotFoundException;
use Vinograd\IO\Filesystem;
use Compass\Exception\InvalidPathException;
use Vinograd\SimpleFiles\AbstractFile;
use Vinograd\SimpleFiles\AbstractFilesystemFunctionality;
use Vinograd\SimpleFiles\File;
use Vinograd\Support\SupportedFunctionalities;
use Vinograd\Support\Functionality;

class FileFunctionality extends AbstractFilesystemFunctionality
{

    private static $self = null;

    /**
     * @inheritDoc
     */
    protected function installMethods(SupportedFunctionalities $component): void
    {
        $this->assignMethod($component, 'sync');
        $this->assignMethod($component, 'copy');
        $this->assignMethod($component, 'remove');
        $this->assignMethod($component, 'read');
        $this->assignMethod($component, 'write');
        $this->assignMethod($component, 'move');
        $this->assignMethod($component, 'assertInitBind');
    }

    /**
     * @param SupportedFunctionalities $component
     */
    protected function uninstallMethods(SupportedFunctionalities $component): void
    {
        $this->revokeMethod($component, 'sync');
        $this->revokeMethod($component, 'copy');
        $this->revokeMethod($component, 'remove');
        $this->revokeMethod($component, 'read');
        $this->revokeMethod($component, 'write');
        $this->revokeMethod($component, 'move');
        $this->revokeMethod($component, 'assertInitBind');
    }

    /**
     * @param $method
     * @param $arguments
     * @return bool
     */
    protected function checkArguments($method, $arguments): bool
    {
        if ($method === 'sync' || $method === 'copy' || $method === 'move' || $method === 'write' || $method === 'read') {
            if (count($arguments) === 1 && is_string($arguments[0])) {
                return true;
            }
        }
        if ($method === 'assertInitBind') {
            if (count($arguments) === 1 && is_string($arguments[0])) {
                return true;
            }
        }
        //remove
        return empty($arguments);
    }

    /**
     * @param File $file
     * @param Filesystem $filesystem
     * @param string $path
     */
    public function assertInitBind(File $file, Filesystem $filesystem, string $path)
    {
        try {
            $filesystem->getAbsolutePath($path);
        } catch (IOException $e) {
            throw new NotFoundException(sprintf('The linked file %s was not found.', $path), 0, $e);
        }
    }

    /**
     * @param File $file
     * @param Filesystem $filesystem
     * @param string $path
     * @throws IOException
     */
    public function sync(File $file, Filesystem $filesystem, string $path): void
    {
        if (!$filesystem->exists($path)) {
            $this->createFile($file, $filesystem, $path);
        }
    }

    /**
     * @param File $file
     * @param Filesystem $filesystem
     * @param string $path
     * @throws IOException
     */
    public function copy(File $file, Filesystem $filesystem, string $path): void
    {
        $this->copyOrMove($file, $filesystem, $path, File::COPY);
    }

    /**
     * @param File $file
     * @param Filesystem $filesystem
     * @param string $path
     * @throws IOException
     */
    public function move(File $file, Filesystem $filesystem, string $path)
    {
        $this->copyOrMove($file, $filesystem, $path, File::MOVE);
    }

    /**
     * @param File $file
     * @param Filesystem $filesystem
     * @param string $path
     * @param string $keyOperation
     * @throws IOException
     */
    protected function copyOrMove(File $file, Filesystem $filesystem, string $path, string $keyOperation)
    {
        $this->read($file, $filesystem, $file->getSourcePath());
        $file->fireBeforeWriteEvent($keyOperation);
        $filesystem->filePutContents($path, $file->getContent());
    }

    /**
     * @param File $file
     * @param Filesystem $filesystem
     * @param string $path
     */
    protected function createFile(File $file, Filesystem $filesystem, string $path): void
    {
        $filesystem->filePutContents($path, '');
    }

    /**
     * @param File $file
     * @param Filesystem $filesystem
     * @param string $path
     */
    public function read(File $file, Filesystem $filesystem, string $path)
    {
        if ($filesystem->exists($path)) {
            $file->setContent($filesystem->fileGetContents($path));
        } else {
            throw new NotFoundException(sprintf('The file "%s" not found.', $path));
        }
    }

    /**
     * @param AbstractFile $file
     * @param Filesystem $filesystem
     * @param string $writePath
     */
    public function write(AbstractFile $file, Filesystem $filesystem, string $writePath)
    {
        $dir = dirname($writePath);
        if (!$filesystem->isDirectory($dir)) {
            throw new InvalidPathException(sprintf('Invalid write path - %s.', $writePath));
        }
        $filesystem->filePutContents($writePath, $file->getContent());
    }

    /**
     * @param AbstractFile $file
     * @param Filesystem $filesystem
     */
    public function remove(AbstractFile $file, Filesystem $filesystem)
    {
        $path = $file->getSourcePath();
        if ($filesystem->exists($path)) {
            $filesystem->removeFile($path);
        } else {
            throw new NotFoundException(sprintf('The file "%s" not found.', $path));
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