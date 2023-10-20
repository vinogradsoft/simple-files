<?php
declare(strict_types=1);

namespace Test\Unit;

use Compass\Path;
use Vinograd\SimpleFiles\FilesystemObject;
use PHPUnit\Framework\TestCase;

class FilesystemObjectTest extends TestCase
{
    public function testGetPath()
    {
        $object = new FilesystemObject($test = 'path/to');
        $path = $object->getPath();
        $source = $path->getSource();
        self::assertEquals($source, $test);
        self::assertEquals(Path::class, get_class($path));
    }

    public function testCloneWithUseProtectedMethodSetData()
    {
        $object = new FilesystemObject($test = 'path/to');
        $newObject = $object->cloneWithData($check = 'new/path');
        self::assertNotSame($object, $newObject);
        self::assertEquals($check, $newObject->getPath()->getSource());
    }

    public function testCloneWithUseProtectedMethodSetDataExcept()
    {
        $this->expectException(\InvalidArgumentException::class);
        $object = new FilesystemObject('path/to');
        $object->cloneWithData(['new/path']);
    }
}
