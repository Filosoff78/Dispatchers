<?php
namespace NB\TestTask\ORM;

use Bitrix\Main\Application;
use Bitrix\Main\Entity\Event;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;
use Bitrix\Main\ORM\Fields\Validators\RangeValidator;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;

Loc::loadMessages(__FILE__);

class DispatcherTable extends DataManager
{
    public static function getTableName()
    {
        return 'nb_tt_dispatcher';
    }

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
                ->setParameter('HIDE_IN_FORM', 'Y')
                ->configureTitle(Loc::getMessage('NB_TESTTASK_DSIP_TABLE_DATE_CREATE')),

            (new BooleanField('ACTIVE'))
                ->configureRequired()
                ->setParameter('ONLY_EDIT', 'Y')
                ->configureDefaultValue(1)
                ->configureTitle(Loc::getMessage('NB_TESTTASK_DSIP_TABLE_ACTIVE')),

            (new DatetimeField('ACTIVE_END'))
                ->configureTitle(Loc::getMessage('NB_TESTTASK_DSIP_TABLE_ACTIVE_END')),

            (new IntegerField('USER_ID'))
                ->configureRequired()
                ->configureTitle(Loc::getMessage('NB_TESTTASK_DSIP_TABLE_USER')),

            (new Reference(
                'REF_USER',
                UserTable::class,
                Join::on('this.USER_ID', 'ref.ID')
            )),

            (new StringField('COMMENT'))
                ->configureTitle(Loc::getMessage('NB_TESTTASK_DSIP_TABLE_COMMENT')),

            (new IntegerField('ACCESS_LEVEL'))
                ->configureRequired()
                ->addValidator(new RangeValidator(1, 12))
                ->configureTitle(Loc::getMessage('NB_TESTTASK_DSIP_TABLE_ACCESS_LEVEL')),

            (new IntegerField('OBJECT_ID'))
                ->configureTitle(Loc::getMessage('NB_TESTTASK_DSIP_TABLE_OBJECT')),

            (new Reference(
                'REF_OBJECT',
                ObjectTable::class,
                Join::on('this.OBJECT_ID', 'ref.ID')
            )),
        ];
    }

    public static function getFieldsInfo() : array
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
                'IS_PRIMARY' => $field->isPrimary(),
                'ONLY_EDIT' => $field->getParameter('ONLY_EDIT') === 'Y',
                'HIDE_IN_FORM' => $field->getParameter('HIDE_IN_FORM') === 'Y',
            ];
        }
        return $fieldsMap ?? [];
    }

    public static function onBeforeUpdate(Event $event){
        self::clearCache();
    }

    public static function onBeforeAdd(Event $event){
        self::clearCache();
    }

    public static function onBeforeDelete(Event $event){
        self::clearCache();
    }

    public static function clearCache() {
        $taggedCache = Application::getInstance()->getTaggedCache();
        $taggedCache->clearByTag('orm_' . self::getTableName());
    }
}
