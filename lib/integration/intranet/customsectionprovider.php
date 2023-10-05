<?php
namespace NB\TestTask\Integration\Intranet;

use \Bitrix\Intranet\CustomSection\Provider;
use \Bitrix\Intranet\CustomSection\Provider\Component;
use \Bitrix\Main\Web\Uri;

class CustomSectionProvider extends Provider
{
    public function isAvailable(string $pageSettings, int $userId): bool
    {
        return true;
    }

    public function resolveComponent(string $pageSettings, Uri $url): ?Component
    {
        $componentName = $pageSettings;
        $componentParameters = [];

        return (new Component())
            ->setComponentTemplate('')
            ->setComponentName('nb:'.$componentName)
            ->setComponentParams($componentParameters);
    }
}
