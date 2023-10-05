<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */

use NB\TestTask\ORM\ObjectTable;

if ($arResult['userField']['MULTIPLE'] === 'Y' && !empty($arResult['value'][0])) {
    $objects = ObjectTable::query()
        ->whereIn('ID', explode(',', $arResult['value'][0]))
        ->setSelect(['ID', 'NAME'])
        ->fetchAll();
} else if ($arResult['userField']['MULTIPLE'] !== 'Y' && !empty($arResult['value'])) {
    $objects = ObjectTable::query()
        ->where('ID', $arResult['value'][0])
        ->setSelect(['ID', 'NAME'])
        ->fetch();
}

if (!empty($objects)) {
    if ($arResult['userField']['MULTIPLE'] === 'Y') {
        foreach ($objects as $object) {
            $arResult['OBJECTS'][] = $object;
        }
    } else {
        $arResult['OBJECTS'][] = $objects;
    }
}