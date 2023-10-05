<?php
use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arResult */
/** @var array $arParams */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @var CBitrixComponentTemplate $this */
/** @var CBitrixComponent $component */

$APPLICATION->IncludeComponent(
    "bitrix:ui.form",
    "",
    $arResult['FORM']
);

if(!$_REQUEST["IFRAME"]):
?>
    <?$this->SetViewTarget('pagetitle');?>
        <a href="/page/menu_dispatcher/dispatcher_list/">
            <button class="ui-btn"><?= Loc::getMessage('RSB_RETURN_TO_LIST') ?></button>
        </a>
    <?$this->EndViewTarget();?>
<?endif;?>
