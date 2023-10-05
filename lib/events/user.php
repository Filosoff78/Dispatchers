<?php

namespace NB\TestTask\Events;

use Bitrix\Main\Loader;
use NB\TestTask\ORM\DispatcherTable;

class User
{
    public static function OnAfterUserUpdate(&$arFields) {
        if ($arFields['ACTIVE'] === 'N') {

            $dispatcherId = !!DispatcherTable::query()
                ->where('USER_ID', $arFields['ID'])
                ->setSelect(['ID'])
                ->fetch() ['ID'];

            if (!$dispatcherId) return true;

            DispatcherTable::update($dispatcherId, ['ACTIVE' => 0]);
            return true;
        }
    }
}