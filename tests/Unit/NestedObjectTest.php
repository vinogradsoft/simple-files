<?php

namespace Test\Unit;

use Vinograd\SimpleFiles\NestedObject;
use PHPUnit\Framework\TestCase;

class NestedObjectTest extends TestCase
{
    public function testSetParent()
    {
        $path = new NestedObject('path');
        $to = new NestedObject('path/to');
        $to->setParent($path);
        self::assertSame($path, $to->getParent());
    }

    public function testSetParentEmpty()
    {
        $path = new NestedObject('path');
        $to = new NestedObject('path/to');
        $to->setParent($path);

        $to->setParent();
        self::assertEmpty($to->getParent());
    }

    public function testParentOne()
    {
        $path = new NestedObject('path');
        $to = new NestedObject('path/to');
        $other = new NestedObject('path/other');
        $to->setParent($path);
        $to->setParent($other);
        self::assertSame($other, $to->getParent());
    }

    public function testGetName()
    {
        $path = new NestedObject($pathTest = 'path');
        $toTest = 'to';
        $to = new NestedObject('path/' . $toTest);

        self::assertEquals($pathTest, $path->getName());
        self::assertEquals($toTest, $to->getName());
    }

    public function testGetLocalPath()
    {
        $path = new NestedObject('path');
        $to = new NestedObject('to');
        $test = new NestedObject('test');
        $to->setParent($path);
        $test->setParent($to);

        self::assertEquals('path/to/test', $test->getLocalPath());
        self::assertEquals(__DIR__ . '/path/to/test', $test->getLocalPath(__DIR__ . '/'));
        self::assertEquals('~/path/to/test', $test->getLocalPath('~/'));
        self::assertEquals('path/to', $to->getLocalPath());
        self::assertEquals('path', $path->getLocalPath());
    }

    public function testGetLocalArrayPath()
    {
        $path = new NestedObject('path');
        $to = new NestedObject('to');
        $test = new NestedObject('test');
        $to->setParent($path);
        $test->setParent($to);

        self::assertEquals('path/to/test', implode(DIRECTORY_SEPARATOR, $test->getLocalArrayPath()));
        self::assertEquals(__DIR__ . '/path/to/test', __DIR__ . '/' . implode(DIRECTORY_SEPARATOR, $test->getLocalArrayPath()));
        self::assertEquals('~/path/to/test', '~/' . implode(DIRECTORY_SEPARATOR, $test->getLocalArrayPath()));
        self::assertEquals('path/to', implode(DIRECTORY_SEPARATOR, $to->getLocalArrayPath()));
        self::assertEquals('path', implode(DIRECTORY_SEPARATOR, $path->getLocalArrayPath()));
    }

    public function testGetLocalName()
    {
        $object = new NestedObject('path/to');
        self::assertEquals('to', $object->getLocalName());
        self::assertEquals('to', $object->getName());

        $object->setLocalName('newName');
        self::assertEquals('newName', $object->getLocalName());
        self::assertEquals('to', $object->getName());

        $object->setLocalName(null);
        self::assertEquals('to', $object->getLocalName());
        self::assertEquals('to', $object->getName());
    }
}
