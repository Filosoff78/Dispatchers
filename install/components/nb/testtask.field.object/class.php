<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Component\BaseUfComponent;
use NB\TestTask\UserField\Types\ObjectsTypes;

class ObjectsUfComponent extends BaseUfComponent
{
    protected static function getUserTypeId(): string
    {
        return ObjectsTypes::USER_TYPE_ID;
    }
}
