<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\UserField\Renderer;
use NB\TestTask\Helpers;
use NB\TestTask\ORM\DispatcherTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

class EditComponent extends CBitrixComponent implements Controllerable
{
    /**
     * @var array Мапинг полей
     */
    private array $fieldsMap;
    /**
     * @var int ИД сущности
     */
    private int $entityID;
    const MODE_CREATE = 0;
    const MODE_EDIT = 1;
    /**
     * @var int Тип формы
     */
    private int $mode;

    protected function checkModules(): bool
    {
        if (!Loader::includeModule('nb.testtask')) {
            ShowError(Loc::getMessage('MODULE_IS_NOT_INSTALLED'));
            return false;
        }

        return true;
    }

    protected function listKeysSignedParameters(): array
    {
        return [
            'ENTITY_ID',
        ];
    }

    public function onPrepareComponentParams($arParams): array
    {
        if (isset($arParams['ENTITY_ID'])) {
            $this->setEntityId($arParams['ENTITY_ID']);
            $this->mode = self::MODE_EDIT;
        }
        $this->mode = self::MODE_CREATE;
        return parent::onPrepareComponentParams($arParams);
    }

    public function setEntityID($entityID)
    {
        $this->entityID = (int)$entityID;
    }

    public function getEntityEditorData(): array
    {
        return [
            'ENTITY_ID' => $this->getEntityId(),
            'ENTITY_DATA' => $this->prepareEntityData()
        ];
    }

    public function getEntityId()
    {
        return $this->entityID;
    }

    public function executeComponent()
    {
        if (!$this->checkModules()) {
            return;
        }

        /** @global CMain $APPLICATION */
        global $APPLICATION;

        $this->arResult['FORM']['ENTITY_ID'] = isset($this->arParams['~ENTITY_ID']) ? (int)$this->arParams['~ENTITY_ID'] : 0;
        $this->setEntityID($this->arResult['FORM']['ENTITY_ID']);

        $this->prepareFieldsMap();

        $this->initializeData();

        $this->arResult['FORM']['CONTEXT_ID'] = 'GRADE_' . $this->arResult['FORM']['ENTITY_ID'];

        $initMode = $this->request->get('init_mode');
        if (!is_string($initMode)) {
            $initMode = '';
        } else {
            $initMode = mb_strtolower($initMode);
            if ($initMode !== 'edit' && $initMode !== 'view') {
                $initMode = '';
            }
        }
        $this->arResult['FORM']['INITIAL_MODE'] = $initMode !== '' ? $initMode : ($this->entityID > 0 ? 'view' : 'edit');

        //region GUID
        $this->arResult['FORM']['GUID'] = $this->arParams['GUID'] ?? "grade_{$this->entityID}_details";
        $this->guid = $this->arResult['FORM']['GUID'];

        $this->arResult['FORM']['CONFIG_ID'] = $this->arParams['CONFIG_ID'] ?? $this->getDefaultConfigID();
        //endregion

        //region Fields
        $this->arResult['FORM']['ENTITY_FIELDS'] = $this->prepareFieldInfos();
        //endregion

        //region Config
        $this->prepareConfiguration();
        //endregion

        //region Настройки карточки
        $arMainCardItemModeParams = Helpers\UiForm::mainUiFormSettingsFields();
        foreach ($arMainCardItemModeParams as $optionCode) {
            $this->arResult['FORM'][$optionCode] = 'Y';
        }
        //endregion

        $this->arResult['FORM']['COMPONENT_AJAX_DATA'] = [
            'COMPONENT_NAME' => $this->getName(),
            'ACTION_NAME' => 'save',
            'SIGNED_PARAMETERS' => $this->getSignedParameters(),
        ];
        $this->arResult['FORM']['ENTITY_ID'] = $this->arResult['FORM']['INITIAL_MODE'] == 'view'
            ? $this->arResult['FORM']['ENTITY_ID']
            : 0;

        //region Page title
        if ($this->arResult['FORM']['ENTITY_DATA']['NAME']) {
            $APPLICATION->SetTitle($this->arResult['FORM']['ENTITY_DATA']['NAME']);
        } else {
            $APPLICATION->SetTitle(Loc::getMessage("COMPONENT_TITLE"));
        }
        //endregion
        $this->includeComponentTemplate();
    }

    public function prepareFieldsMap()
    {
        if (!isset($this->fieldsMap)) {
            $this->fieldsMap = DispatcherTable::getFieldsInfo();
        }
    }

