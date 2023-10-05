<?php
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
    'bitrix:main.ui.grid',
    '',
    [
        "GRID_ID" => $arResult["GRID_ID"],
        "COLUMNS" => $arResult["HEADERS"],
        "ROWS" => $arResult["ROWS"],
        "MESSAGES" => [],
        "NAV_OBJECT" => $arResult["NAV_OBJECT"],
        "TOTAL_ROWS_COUNT" => $arResult["TOTAL_ROWS_COUNT"],
        "PAGE_SIZES" => $arResult["GRID_PAGE_SIZES"],
        "AJAX_MODE" => "Y",
        "AJAX_ID" => CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
        "ENABLE_NEXT_PAGE" => true,
        "ACTION_PANEL" => $arResult["ACTION_PANEL"],
        "AJAX_OPTION_JUMP" => "N",
        "SHOW_CHECK_ALL_CHECKBOXES" => true,
        "SHOW_ROW_CHECKBOXES" => true,
        "SHOW_ROW_ACTIONS_MENU" => true,
        "SHOW_GRID_SETTINGS_MENU" => true,
        "SHOW_NAVIGATION_PANEL" => true,
        "SHOW_PAGINATION" => true,
        "SHOW_SELECTED_COUNTER" => true,
        "SHOW_TOTAL_COUNTER" => true,
        "SHOW_PAGESIZE" => true,
        "SHOW_ACTION_PANEL" => true,
        "ALLOW_COLUMNS_SORT" => true,
        "ALLOW_COLUMNS_RESIZE" => true,
        "ALLOW_HORIZONTAL_SCROLL" => true,
        "ALLOW_SORT" => true,
        "ALLOW_PIN_HEADER" => true,
        "AJAX_OPTION_HISTORY" => "N"
    ],
    $component, ["HIDE_ICONS" => "Y"]
);
?>

<script>
  var gridId = '<?=$arResult['GRID_ID']?>';
  var sidePanelParams = <?=json_encode($arResult['SIDE_PANEL_PARAMS']);?>;
</script>
