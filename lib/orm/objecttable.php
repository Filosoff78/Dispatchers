<?php
namespace NB\TestTask\ORM;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;

Loc::loadMessages(__FILE__);

class ObjectTable extends DataManager
{
    public static function getTableName()
    {
        return 'nb_tt_object';
    }

    /**
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function getMap()
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete()
                ->configureTitle('ID'),

            (new DatetimeField('DATE_CREATE'))
                ->configureDefaultValue(static function () {
                    return new DateTime();
                })
                ->configureTitle(Loc::getMessage('NB_TESTTASK_OBJECT_TABLE_CREATE')),

            (new StringField('NAME'))
                ->configureRequired()
                ->configureTitle(Loc::getMessage('NB_TESTTASK_OBJECT_TABLE_NAME')),

            (new StringField('ADDRESS'))
                ->configureRequired()
                ->configureTitle(Loc::getMessage('NB_TESTTASK_OBJECT_TABLE_ADDRESS')),

            (new StringField('COMMENT'))
                ->configureRequired()
                ->configureTitle(Loc::getMessage('NB_TESTTASK_OBJECT_TABLE_COMMENT')),
        ];
    }

    public static function getFieldsInfo()
    {
        $map = self::getEntity()->getFields();

        foreach ($map as $field) {
            if ($field instanceof Reference)
                continue;

            $fieldsMap[$field->getName()] = [
                'NAME' => $field->getName(),
                'TITLE' => $field->getTitle(),
                'IS_REQUIRED' => $field->isRequired(),
                'DEFAULT_VALUE' => $field->getDefaultValue(),
                'DATA_TYPE' => $field->getDataType(),
                'IS_PRIMARY' => $field->isPrimary()
            ];
        }
        return $fieldsMap ?? [];
    }
}
