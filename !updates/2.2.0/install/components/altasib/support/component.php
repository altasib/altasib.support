<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Evgenió Pedan                    #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2016 ALTASIB             #
#################################################
?>
<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!isset($arParams["CACHE_TIME"]))
        $arParams["CACHE_TIME"] = 3600;

use ALTASIB\Support;
use Bitrix\Main;

if(!Main\Loader::includeModule("altasib.support"))
{
    ShowError("ALTASIB_SUPPORT_MODULE_NOT_INSTALL");
    return;
}

if(!$USER->IsAuthorized())
        $APPLICATION->AuthForm('');

$arDefaultUrlTemplates404 = array(
    "ticket_edit"=>"ticket/#ID#/",
    "ticket_list" => "",
    "get_file"=>"ticket/#ID#/file/#FILE_HASH#/",
    'desktop'=>'desktop/',
);

$arDefaultVariableAliases404 = array();
$arDefaultVariableAliases = array("ID"=>"ID","FILE_HASH"=>"FILE_HASH",'TICKET_ID'=>'ID');
$arComponentVariables = array("ID","FILE_HASH",'TICKET_ID');

if($arParams["SEF_MODE"] == "Y")
{
        $arVariables = array();

        $arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams["SEF_URL_TEMPLATES"]);
        $arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases404, $arParams["VARIABLE_ALIASES"]);

        $componentPage = CComponentEngine::ParseComponentPath(
                $arParams["SEF_FOLDER"],
                $arUrlTemplates,
                $arVariables
        );

        if(!$componentPage)
            $componentPage = "ticket_list";

        CComponentEngine::InitComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

        $arResult = array(
                "FOLDER" => $arParams["SEF_FOLDER"],
                "URL_TEMPLATES" => $arUrlTemplates,
                "VARIABLES" => $arVariables,
                "ALIASES" => $arVariableAliases,
        );
}
else
{
        $arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases, $arParams["VARIABLE_ALIASES"]);
        CComponentEngine::InitComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);

        $componentPage = "";
        if(isset($arVariables["ID"]) && intval($arVariables["ID"]) > 0 && isset($arVariables["FILE_HASH"]) && strlen($arVariables["FILE_HASH"]) > 0)
        {
            $componentPage = 'get_file';
        }
        elseif(isset($arVariables["ID"]) && intval($arVariables["ID"]) >= 0)
        {
            $componentPage = "ticket_edit";
        }
        else
                $componentPage = "ticket_list";
        
        if(isset($_REQUEST['desktop']) && $arParams['IS_SUPPORT_TEAM'])
            $componentPage = 'desktop';

        $arResult = array(
                "FOLDER" => "",
                "URL_TEMPLATES" => Array(
                        "ticket_edit" => htmlspecialchars($APPLICATION->GetCurPage())."?".$arVariableAliases["ID"]."=#ID#",
                        "ticket_list" => htmlspecialchars($APPLICATION->GetCurPage()),
                        "get_file" => htmlspecialchars($APPLICATION->GetCurPage())."?".$arVariableAliases["ID"]."=#ID#&FILE_HASH=#FILE_HASH#",
                        "desktop" => htmlspecialchars($APPLICATION->GetCurPage())."?desktop=y",
                ),
                "VARIABLES" => $arVariables,
                "ALIASES" => $arVariableAliases
        );
}
$arParams["ID"] = (int)$arVariables["ID"];
$arParams["FILE_HASH"] = trim($arVariables["FILE_HASH"]);
$arParams['URL_LIST'] = $arResult['FOLDER'].$arResult['URL_TEMPLATES']['ticket_list'];
$arParams['URL_DETAIL'] = $arResult['FOLDER'].$arResult['URL_TEMPLATES']['ticket_edit'];
$arParams['URL_GET_FILE'] = $arResult['FOLDER'].$arResult['URL_TEMPLATES']['get_file'];
$arParams['URL_DESKTOP'] = $arResult['FOLDER'].$arResult['URL_TEMPLATES']['desktop'];

