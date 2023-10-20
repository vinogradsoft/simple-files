<?php
declare(strict_types=1);

namespace Test\Unit;

use Test\Cases\Dummy\DummyAbstractFilesystemObject;
use Test\Cases\Dummy\DummyFunctionality;
use Vinograd\SimpleFiles\AbstractFilesystemObject;
use PHPUnit\Framework\TestCase;
use Vinograd\SimpleFiles\FileFunctionalitiesContext;

class AbstractFilesystemObjectTest extends TestCase
{
    public function testEquals()
    {
        $filesystemObject = $this->getMockForAbstractClass(
            AbstractFilesystemObject::class
        );
        $filesystemObject2 = $this->getMockForAbstractClass(
            AbstractFilesystemObject::class
        );
        self::assertEquals(false, $filesystemObject->equals($filesystemObject2));
        self::assertEquals(true, $filesystemObject->equals($filesystemObject));
    }

    public function testCloneWithData()
    {
        $filesystemObject = $this->getMockForAbstractClass(
            AbstractFilesystemObject::class
        );
        $dummyFunctionality = new DummyFunctionality();
        $dummyFunctionality->install($filesystemObject);

        $supportFilesystemObject = FileFunctionalitiesContext::getFunctionalitySupport($filesystemObject);
        $clone = $filesystemObject->cloneWithData('path/to');
        $supportClone = FileFunctionalitiesContext::getFunctionalitySupport($clone);

        self::assertTrue($supportFilesystemObject->has('get'));
        self::assertTrue($supportClone->has('get'));
        self::assertNotSame($supportFilesystemObject, $supportClone);
        self::assertNotSame($filesystemObject, $clone);
    }

    public function testRevokeAllSupports()
    {
        $filesystemObject = $this->getMockForAbstractClass(
            AbstractFilesystemObject::class
        );
        $dummyFunctionality = new DummyFunctionality();
        $dummyFunctionality->install($filesystemObject);

        $supportFilesystemObject = FileFunctionalitiesContext::getFunctionalitySupport($filesystemObject);
        $supportFilesystemObjectControl = FileFunctionalitiesContext::getFunctionalitySupport($filesystemObject);

        $filesystemObject->revokeAllSupports();

        $newSupport = FileFunctionalitiesContext::getFunctionalitySupport($filesystemObject);

        self::assertSame($supportFilesystemObject, $supportFilesystemObjectControl);
        self::assertNotSame($supportFilesystemObject, $newSupport);
    }

    public function test__call()
    {
        $filesystemObject = $this->getMockForAbstractClass(
            AbstractFilesystemObject::class
        );
        $dummyFunctionality = new DummyFunctionality();
        $dummyFunctionality->install($filesystemObject);
        $obj = $filesystemObject->get();
        $objControl = $filesystemObject->__call('get', []);
        self::assertSame($filesystemObject, $obj);
        self::assertSame($filesystemObject, $objControl);
    }

    public function testUninstallFunctionality()
    {
        $abstractFilesystemObject = new DummyAbstractFilesystemObject();
        $abstractFilesystemObject->installFunctionality($functionality = DummyFunctionality::create($abstractFilesystemObject));
        $support = FileFunctionalitiesContext::getFunctionalitySupport($abstractFilesystemObject);
        self::assertTrue($support->has('get'));
        $abstractFilesystemObject->uninstallFunctionality($functionality);
        self::assertFalse($support->has('get'));
    }

    public function tearDown(): void
    {
        FileFunctionalitiesContext::reset();
    }
}