    public function prepareFieldInfos(): array
    {
        if (isset($this->entityFieldInfos)) {
            return $this->entityFieldInfos;
        }

        $this->entityFieldInfos = [];

        foreach ($this->fieldsMap as $field) {
            if ($field['IS_PRIMARY']) {
                continue;
            }

            if ($this->mode === self::MODE_CREATE && $field['ONLY_EDIT']) {
                continue;
            }

            if ($field['HIDE_IN_FORM']) {
                continue;
            }

            $fieldItem = [
                'name' => $field['NAME'],
                'title' => $field['TITLE'],
                'required' => $field['IS_REQUIRED'],
                'defaultValue' => $field['DEFAULT_VALUE'],
                'editable' => true,
                'type' => self::convertFieldTypeForFrom($field),
                'settings' => [],
                'data' => [],
            ];

            if ($field['NAME'] == 'OBJECT_ID') {
                $fieldItem['data'] = [
                    'view' => $field['NAME'] . '[VIEW_HTML]',
                    'edit' => $field['NAME'] . '[EDIT_HTML]',
                ];
                $fieldItem['type'] = 'custom';
            }

            $this->entityFieldInfos[] = $fieldItem;
        }

        return $this->entityFieldInfos;
    }

    public function prepareConfiguration()
    {
        if (isset($this->arResult['FORM']['ENTITY_CONFIG'])) {
            return $this->arResult['FORM']['ENTITY_CONFIG'];
        }

        $section = [
            "name" => "main",
            "type" => "section",
            "title" => "",
            "elements" => []
        ];
        foreach ($this->fieldsMap as $field) {
            if ($field['IS_PRIMARY']) {
                continue;
            }

            $configItem = [
                "name" => $field['NAME']
            ];
            $section["elements"][] = $configItem;
        }
        $this->arResult['FORM']['ENTITY_CONFIG'][] = $section;
        return $this->arResult['FORM']['ENTITY_CONFIG'];
    }

    public function prepareEntityData()
    {
        if ($this->entityData) {
            return $this->entityData;
        }

        $this->entityData = DispatcherTable::query()
            ->addSelect('*')
            ->where('ID', $this->entityID)
            ->fetch();

        $entityData = [];
        foreach ($this->fieldsMap as $key => $field) {
            if ($field['IS_PRIMARY']) {
                continue;
            }

            if ($key === 'ACTIVE') {
                $entityData[$key] = $this->entityData[$key] == '1' ? 'Y' : '';
            } elseif ($key === 'ACTIVE_END') {
                $entityData[$key] = $this->entityData[$key] instanceof \Bitrix\Main\Type\DateTime ? $this->entityData[$key]->toString(
                ) : '';
            } elseif ($key === 'OBJECT_ID') {
                $userField = [
                    'USER_TYPE_ID' => 'st_objects',
                    'FIELD_NAME' => $key,
                    'FIELD_FORM_NAME' => $key,
                    'VALUE' => $this->entityData[$key] ?? null,
                    'MULTIPLE' => 'N',
                ];

                $entityData[$key . '[VIEW_HTML]'] = (new Renderer($userField, ['mode' => 'main.view']))->render();
                $entityData[$key . '[EDIT_HTML]'] = (new Renderer($userField, ['mode' => 'main.edit']))->render();
            } else {
                $entityData[$key] = $this->entityData[$key] ?? null;
            }
        }

        return ($this->arResult['FORM']['ENTITY_DATA'] = $entityData);
    }

    public function getDefaultConfigID(): string
    {
        return 'disp_details';
    }

    protected static function convertFieldTypeForFrom(array $field): string
    {
        switch ($field['DATA_TYPE']) {
            case 'integer':
                return 'number';
            case 'text':
                return 'textarea';
            case 'datetime':
                return 'datetime';
            case 'boolean':
                return 'boolean';
            default:
                return 'text';
        }
    }

    public function initializeData()
    {
        $this->prepareEntityData();
        $this->prepareFieldInfos();
    }

    public function configureActions(): array
    {
        return [];
    }

    public function saveAction(): ?array
    {
        $data = $this->request->get('data') ?: [];

        if (empty($data) || !$this->checkModules()) {
            return null;
        }

        $ID = $this->getEntityId();
        $isNew = empty($ID);

        $fields = [];

        foreach (DispatcherTable::getFieldsInfo() as $field) {
            if ($field['IS_PRIMARY']) {
                continue;
            }

            if (is_null($data[$field['NAME']])) {
                continue;
            }

            if ($field['DATA_TYPE'] === 'datetime') {
                $fields[$field['NAME']] = new \Bitrix\Main\Type\DateTime($data[$field['NAME']]);
            } else {
                if ($field['DATA_TYPE'] === 'boolean') {
                    $fields[$field['NAME']] = $data[$field['NAME']] === 'Y' ? 1 : 0;
                } else {
                    $fields[$field['NAME']] = is_array(
                        $data[$field['NAME']]
                    ) ? $data[$field['NAME']][0] : $data[$field['NAME']];
                }
            }
        }

        if (empty($fields)) {
            return null;
        }

        if ($isNew) {
            $result = DispatcherTable::add(['fields' => $fields]);
        } else {
            $result = DispatcherTable::update($ID, $fields);
        }

        if (!$result->isSuccess()) {
            return ['ERROR' => $result->getErrorMessages()];
        }

        $this->setEntityID($result->getId());
        $this->prepareFieldsMap();

        return $this->getEntityEditorData();
    }
}
