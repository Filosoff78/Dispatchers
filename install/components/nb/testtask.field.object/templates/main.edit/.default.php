<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */
use Bitrix\Main\Localization\Loc;

\Bitrix\Main\UI\Extension::load("ui.entity-selector");
$isMulti = $arResult['userField']['MULTIPLE'] === 'Y';
$message = Loc::loadLanguageFile(__FILE__);
?>
<div id="tag-selector__<?= $arResult['arJsParams']['fieldFormName']; ?>"></div>
<div id="tag-selector-result__<?= $arResult['arJsParams']['fieldFormName']; ?>">
    <?php if (!$isMulti) : ?>
        <input type="hidden" id="<?= $arResult['arJsParams']['fieldName']; ?>_input"
               name="<?= $arResult['arJsParams']['fieldName']; ?>"
               value="<?= $arResult['OBJECTS'][0]['ID'] ?>">
    <?php else: ?>
        <input type="hidden" id="<?= $arResult['arJsParams']['fieldName']; ?>_input"
               name="<?= $arResult['arJsParams']['fieldName']; ?>"
               value="<?= (implode(',', array_column($arResult['OBJECTS'], 'ID'))); ?>">
    <?php endif; ?>
</div>
<script type="application/javascript">
  BX.message(<?=CUtil::PhpToJSObject($message)?>);
  new BX.ObjectsTagSelector(<?=\CUtil::PhpToJSObject($arResult['arJsParams'])?>);
</script>
