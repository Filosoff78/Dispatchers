<?

use Bitrix\Intranet\CustomSection\Entity as Menu;
use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\ORM\Data\DataManager;
use NB\TestTask\ORM\DispatcherTable;

Loc::loadMessages(__FILE__);

class nb_testtask extends CModule
{
    protected $tables = [
        DispatcherTable::class,
        \NB\TestTask\ORM\ObjectTable::class,
    ];

    public function __construct()
    {
        $arModuleVersion = array();

        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_ID = 'nb.testtask';
        $this->MODULE_NAME = Loc::getMessage('NB_TESTTASK_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('NB_TESTTASK_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = '';
        $this->PARTNER_URI = '';
    }

    public function DoInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);

        $this->InstallDB();
        $this->InstallPublicMenu();
        $this->InstallFiles();
        $this->InstallEvents();
    }

    public function DoUninstall()
    {
        $this->UnInstallDB();
        $this->UnInstallPublicMenu();
        $this->UnInstallFiles();
        $this->UnInstallEvents();

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    //region db
    function InstallDB()
    {
        global $APPLICATION;
        Loader::includeModule($this->MODULE_ID);

        try {
            /**
             * @var \Bitrix\Main\ORM\Data\DataManager $table
             */
            foreach ($this->tables as $table) {
                echo $table;
                $entity = $table::getEntity();
                $tableName = $entity->getDBTableName();

                $connection = Application::getConnection();

                if (!$connection->isTableExists($tableName)) {
                    $entity->createDbTable();
                }
            }
            return true;
        } catch (Throwable $e) {
            echo $APPLICATION->ThrowException($e->getMessage());
        }
        return false;
    }

    function UnInstallDB()
    {
        global $APPLICATION;
        Loader::includeModule($this->MODULE_ID);
        try {
            /**
             * @var \Bitrix\Main\ORM\Data\DataManager $table
             */
            foreach ($this->tables as $table) {
                $entity = $table::getEntity();
                $tableName = $entity->getDBTableName();

                $connection = Application::getConnection();

                if ($connection->isTableExists($tableName)) {
                    $connection->dropTable($tableName);
                }
            }
            return true;
        } catch (Throwable $e) {
            $APPLICATION->ThrowException($e->getMessage());
        }
        return false;
    }
    //endregion;

    //region menu
    function InstallPublicMenu()
    {
        global $APPLICATION;

        $arFields = [
            "CODE" => "menu_dispatcher",
            "TITLE" => Loc::getMessage('NB_TESTTASK_MENU'),
            "MODULE_ID" => $this->MODULE_ID
        ];

        $fetchResult = Menu\CustomSectionTable::query()
            ->addSelect('ID')
            ->where('CODE', $arFields['CODE'])
            ->where('MODULE_ID', $arFields['MODULE_ID'])
            ->fetch();

        if (!$fetchResult) {
            $result = Menu\CustomSectionTable::add(['fields' => $arFields]);

            if (!$result->isSuccess()) {
                $errors[] = $result->getErrorMessages();

                $APPLICATION->ThrowException(implode("", $errors));
                return false;
            }
            $sectionId = $result->getId();
        } else {
            $sectionId = $fetchResult['ID'];
        }

        $arFields = [];

        $arFields[] = [
            "CUSTOM_SECTION_ID" => $sectionId,
            "CODE" => "dispatcher_list",
            "TITLE" => Loc::getMessage('NB_TESTTASK_MENU'),
            "MODULE_ID" => $this->MODULE_ID,
            "SETTINGS" => "dispatcher.list",
            "SORT" => 100
        ];

        foreach ($arFields as $arField) {
            $fetchResult = Menu\CustomSectionPageTable::query()
                ->addSelect('ID')
                ->where('CODE', $arField['CODE'])
                ->where('MODULE_ID', $arField['MODULE_ID'])
                ->fetch();
            if (!$fetchResult) {
                Menu\CustomSectionPageTable::add(['fields' => $arField]);
            }
        }
        return true;
    }

    function UnInstallPublicMenu()
    {
        $rows = Menu\CustomSectionPageTable::query()
            ->addSelect('ID')
            ->where('MODULE_ID', $this->MODULE_ID)
            ->exec();
        while ($row = $rows->fetch()) {
            Menu\CustomSectionPageTable::delete($row['ID']);
        }

        $rows = Menu\CustomSectionTable::query()
            ->addSelect('ID')
            ->where('MODULE_ID', $this->MODULE_ID)
            ->exec();
        while ($row = $rows->fetch()) {
            Menu\CustomSectionTable::delete($row['ID']);
        }
        return true;
    }
    //endregion;

    //region files
    function InstallFiles()
    {
        copyDirFiles(
            __DIR__ . '/components',
            Application::getDocumentRoot() . '/local/components',
            true,
            true
        );

        copyDirFiles(
            __DIR__ . '/public',
            Application::getDocumentRoot() . '/',
            true,
            true
        );
    }

    function UnInstallFiles()
    {
        $folders = [
            Application::getDocumentRoot() . '/local/components/nb/dispatcher.list',
            Application::getDocumentRoot() . '/local/components/nb/testtask.edit',
            Application::getDocumentRoot() . '/local/components/nb/testtask.field.object',
            Application::getDocumentRoot() . '/nb/disp/',
        ];

        foreach ($folders as $folder) {
            if (file_exists($folder))
                Bitrix\Main\IO\Directory::deleteDirectory($folder);
        }
        return true;
    }
    //endregion;

    //region events
    function InstallEvents()
    {
        $eventManager = EventManager::getInstance();

        $eventManager->registerEventHandler(
            'main',
            'OnUserTypeBuildList',
            $this->MODULE_ID,
            '\NB\TestTask\UserField\Types\ObjectsTypes',
            'GetUserTypeDescription'
        );

        $eventManager->registerEventHandler(
            'main',
            'OnAfterUserUpdate',
            $this->MODULE_ID,
            '\NB\TestTask\Events\User',
            'OnAfterUserUpdate'
        );
    }

    function UnInstallEvents()
    {
        $eventManager = EventManager::getInstance();

        $eventManager->unRegisterEventHandler(
            'main',
            'OnUserTypeBuildList',
            $this->MODULE_ID,
            '\NB\TestTask\UserField\Types\ObjectsTypes',
            'GetUserTypeDescription'
        );

        $eventManager->unRegisterEventHandler(
            'main',
            'OnAfterUserUpdate',
            $this->MODULE_ID,
            '\NB\TestTask\Events\User',
            'OnAfterUserUpdate'
        );
    }
    //endregion;
}
