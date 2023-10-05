<?php
namespace NB\TestTask\UserField\Types;
use \Bitrix\Main\UserField\Types\BaseType;
use \Bitrix\Main\Localization\Loc;

class ObjectsTypes extends BaseType
{
    public const
        USER_TYPE_ID = 'st_objects',
        RENDER_COMPONENT = 'nb:testtask.field.object';

    public static function getDescription(): array
    {
        return [
            'USER_TYPE_ID' => self::USER_TYPE_ID,
            'CLASS_NAME' => __CLASS__,
            'DESCRIPTION' => Loc::getMessage('NB_FIELD_OBJECTS_DESC'),
            'BASE_TYPE' => \CUserTypeManager::BASE_TYPE_INT
        ];
    }

    public static function getDbColumnType(): string
    {
        global $DB;
        switch(strtolower($DB->type))
        {
            case "mysql":
                return "int(18)";
            case "oracle":
                return "number(18)";
            case "mssql":
                return "int";
        }
        return "int";
    }

    public static function checkFields($userField, $value)
    {
        return [];
    }

}
