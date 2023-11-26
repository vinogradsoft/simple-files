<?php
declare(strict_types=1);

namespace Vinograd\SimpleFiles;

use BadMethodCallException;
use LogicException;
use Vinograd\Support\ContextFunctionalitySupport;
use Vinograd\Support\Functionality;
use Vinograd\Support\FunctionalitySupport;
use Vinograd\IO\Filesystem;

class FileFunctionalitiesContext extends ContextFunctionalitySupport
{

    protected static FunctionalitySupport|null $globalDirectorySupport = null;
    protected static FunctionalitySupport|null $globalFileSupport = null;
    protected static Filesystem|null $filesystem = null;
    protected static ProxyFilesystem|null $filesystemForGroups = null;
    protected static ProxyFilesystem|null $filesystemForGlobal = null;

    /**
     * @param Functionality $support
     * @param string $methodName
     * @return void
     */
    public static function registerGlobalFunctionalityForDirectories(Functionality $support, string $methodName): void
    {
        if (self::$globalDirectorySupport === null) {
            self::$globalDirectorySupport = static::createGlobalFunctionalitySupport();
        }
        self::$globalDirectorySupport->installMethod($support, $methodName);
    }

    /**
     * @param string $methodName
     * @return void
     */
    public static function unregisterGlobalFunctionalityForDirectories(string $methodName): void
    {
        if (self::$globalDirectorySupport === null) {
            throw new LogicException($methodName . '(...))' . ' - method has not been registered.');
        }
        self::$globalDirectorySupport->uninstallMethod($methodName);
    }

    /**
     * @param string $methodName
     * @return bool
     */
    public static function hasGlobalFunctionalityForDirectories(string $methodName): bool
    {
        if (self::$globalDirectorySupport === null) {
            return false;
        }
        return self::$globalDirectorySupport->has($methodName);
    }

    /**
     * @param $source
     * @param string $methodName
     * @param array $arguments
     * @return mixed
     */
    public static function fireGlobalDirectoryMethod($source, string $methodName, array $arguments): mixed
    {
        if (self::$globalDirectorySupport === null) {
            throw new BadMethodCallException('Calling unknown method ' . get_class($source) . '::' . $methodName . '(...))');
        }
        return self::$globalDirectorySupport->fireCallMethodEvent($source, $methodName, $arguments);
    }

    /**
     * @param Functionality $support
     * @param string $methodName
     * @return void
     */
    public static function registerGlobalFunctionalityForFiles(Functionality $support, string $methodName): void
    {
        if (self::$globalFileSupport === null) {
            self::$globalFileSupport = static::createGlobalFunctionalitySupport();
        }
        self::$globalFileSupport->installMethod($support, $methodName);
    }

    /**
     * @param string $methodName
     * @return void
     */
    public static function unregisterGlobalFunctionalityForFiles(string $methodName): void
    {
        if (self::$globalFileSupport === null) {
            throw new LogicException($methodName . '(...))' . ' - method has not been registered.');
        }
        self::$globalFileSupport->uninstallMethod($methodName);
    }

    /**
     * @param string $methodName
     * @return bool
     */
    public static function hasGlobalFunctionalityForFiles(string $methodName): bool
    {
        if (self::$globalFileSupport === null) {
            return false;
        }
        return self::$globalFileSupport->has($methodName);
    }

    /**
     * @param $source
     * @param string $methodName
     * @param array $arguments
     * @return mixed
     */
    public static function fireGlobalFileMethod($source, string $methodName, array $arguments): mixed
    {
        if (static::$globalFileSupport === null) {
            throw new BadMethodCallException('Calling unknown method ' . get_class($source) . '::' . $methodName . '(...))');
        }
        return static::$globalFileSupport->fireCallMethodEvent($source, $methodName, $arguments);
    }

    /**
     * @return FunctionalitySupport
     */
    protected static function createFunctionalitySupport(): FunctionalitySupport
    {
        return new FileFunctionalities(static::getFilesystem());
    }

    /**
     * @return FunctionalitySupport
     */
    protected static function createGroupFunctionalitySupport(): FunctionalitySupport
    {
        return new FileFunctionalities(static::getFilesystemForGroups());
    }

    /**
     * @return FunctionalitySupport
     */
    protected static function createGlobalFunctionalitySupport(): FunctionalitySupport
    {
        return new FileFunctionalities(static::getFilesystemForGlobal());
    }

    /**
     * @return ProxyFilesystem
     */
    protected static function getFilesystemForGlobal(): ProxyFilesystem
    {
        if (static::$filesystemForGlobal === null) {
            static::$filesystemForGlobal = new ProxyFilesystem(static::getFilesystem());
        }
        return static::$filesystemForGlobal;
    }

    /**
     * @return Filesystem
     */
    protected static function getFilesystem(): Filesystem
    {
        if (static::$filesystem === null) {
            static::$filesystem = new DefaultFilesystem();
        }
        return static::$filesystem;
    }

    /**
     * @return ProxyFilesystem
     */
    protected static function getFilesystemForGroups(): ProxyFilesystem
    {
        if (static::$filesystemForGroups === null) {
            static::$filesystemForGroups = new ProxyFilesystem(static::getFilesystem());
        }
        return static::$filesystemForGroups;
    }

    /**
     * @param Filesystem $filesystem
     * @return void
     */
    public static function setFilesystem(Filesystem $filesystem): void
    {
        static::getFilesystemForGlobal()->setFilesystem($filesystem);
        static::getFilesystemForGroups()->setFilesystem($filesystem);
        static::$filesystem = $filesystem;
    }

    /**
     * @return void
     */
    public static function reset(): void
    {
        static::$functionalitySupports = null;
        static::$functionalityForGroups = [];
        static::$globalDirectorySupport = null;
        static::$globalFileSupport = null;
        static::$filesystem = null;
        static::$filesystemForGroups = null;
        static::$filesystemForGlobal = null;
    }

}