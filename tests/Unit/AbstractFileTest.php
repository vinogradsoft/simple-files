<?php
declare(strict_types=1);

namespace Test\Unit;

use Test\Cases\FileSystemCase;
use Test\Cases\Helper\HelperForAbstractClasses;
use Vinograd\SimpleFiles\AbstractDirectory;
use Vinograd\SimpleFiles\AbstractFile;
use Vinograd\SimpleFiles\Exception\TreeException;

class AbstractFileTest extends FileSystemCase
{
    public function testRemoveFromParent()
    {
        $directory = $this->getMockForAbstractClass(AbstractDirectory::class, ['root']);
        $file = $this->getMockForAbstractClass(AbstractFile::class, ['path/to/file']);

        $directory->addFile($file);
        $file->removeFromParent();

        $directories = $directory->getDirectories();
        self::assertCount(0, $directories);
        self::assertEmpty($file->getParent());
    }

    public function testRemoveFromParentExcept()
    {
        $this->expectException(TreeException::class);

        $file = $this->getMockForAbstractClass(AbstractFile::class, ['path/to/file']);
        $file->removeFromParent();
    }

    public function testIsChild()
    {
        $directory = $this->getMockForAbstractClass(AbstractDirectory::class, ['root']);
        $file = $this->getMockForAbstractClass(AbstractFile::class, ['path/to/file']);

        $directory->addFile($file);

        self::assertTrue($file->isChild());

        $file->removeFromParent();

        self::assertFalse($file->isChild());
    }

    public function testRemove()
    {
        $this->createFile($filePath = $this->getRuntimePath() . '/file.test');
        //chek
        self::assertFileExists($filePath, sprintf('The service file was not created! (%s)', $filePath));

        $file = HelperForAbstractClasses::mockFile($filePath);
        $file->delete();

        self::assertFileDoesNotExist($filePath);
    }

    /**
     * @dataProvider getCasesGetExtension
     */
    public function testGetExtension($ext, $filePath)
    {
        $file = $this->getMockForAbstractClass(AbstractFile::class, [$filePath]);
        self::assertEquals($ext, $file->getExtension());
    }

    /**
     * @dataProvider getCasesGetExtension
     */
    public function testGetSourceExtension($ext, $filePath)
    {
        $file = $this->getMockForAbstractClass(AbstractFile::class, [$filePath]);
        self::assertEquals($ext, $file->getSourceExtension());
    }

    public function getCasesGetExtension()
    {
        return [
            ['test', 'path/to/file.test'],
            ['test', 'path/to/.test'],
            ['', 'path/to/test'],
        ];
    }

}
