<?php

namespace Test\Cases;

use Vinograd\SimpleFiles\FileFunctionalitiesContext;

abstract class IoEnvCase extends FileSystemCase
{
    protected $outDirectoryName;
    protected $outPath;

    public function setUp(): void
    {
        $this->createDirectory($this->outPath = $this->getRuntimePath() .  '/' . $this->outDirectoryName);
    }

    protected function createFilesystem(array $paths = []): void
    {
        if (empty($paths)) {
            $paths = [
                'directories' => [
                    $this->outPath . '/childL',
                    $this->outPath . '/childL/root',
                    $this->outPath . '/childL/root/child1',
                    $this->outPath . '/childL/root/child1/child2',
                    $this->outPath . '/childL/root/child1/child2/child3',
                    $this->outPath . '/childL/root/child1/child2/child3/child4',
                    $this->outPath . '/childL/root/child1/child2/child3/child4/child5',
                ],
                'files' => [
                    $this->outPath . '/childL/file1.txt' => 'initial1',
                    $this->outPath . '/childL/root/file7.txt' => 'initial7',
                    $this->outPath . '/childL/root/child1/file6.txt' => 'initial6',
                    $this->outPath . '/childL/root/child1/child2/file5.txt' => 'initial5',
                    $this->outPath . '/childL/root/child1/child2/child3/file4.txt' => 'initial4',
                    $this->outPath . '/childL/root/child1/child2/child3/child4/file3.txt' => 'initial3',
                    $this->outPath . '/childL/root/child1/child2/child3/child4/child5/file2.txt' => 'initial2',
                ],
            ];
        }

        if (isset($paths['directories'])) {
            foreach ($paths['directories'] as $directoryPath) {
                $this->createDirectory($directoryPath);
            }
        }
        if (isset($paths['files'])) {
            foreach ($paths['files'] as $filePath => $data) {
                $this->createFile($filePath, $data);
            }
        }

    }

    public function tearDown(): void
    {
        FileFunctionalitiesContext::reset();
        $this->delete($this->outPath);
    }
}