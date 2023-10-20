<?php
declare(strict_types=1);

namespace Vinograd\SimpleFiles;

use Vinograd\Support\AbstractFunctionality;
use Vinograd\Support\SupportedFunctionalities;

abstract class AbstractFilesystemFunctionality extends AbstractFunctionality
{
    /**
     * @param SupportedFunctionalities $component
     * @param string $methodName
     */
    protected function assignMethod(SupportedFunctionalities $component, string $methodName): void
    {
        FileFunctionalitiesContext::getFunctionalitySupport($component)->installMethod($this, $methodName);
    }

    /**
     * @param SupportedFunctionalities $component
     * @param string $methodName
     */
    protected function revokeMethod(SupportedFunctionalities $component, string $methodName): void
    {
        FileFunctionalitiesContext::getFunctionalitySupport($component)->uninstallMethod($methodName);
    }
}