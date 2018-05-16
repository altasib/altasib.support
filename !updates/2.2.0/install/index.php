<?php
/**
 * ALTASIB
 * @site http://www.altasib.ru
 * @email dev@altasib.ru
 * @copyright 2006-2018 ALTASIB
 */

global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang) - 18);
@include(GetLangFileName($strPath2Lang . "/lang/", "/install/index.php"));
IncludeModuleLangFile($strPath2Lang . "/install/index.php");

use ALTASIB\Support as S;
use Bitrix\Main;

Class altasib_support extends CModule
{
    var $MODULE_ID = "altasib.support";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    public $MODULE_GROUP_RIGHTS = 'Y';

    Function altasib_support()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path . "/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        } else {
            $this->MODULE_VERSION = '1.0.0';
            $this->MODULE_VERSION_DATE = '14.10.2014';
        }

        $this->MODULE_NAME = GetMessage("ALTASIB_SUPPORT_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("ALTASIB_SUPPORT_MODULE_DESC");
        $this->PARTNER_NAME = "ALTASIB";
        $this->PARTNER_URI = "http://www.altasib.ru/";

    }

    function InstallDB()
    {
        global $DB, $DBType, $APPLICATION, $step;

        if (!$DB->Query("SELECT 'x' FROM altasib_support_ticket", true)) {
            $errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/altasib.support/install/db/" . $DBType . "/install.sql");
        }

        if (!empty($errors)) {
            $APPLICATION->ThrowException(implode("", $errors));
            return false;
        }
        RegisterModule("altasib.support");
        Main\Loader::includeModule("altasib.support");
        $group = new CGroup;
        $EMP_ID = $group->Add(Array(
            'ACTIVE' => 'Y',
            'NAME' => GetMessage('ALTASIB_SUPPORT_GROUP_EMPLOYEE'),
            'STRING_ID' => 'ALTASIB_SUPPORT_EMP'
        ));
        $ADM_ID = $group->Add(Array(
            'ACTIVE' => 'Y',
            'NAME' => GetMessage('ALTASIB_SUPPORT_GROUP_ADMIN'),
            'STRING_ID' => 'ALTASIB_SUPPORT_ADM'
        ));
        $APPLICATION->SetGroupRight('altasib.support', 3, 'C');
        $APPLICATION->SetGroupRight('altasib.support', $EMP_ID, 'E');
        $APPLICATION->SetGroupRight('altasib.support', $ADM_ID, 'W');
        //category
        S\CategoryTable::add(array(
                'NAME' => GetMessage('ALTASIB_SUPPORT_CAREGORY'),
                'RESPONSIBLE_USER_ID' => 1,
                'USE_DEFAULT' => 'Y'
            )
        );

        //status
        for ($i = 1; $i <= 5; $i++) {
            S\StatusTable::add(array(
                    'NAME' => GetMessage('ALTASIB_SUPPORT_STATUS_' . $i),
                    'SORT' => $i * 100
                )
            );
        }

        //sla
        //category
        S\SlaTable::add(array(
            "NAME" => GetMessage('ALTASIB_SUPPORT_INSTALL_SLA'),
            "RESPONSE_TIME" => 72,
            "NOTICE_TIME" => 36,
        ));

        CAgent::AddAgent("ALTASIB\Support\TicketTable::autoClose();", "altasib.support", "Y", 86400,
            date('d.m.Y H:i:s'), "Y", date('d.m.Y H:i:s'));
        CAgent::AddAgent("ALTASIB\Support\TicketTable::checkOverdue();", "altasib.support", "Y", 3600,
            date('d.m.Y H:i:s'), "Y", date('d.m.Y H:i:s'));
        CAgent::AddAgent("ALTASIB\Support\Event::expiredSend();", "altasib.support", "Y", 3600, date('d.m.Y H:i:s'),
            "Y", date('d.m.Y H:i:s'));

        RegisterModuleDependences("pull", "OnGetDependentModule", "altasib.support", "ALTASIB\Support\Tools",
            "getPullSchema");

        RegisterModuleDependences("socialnetwork", "OnFillSocNetFeaturesList", "altasib.support",
            "ALTASIB\Support\Tools", "__AddSocNetFeature");
        RegisterModuleDependences("socialnetwork", "OnFillSocNetMenu", "altasib.support", "ALTASIB\Support\Tools",
            "__AddSocNetMenu");
        RegisterModuleDependences("socialnetwork", "OnParseSocNetComponentPath", "altasib.support",
            "ALTASIB\Support\Tools", "__OnParseSocNetComponentPath");
        RegisterModuleDependences("socialnetwork", "OnInitSocNetComponentVariables", "altasib.support",
            "ALTASIB\Support\Tools", "__OnInitSocNetComponentVariables");
        RegisterModuleDependences("im", "OnGetNotifySchema", "altasib.support", "ALTASIB\Support\Tools",
            "getNotifySchema");
        RegisterModuleDependences("im", "OnAnswerNotify", "altasib.support", "ALTASIB\Support\Tools",
            "OnAnswerNotifyCallBack");
        return true;
    }

    function UnInstallDB($arParams)
    {
        global $DB, $DBType, $APPLICATION;

        if (array_key_exists("savedata", $arParams) && $arParams["savedata"] != "Y") {
            $errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/altasib.support/install/db/" . $DBType . "/uninstall.sql");
            if (!empty($errors)) {
                $APPLICATION->ThrowException(implode("", $errors));
                return false;
            }
        }
        CAgent::RemoveModuleAgents("altasib.support");
        UnRegisterModuleDependences("pull", "OnGetDependentModule", "altasib.support", "ALTASIB\Support\Tools",
            "getPullSchema");
        UnRegisterModuleDependences("socialnetwork", "OnFillSocNetFeaturesList", "altasib.support",
            "ALTASIB\Support\Tools", "__AddSocNetFeature");
        UnRegisterModuleDependences("socialnetwork", "OnFillSocNetMenu", "altasib.support", "ALTASIB\Support\Tools",
            "__AddSocNetMenu");
        UnRegisterModuleDependences("socialnetwork", "OnParseSocNetComponentPath", "altasib.support",
            "ALTASIB\Support\Tools", "__OnParseSocNetComponentPath");
        UnRegisterModuleDependences("socialnetwork", "OnInitSocNetComponentVariables", "altasib.support",
            "ALTASIB\Support\Tools", "__OnInitSocNetComponentVariables");
        UnRegisterModuleDependences("im", "OnGetNotifySchema", "altasib.support", "ALTASIB\Support\Tools",
            "getNotifySchema");
        UnRegisterModuleDependences("im", "OnAnswerNotify", "altasib.support", "ALTASIB\Support\Tools",
            "OnAnswerNotifyCallBack");

        UnRegisterModule("altasib.support");
        return true;
    }

    function InstallEvents()
    {
        include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/altasib.support/install/events/install.php");
        return true;
    }

    function UnInstallEvents()
    {
        include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/altasib.support/install/events/uninstall.php");
        return true;
    }

    function InstallFiles($params = array())
    {

        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/altasib.support/install/admin",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/altasib.support/install/components",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/altasib.support/install/js",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/js", true, true);

        $rsSite = CSite::GetList(($by = 'sort'), ($order = 'asc'));
        while ($arSite = $rsSite->Fetch()) {
            if ($params['install_public'][$arSite['LID']] == 'Y') {
                CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/altasib.support/install/public',
                    $arSite['ABS_DOC_ROOT'] . $arSite['DIR'] . $params['install_dir'][$arSite['LID']], true, true);

                CUrlRewriter::Add(array(
                    "CONDITION" => "#^" . $arSite['DIR'] . $params['install_dir'][$arSite['LID']] . "/#",
                    "RULE" => "",
                    "ID" => "altasib:support",
                    "PATH" => $arSite['DIR'] . $params['install_dir'][$arSite['LID']] . "/index.php",
                ));
            }
        }
        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/altasib.support/install/admin",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin");
        DeleteDirFilesEx("/bitrix/js/altasib.support");
        DeleteDirFilesEx("/bitrix/components/altasib/support");
        DeleteDirFilesEx("/bitrix/components/altasib/support.ticket.detail");
        DeleteDirFilesEx("/bitrix/components/altasib/support.ticket.form");
        DeleteDirFilesEx("/bitrix/components/altasib/support.ticket.list");
        DeleteDirFilesEx("/bitrix/components/altasib/support.desktop");
        DeleteDirFilesEx("/bitrix/components/altasib/support.ticket.detail.info");
        return true;
    }

    function DoInstall()
    {
        global $DB, $DOCUMENT_ROOT, $APPLICATION, $step;
        $step = (int)$step;
        if ($step < 2) {
            $APPLICATION->IncludeAdminFile(GetMessage("ALTASIB_SUPPORT_INSTALL_TITLE"),
                $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/altasib.support/install/step1.php");
        } elseif ($step == 2) {
            if ($this->InstallDB()) {
                $this->InstallEvents();
                $this->InstallFiles(array(
                    'install_public' => $_REQUEST['install_public'],
                    'install_dir' => $_REQUEST['install_dir'],
                ));
            }
            $GLOBALS["errors"] = $this->errors;
            $APPLICATION->IncludeAdminFile(GetMessage("ALTASIB_SUPPORT_INSTALL_TITLE"),
                $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/altasib.support/install/step2.php");
        }
    }

    function DoUninstall()
    {
        global $DB, $APPLICATION, $step;
        $step = IntVal($step);
        if ($step < 2) {
            $APPLICATION->IncludeAdminFile(GetMessage("ALTASIB_SUPPORT_UNINSTALL_TITLE"),
                $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/altasib.support/install/unstep1.php");
        } elseif ($step == 2) {

            $this->UnInstallFiles();
            if ($_REQUEST["saveemails"] != "Y") {
                $this->UnInstallEvents();
            }

            $this->UnInstallDB(array(
                "savedata" => $_REQUEST["savedata"],
            ));

            $this->UnInstallFiles();

            $GLOBALS["errors"] = $this->errors;

            $APPLICATION->IncludeAdminFile(GetMessage("ALTASIB_SUPPORT_UNINSTALL_TITLE"),
                $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/altasib.support/install/unstep2.php");
        }
    }

    function GetModuleRightList()
    {
        $arr = array(
            "reference_id" => array("D", "C", "E", "W"),
            "reference" => array(
                "[D] " . GetMessage("ALTASIB_SUPPORT_DENIED"),// Access denied
                "[C] " . GetMessage("ALTASIB_SUPPORT_CLIENT"), // client
                "[E] " . GetMessage("ALTASIB_SUPPORT_EMPLOYEE"), // employee
                "[W] " . GetMessage("ALTASIB_SUPPORT_ADMIN")// admin
            )
        );
        return $arr;
    }
}