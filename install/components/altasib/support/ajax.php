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
define("PUBLIC_AJAX_MODE", true);
define("NO_KEEP_STATISTIC", "Y");
define("NO_AGENT_STATISTIC","Y");
define("NO_AGENT_CHECK", true);
define("DisableEventsCheck", true);
define('EXTRANET_NO_REDIRECT',true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);
use Bitrix\Main;
if(!Main\Loader::includeModule("altasib.support"))
    return;
use ALTASIB\Support;
use ALTASIB\Support\UserTable;

$arParams["ROLE"] = $Role = $APPLICATION->GetUserRight("altasib.support");
if($arParams["ROLE"] == 'D')
    $APPLICATION->AuthForm();

$request = Main\Context::getCurrent()->getRequest();
$arParams['IS_SUPPORT_TEAM'] = ($arParams["ROLE"]=='E' || $arParams["ROLE"]=='W') ? true: false;
$arParams["ID"] = (int)$request['TICKET_ID'];
$arParams["PULL_TAG"] = 'ALTASIB_SUPPORT_'.$arParams["ID"];
$arParams["PULL_TAG_SUPPORT"] = 'ALTASIB_SUPPORT_'.$arParams["ID"].'_SUPPORT';
$arParams["PULL_TAG_SUPPORT_ADMIN"] = 'ALTASIB_SUPPORT_'.$arParams["ID"].'_SUPPORT_ADMIN';

if($arParams["ID"]>0)
{
    $arParams["PULL_TAG_SUPPORT"] = 'ALTASIB_SUPPORT_'.$arParams["ID"].'_SUPPORT';
    if (check_bitrix_sessid() && $request['AJAX_ACTION']=='update-s' && $GLOBALS["USER"]->IsAuthorized() && CModule::IncludeModule("pull") && CPullOptions::GetNginxStatus())
    {
        if(!$arParams['IS_SUPPORT_TEAM'])
        {
            CPullWatch::Add($GLOBALS["USER"]->GetId(), $arParams["PULL_TAG"]);
            CPullWatch::Extend($GLOBALS["USER"]->GetId(), $arParams["PULL_TAG"]);
        }
        else
        {
            $pullParams = array_merge(UserTable::getRow(array('filter'=>array('ID'=>$GLOBALS["USER"]->GetId()),'select'=>array('ID','LOGIN','SHORT_NAME'))),array('TICKET_ID'=>$arParams["ID"]));
            if($arParams['ROLE']=='W')
            {
                CPullWatch::Add($GLOBALS["USER"]->GetId(), $arParams["PULL_TAG_SUPPORT_ADMIN"]);
                CPullWatch::Extend($GLOBALS["USER"]->GetId(), $arParams["PULL_TAG_SUPPORT_ADMIN"]);
            }
            else
            {            
                CPullWatch::Add($GLOBALS["USER"]->GetId(), $arParams["PULL_TAG_SUPPORT"]);
                CPullWatch::Extend($GLOBALS["USER"]->GetId(), $arParams["PULL_TAG_SUPPORT"]);
            }
            
        	CPullWatch::AddToStack($arParams["PULL_TAG_SUPPORT"],
        		Array(
        			'module_id' => 'altasib.support',
        			'command' => 'showview',
        			'params' => $pullParams
        		)
        	);
            
        	CPullWatch::AddToStack($arParams["PULL_TAG_SUPPORT_ADMIN"],
        		Array(
        			'module_id' => 'altasib.support',
        			'command' => 'showview',
        			'params' => $pullParams
        		)
        	);                    
        }
        echo CUtil::PhpToJSObject(array('sessid'=>bitrix_sessid()));
    }
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>