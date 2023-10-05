<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */

use Bitrix\Main\Localization\Loc;
use NB\TestTask\ORM\ObjectTable;

\Bitrix\Main\UI\Extension::load('ui.entity-selector');

$arResult['FIELD_FORM_NAME'] = str_replace(['[', ']'], '_', $arResult['fieldName']);

$arResult['arJsParams'] = [
    'fieldName' => $arResult['fieldName'],
    'userFieldName' => $arResult['userField']['FIELD_NAME'],
    'fieldFormName' => $arResult['FIELD_FORM_NAME'] ?? $arResult['userField']['FIELD_FORM_NAME'],
    'selectedItemIds' => $arResult['value'],
    'multiple' => $arResult['userField']['MULTIPLE'] === 'Y',
];

$arResult['arJsParams']['tabs'] = [
    [
        'id' => 'authors',
        'title' => Loc::getMessage("FIELD_COMR_SELECTOR_TITILE")
    ]
];

$arResult['OBJECTS'] = [];

if (empty($arResult['value']) || empty($arResult['value'][0]))
    return;

if (!$arResult['arJsParams']['multiple']) {
    $arResult['OBJECTS'][] = ObjectTable::query()
        ->where('ID', $arResult['value'])
        ->setSelect(['ID', 'NAME'])
        ->fetch();
} else {
    $arResult['OBJECTS'] = ObjectTable::query()
        ->whereIn('ID', explode(',', $arResult['value'][0]))
        ->setSelect(['ID', 'NAME'])
        ->fetchAll();
}

foreach ($arResult['OBJECTS'] as $object) {
    $arResult['arJsParams']['selectedItems'][] = [
        'id' => $object['ID'],
        'entityId' => 'objects',
        'title' => $object['NAME']
    ];
}
