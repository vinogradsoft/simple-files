<?php

namespace Vinograd\SimpleFiles;

use Vinograd\IO\Exception\IOException;
use Vinograd\IO\Filesystem;

class DefaultFilesystem implements FileSystem
{
    /**
     * @param string $path
     * @return string
     * @throws IOException
     */
    public function getAbsolutePath(string $path): string
    {
        if (!$resultPath = realpath($path)) {
            throw new IOException(sprintf('Invalid path: %s', $path));
        }
        return $resultPath;
    }

    /**
     * @param string $filename
     * @return bool
     */
    public function exists(string $filename): bool
    {
        return file_exists($filename);
    }

    /**
     * @param string $directory
     * @param int $permissions
     * @throws IOException
     */
    public function createDirectory(string $directory, int $permissions = 0777): void
    {
        if (!@mkdir($directory, $permissions)) {
            throw new IOException(sprintf('Directory "%s" was not created.', $directory));
        }
    }

    /**
     * @param string $directory
     * @throws IOException
     */
    public function removeDirectory(string $directory): void
    {
        if (!@rmdir($directory)) {
            throw new IOException(sprintf('Unable to remove directory "%s".', $directory));
        }
    }

    /**
     * @param string $filename
     * @return bool
     */
    public function isDirectory(string $filename): bool
    {
        return is_dir($filename);
    }

    /**
     * @param string $filename
     * @return bool
     */
    public function isFile(string $filename): bool
    {
        return is_file($filename);
    }

    /**
     * @param string $filename
     * @return string
     * @throws IOException
     */
    public function fileGetContents(string $filename): string
    {
        if (!$content = @file_get_contents($filename)) {
            throw new IOException(sprintf('Unable to read file "%s".', $filename));
        }
        return $content;
    }

    /**
     * @param string $filename
     * @param mixed $data
     * @param int $flags
     * @throws IOException
     */
    public function filePutContents(string $filename, $data, int $flags = 0): void
    {
        $result = @file_put_contents($filename, $data, $flags);
        if ($result !== strlen($data)) {
            throw new IOException(sprintf('Unable to write to file "%s".', $filename));
        }
    }

    /**
     * @param string $filename
     * @throws IOException
     */
    public function removeFile(string $filename): void
    {
        if (!$result = @unlink($filename)) {
            throw new IOException(sprintf('Failed to delete file "%s".', $filename));
        }
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
        if (!$result = @\yaml_parse_file($filename, $pos, $ndocs, $callbacks)) {
            throw new IOException(sprintf('Unable to read file "%s".', $filename));
        }
        return $result;
    }
}