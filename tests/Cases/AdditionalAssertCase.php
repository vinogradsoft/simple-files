<?php

namespace Test\Cases;

use BadMethodCallException;
use Vinograd\SimpleFiles\Directory;
use Vinograd\SimpleFiles\File;
use Vinograd\SimpleFiles\FileFunctionalitiesContext;

abstract class AdditionalAssertCase extends IoEnvCase
{
    protected function assertEmptyObjectDirectory(Directory $directory)
    {
        $this->expectException(BadMethodCallException::class);
        $this->assertPropertyEmpty($directory, 'directories');
        $this->assertPropertyEmpty($directory, 'files');
        $this->assertPropertyEmpty($directory, 'parent');
        $this->assertPropertyEmpty($directory, 'pathObject');
        self::assertFalse($directory->isBinded());
        $directory->delete();
    }

    protected function assertEmptyObjectFile(File $file)
    {
        $this->assertPropertyEmpty($file, 'content');
        $this->assertPropertyEmpty($file, 'parent');
        $this->assertPropertyEmpty($file, 'pathObject');
        $this->assertPropertyEmpty($file, 'listeners');
        self::assertFalse($file->isBinded());
        $support = FileFunctionalitiesContext::getFunctionalitySupport($file);
        self::assertFalse($support->has('sync'));
        self::assertFalse($support->has('copy'));
        self::assertFalse($support->has('remove'));
        self::assertFalse($support->has('read'));
        self::assertFalse($support->has('write'));
        self::assertFalse($support->has('move'));
        self::assertFalse($support->has('assertInitBind'));
    }

    protected function assertPropertyEmpty($object, string $propertyName)
    {
        $reflection = new \ReflectionObject($object);

        $storagePrototype = $reflection->getProperty($propertyName);
        $storagePrototype->setAccessible(true);
        $value = $storagePrototype->getValue($object);
        if (is_array($value)) {
            if (empty($value)) {
                self::assertTrue(true);
            }
        } else {
            self::assertEmpty($value);
        }
    }
}