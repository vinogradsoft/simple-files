<?php
declare(strict_types=1);

namespace Test\Cases;

abstract class FileSystemCase extends CommonCase
{

    protected function delete(string $path): void
    {
        if (!file_exists($path)) {
            throw new \RuntimeException('Undefined path ' . $path);
        }

        if (is_dir($path)) {
            foreach (scandir($path, SCANDIR_SORT_ASCENDING) as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }
                $this->delete($path . DIRECTORY_SEPARATOR . $item);
            }
            if (!rmdir($path)) {
                throw new \RuntimeException('Unable to delete directory ' . $path);
            }
        } else {
            if (basename($path) !== '.gitkeep') {
                if (!unlink($path)) {
                    throw new \RuntimeException('Unable to delete file ' . $path);
                }
            }
        }
    }

    protected function createDirectory(string $path): void
    {
        if (!file_exists($path)) {
            if (!mkdir($path) && !is_dir($path)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
            }
        } else {
            throw new \RuntimeException(sprintf('The project "%s" already exists.', $path));
        }
    }

    protected function createFile(string $path, string $content = ''): void
    {
        if (file_exists($path)) {
            throw new \RuntimeException(sprintf('The file (%s) already exists. ', $path));
        }
        file_put_contents($path, $content);
    }
}