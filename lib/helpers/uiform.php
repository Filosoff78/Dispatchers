<?php

namespace NB\TestTask\Helpers;

use Bitrix\Main\Localization\Loc;

class UiForm
{
    /**
     * Поля для настройки компонента ui.form
     * @return array
     */
    public static function mainUiFormSettingsFields(): array
    {
        return [
            'SUR_SLIDER_WIDTH',
            'SUR_SLIDER_COPY_LINK',
            'SUR_SLIDER_NEW_WINDOW',
            'ENABLE_CONFIG_CONTROL',
            'ENABLE_SECTION_EDIT',
            'ENABLE_SECTION_CREATION',
            'ENABLE_SECTION_DRAG_DROP',
            'ENABLE_FIELDS_CONTEXT_MENU',
            'ENABLE_PERSONAL_CONFIGURATION_UPDATE',
            'ENABLE_COMMON_CONFIGURATION_UPDATE',
            'ENABLE_SETTINGS_FOR_ALL',
            'ENABLE_BOTTOM_PANEL',
            'ENABLE_MODE_TOGGLE',
        ];
    }

    public static function getMainUiFormSettingsFieldsTitle($code)
    {
        return Loc::getMessage($code);
    }
}