$arParams['Right'] = new ALTASIB\Support\Rights($USER->getId(),$arParams['ID']);
$arParams["ROLE"] = $Role = $arParams['Right']->getRole();
if($arParams["ROLE"] == 'D')
    $APPLICATION->AuthForm();

$arParams['IS_SUPPORT_TEAM'] = $arParams['Right']->isSupportTeam();
$arParams["FULL_RIGHT"] = ($Role == "W");
$arParams["CATEGORY_ID"] = (int)$arParams["CATEGORY_ID"];

if($componentPage == 'ticket_edit' || $componentPage == 'get_file')
{
    if($Role == "E" || $Role == "W")
    {
        $arParams["HAVE_CHANGE_STATUS"] = false;
        $arParams["HAVE_CHANGE_RESPONSIBLE"] = false;
        $arParams["HAVE_CHANGE_ASSISTANTS"] = false;
        $arParams["HAVE_CHANGE_CATEGORY"] = false;
        $arParams["HAVE_CHANGE_PRIORITY"] = false;
    }
    $arParams["HAVE_ANSWER"] = false;
    $arParams["HAVE_CREATE"] = false;
    if($arParams["ID"]>0)
    {
        if($arParams['Right']->getRight() === false)
        {
            ShowError(GetMessage('ALTASIB_SUPPORT_CMP_TICKET_NOT_FOUND'));
            return false;                    
        }
        $arParams["HAVE_CHANGE_STATUS"] = $arParams['Right']->allow('CHANGE_STATUS');
        $arParams["HAVE_CHANGE_RESPONSIBLE"] = $arParams['Right']->allow('CHANGE_RESPONSIBLE');
        $arParams["HAVE_CHANGE_ASSISTANTS"] = $arParams['Right']->allow('CHANGE_ASSISTANTS');
        $arParams["HAVE_CHANGE_CATEGORY"] = $arParams['Right']->allow('CHANGE_CATEGORY');
        $arParams["HAVE_CHANGE_PRIORITY"] = $arParams['Right']->allow('CHANGE_PRIORITY');
        $arParams["HAVE_ANSWER"] = $arParams['Right']->allow('ANSWER');                    
    }
    else
    {
        $arParams['isWorker'] = false;
        $c2cw = Support\C2CWTable::GetList(
            array(
                'filter' => array('WORKER_USER_ID'=>$USER->GetId())
            )
        );
        while($arClienWorker = $c2cw->fetch())
        {
            $arParams['isWorker'] = true;
            if($arClienWorker['R_CREATE'] == 'Y')
            {
                $arParams['HAVE_CREATE_TO_CAREGORY'][] = $arClienWorker['CATEGORY_ID'];
                $arParams["HAVE_CREATE"] = true;
            }
        }
        
        if(!$arParams['isWorker'])
        {
            $arParams["HAVE_CREATE"] = true;
        }
        elseif(empty($arParams['HAVE_CREATE_TO_CAREGORY']))
        {
            ShowError(GetMessage('ALTASIB_SUPPORT_CMP_CREATE_ERROR'));
            return false;
        }
               
    }                
}
$this->IncludeComponentTemplate($componentPage);
$APPLICATION->AddHeadString('<script type="text/javascript">
    new_href = window.location.href;
    var hashpos = new_href.indexOf(\'#\'), hash = \'\';
    if (hashpos != -1)
    {
            hash = new_href.substr(hashpos);
            new_href = new_href.substr(0, hashpos);
    }
    var supportVar = {
        TICKET_ID : '.intval($arParams["ID"]).',
        bsmsessid : \''.bitrix_sessid().'\',
        CURRENT_URL : new_href,
        extranet : '.var_export(\Bitrix\Main\ModuleManager::isModuleInstalled('intranet'),true).'
    };
</script>');
?>