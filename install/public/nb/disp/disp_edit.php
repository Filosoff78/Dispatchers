<?php
require ($_SERVER['DOCUMENT_ROOT']."/bitrix/header.php");
global $APPLICATION;

$request = Bitrix\Main\Context::getCurrent()->getRequest();

if(isset($_REQUEST["IFRAME"]) && $_REQUEST["IFRAME"] === "Y") {
    $APPLICATION->IncludeComponent(
        'bitrix:ui.sidepanel.wrapper',
        '',
        [
            'POPUP_COMPONENT_NAME' => 'nb:testtask.edit',
            'POPUP_COMPONENT_TEMPLATE_NAME' => '',
            'POPUP_COMPONENT_PARAMS' => [
                'ENTITY_ID' => $request->getQuery('ID')
            ],
            'CLOSE_AFTER_SAVE' => true,
            'RELOAD_GRID_AFTER_SAVE' => true,
            'USE_UI_TOOLBAR' => 'Y'
        ]
    );
} else {
    $APPLICATION->IncludeComponent(
        'nb:testtask.edit',
        '',
        [
            'COMPONENT_TEMPLATE' => '',
            'ENTITY_ID' => $request->getQuery('ID')
        ],
        false
    );
}
require ($_SERVER['DOCUMENT_ROOT']."/bitrix/footer.php");
