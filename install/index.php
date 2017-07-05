<?
/**
 * Created by PhpStorm
 * User: Uriy Smirnov
 * p-w-d.ru
 * @ Pride Web Development
 */

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\IO\InvalidPathException;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\SystemException;

Loc::loadMessages(__FILE__);

if (class_exists('pwd_offerchanger')) {
    return;
}


Class pwd_offerchanger extends CModule
{
    var $exclusionAdminFiles;
    var $hlName = 'OfferChanger';
    var $hlTableName = 'offer_changer';
    var $langs = array(
        'ru' => 'HL Подмена офферов',
        'en' => 'HL Offer Changer',
    );

    private $arFields = array(
        'UF_PARAMETER' => array(
            'MANDATORY' => 'Y',
            'USER_TYPE_ID' => 'string',
            'EDIT_FORM_LABEL' => [
                'ru' => 'Значение параметра, инициализирующего замену',
            ],
            'LIST_COLUMN_LABEL' => [
                'ru' => 'Значение параметра, инициализирующего замену',
            ],
            'SETTINGS' => array(),
        ),
        'UF_BLOCK_ID' => array(
            'MANDATORY' => 'Y',
            'USER_TYPE_ID' => 'string',
            'EDIT_FORM_LABEL' => [
                'ru' => 'ID блока',
            ],
            'LIST_COLUMN_LABEL' => [
                'ru' => 'ID блока',
            ],

            'SETTINGS' => array(
                'SIZE' => '60'
            ),
        ),
        'UF_BLOCK_TEXT' => array(
            'MANDATORY' => 'N',
            'USER_TYPE_ID' => 'string',
            'EDIT_FORM_LABEL' => [
                'ru' => 'Текст блока',
            ],
            'LIST_COLUMN_LABEL' => [
                'ru' => 'Текст блока',
            ],

            'SETTINGS' => array(
                'SIZE' => '60',
                'ROWS' => '3',
            ),
        ),
        'UF_BANNER' => array(
            'MANDATORY' => 'N',
            'USER_TYPE_ID' => 'file',
            'EDIT_FORM_LABEL' => [
                'ru' => 'Баннер',
            ],
            'LIST_COLUMN_LABEL' => [
                'ru' => 'Баннер',
            ],
            'SETTINGS' => array(
                'LIST_WIDTH' => '150',
                'LIST_HEIGHT' => '150',
            ),
        ),

    );

    protected $eventHandlers = array();


    function __construct()
    {
        $arModuleVersion = array();
        include(__DIR__ . "/version.php");

        $this->MODULE_ID = 'pwd.offerchanger';
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage("PWD_OFFER_CHANGER_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("PWD_OFFER_CHANGER_MODULE_DESC");

        $this->PARTNER_NAME = Loc::getMessage("PWD_OFFER_CHANGER_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("PWD_OFFER_CHANGER_PARTNER_URI");

        $this->MODULE_SORT = 1;
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';
        $this->MODULE_GROUP_RIGHTS = "Y";

        $this->eventHandlers = array(
            array(
                'main',
                'OnPageStart',
                '\Pwd\Offerchanger\Module',
                'onPageStart',
            ),

        );
    }


    //Определяем место размещения модуля
    public function GetPath($notDocumentRoot = false)
    {
        if ($notDocumentRoot) {
            return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
        } else {
            return dirname(__DIR__);
        }
    }

    //Проверяем что система поддерживает D7
    public function isVersionD7()
    {
        return CheckVersion(ModuleManager::getVersion('main'), '14.00.00');
    }


    /**
     * Создание справочника замен
     *
     * @throws SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function createHL()
    {
        if (Loader::includeModule('highloadblock')) {
            $arHlBlock = HighloadBlockTable::getList(array(
                'filter' => array(
                    'TABLE_NAME' => $this->hlTableName,
                )
            ))->fetch();

            if (empty($arHlBlock)) {
                $data = array(
                    'NAME' => $this->hlName,
                    'TABLE_NAME' => $this->hlTableName,
                );

                $result = HighloadBlockTable::add($data);

                if ($result->isSuccess()) {
                    $hlID = $result->getId();

                    /*HighloadBlockTable::update(
                        $hlID,
                        array(
                            'LANGS' => $this->langs,
                        )
                    );*/


                } else {
                    throw new SystemException(Loc::getMessage('HIGHLOADBLOCK_ADDED_INFO_ERROR', array(
                        '#NAME#' => $this->hlName,
                        '#ERROR#' => $result->getErrorMessages(),
                    )));
                }
            } else {
                $hlID = $arHlBlock['ID'];
            }

            if (!empty($hlID)) {
                # Создаем пользовательские поля справочника, если не созданы ранее
                $rsProps = CUserTypeEntity::GetList(array(), array(
                    'ENTITY_ID' => 'HLBLOCK_' . $hlID,
                    'FIELD_NAME' => array_keys($this->arFields)
                ));
                while ($arProp = $rsProps->GetNext()) {
                    unset($this->arFields[$arProp['FIELD_NAME']]);
                }

                $obUField = new CUserTypeEntity();
                foreach ($this->arFields as $fieldCode => $arField) {
                    $arFieldProps = array(
                        'ENTITY_ID' => 'HLBLOCK_' . $hlID,
                        'FIELD_NAME' => $fieldCode,
                        'USER_TYPE_ID' => $arField['USER_TYPE_ID'],
                        'SORT' => 500,
                        'MULTIPLE' => 'N',
                        'MANDATORY' => $arField['MANDATORY'],
                        'SHOW_FILTER' => 'N',
                        'SHOW_IN_LIST' => 'Y',
                        'EDIT_IN_LIST' => 'Y',
                        'IS_SEARCHABLE' => 'N',
                        'SETTINGS' => $arField['SETTINGS'],
                        'EDIT_FORM_LABEL' => $arField['EDIT_FORM_LABEL'],
                        'LIST_COLUMN_LABEL' => $arField['LIST_COLUMN_LABEL'],
                    );

                    $fieldId = $obUField->Add($arFieldProps);

                    if (empty($fieldId)) {
                        throw new SystemException(Loc::getMessage('HIGHLOADBLOCK_ADDING_INFO_ERROR', array(
                            '#NAME#' => $this->hlName,
                            '#ERROR#' => $result->getErrorMessages(),
                        )));
                    }
                }
            }
        }
    }


    public function removeHL()
    {
        if (Loader::includeModule('highloadblock')) {
            $arHlBlock = HighloadBlockTable::getList(array(
                    'filter' => array(
                        'TABLE_NAME' => $this->hlTableName,
                    ))
            )->fetch();

            if (!empty($arHlBlock['ID'])) {
                $res = HighloadBlockTable::delete($arHlBlock['ID']);

                if (!$res->isSuccess()) {
                    throw new SystemException(Loc::getMessage('HIGHLOADBLOCK_DELETING_INFO_ERROR', array(
                        '#NAME#' => $this->hlName,
                        '#ERROR#' => $res->getErrorMessages(),
                    )));
                }
            }

            if (!empty($arHlBlock['ID'])) {
                # Удаляем пользовательские поля справочника
                $rsProps = CUserTypeEntity::GetList(
                    array(),
                    array(
                        'ENTITY_ID' => 'HLBLOCK_' . $arHlBlock['ID'],
                        'FIELD_NAME' => array_keys($this->arFields)
                    )
                );
                while ($arProp = $rsProps->GetNext()) {
                    $obUField = new CUserTypeEntity();
                    $obUField->Delete($arProp['ID']);
                }
            }
        }
    }

    function InstallDB()
    {
        # Создаем справочник
        $this->createHL();
    }

    function UnInstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        # Удаляем справочник
        $this->removeHL();
    }

    function InstallEvents()
    {

        $eventManager = \Bitrix\Main\EventManager::getInstance();

        foreach ($this->eventHandlers as $handler) {

            $eventManager->registerEventHandler(
                $handler[0],
                $handler[1],
                $this->MODULE_ID,
                $handler[2],
                $handler[3]
            );

        }

        return true;
    }

    function UnInstallEvents()
    {

        $eventManager = \Bitrix\Main\EventManager::getInstance();

        foreach ($this->eventHandlers as $handler) {
            $eventManager->unRegisterEventHandler(
                $handler[0],
                $handler[1],
                $this->MODULE_ID,
                $handler[2],
                $handler[3]
            );

        }

        return true;

    }

    function InstallFiles($arParams = array())
    {
        $path = $this->GetPath() . "/install/components";

        if (Directory::isDirectoryExists($path)) {
            CopyDirFiles($path, $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components", true, true);
        } else {
            throw new InvalidPathException($path);
        }

        return true;
    }

    function UnInstallFiles()
    {
        Directory::deleteDirectory($_SERVER["DOCUMENT_ROOT"] . '/bitrix/components/pwd/offer.changer/');

        return true;
    }


    /**
     * Установка модуля
     * @throws InvalidPathException
     */
    function DoInstall()
    {
        global $APPLICATION;
        if ($this->isVersionD7()) {
            $this->InstallDB();
            $this->InstallEvents();
            $this->InstallFiles();

            ModuleManager::registerModule($this->MODULE_ID);
        } else {
            $APPLICATION->ThrowException(Loc::getMessage("PWD_OFFER_CHANGER_INSTALL_ERROR_VERSION"));
        }

        $APPLICATION->IncludeAdminFile(Loc::getMessage("PWD_OFFER_CHANGER_INSTALL_TITLE"), $this->GetPath() . "/install/step.php");
    }


    /**
     * Удаление модуля
     */
    function DoUninstall()
    {
        global $APPLICATION;

        $obContext = Application::getInstance()->getContext();
        $arRequest = $obContext->getRequest();

        if ($arRequest["step"] < 2) {
            $APPLICATION->IncludeAdminFile(Loc::getMessage("PWD_OFFER_CHANGER_UNINSTALL_TITLE"), $this->GetPath() . "/install/unstep1.php");
        } elseif ($arRequest["step"] == 2) {
            $this->UnInstallFiles();
            $this->UnInstallEvents();

            if ($arRequest["savedata"] != "Y") {
                $this->UnInstallDB();
            }

            ModuleManager::unRegisterModule($this->MODULE_ID);
            $APPLICATION->IncludeAdminFile(Loc::getMessage("PWD_OFFER_CHANGER_UNINSTALL_TITLE"), $this->GetPath() . "/install/unstep2.php");
        }
    }
}