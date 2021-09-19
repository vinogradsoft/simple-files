<?php

namespace Test\Unit\FileFunctionalitiesContext;

use Vinograd\SimpleFiles\FileFunctionalities;
use Vinograd\SimpleFiles\FileFunctionalitiesContext;
use PHPUnit\Framework\TestCase;
use Vinograd\SimpleFiles\ProxyFilesystem;

class FileFunctionalitiesContextForGroupTest extends TestCase
{
    public function testGetGroupFunctionalitySupport()
    {
        $groupFunctionalitySupport = FileFunctionalitiesContext::getGroupFunctionalitySupport('group1');
        if ($groupFunctionalitySupport instanceof FileFunctionalities) {
            self::assertTrue(true);
        } else {
            self::fail();
        }
        $groupFunctionalitySupportControl = FileFunctionalitiesContext::getGroupFunctionalitySupport('group1');
        $groupFunctionalitySupport2 = FileFunctionalitiesContext::getGroupFunctionalitySupport('group2');
        self::assertSame($groupFunctionalitySupport, $groupFunctionalitySupportControl);
        self::assertNotSame($groupFunctionalitySupport, $groupFunctionalitySupport2);
    }

    public function testGetGroupFunctionalitySupportCheckFilesystem()
    {
        $groupFunctionalitySupport = FileFunctionalitiesContext::getGroupFunctionalitySupport('group1');
        $filesystem = $groupFunctionalitySupport->getFilesystem();
        if ($filesystem instanceof ProxyFilesystem) {
            self::assertTrue(true);
        } else {
            self::fail();
        }
    }

    public function tearDown(): void
    {
        FileFunctionalitiesContext::reset();
    }
}
