<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */

$isMulti = $arResult['userField']['MULTIPLE'] === 'Y';

$isMulti ? $compStr = implode(', ', $arResult['OBJECTS'] ?: [])
         : $compStr = $arResult['OBJECTS'][0];

print $compStr['NAME'] ?: 'Не выбран';
