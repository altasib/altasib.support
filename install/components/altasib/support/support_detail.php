<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Evgenió Pedan                    #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2016 ALTASIB             #
#################################################
?>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$APPLICATION->IncludeComponent(
   "bitrix:socialnetwork.group_menu",
   "",
   Array(
      "GROUP_VAR" => $arResult["ALIASES"]["group_id"],
      "PAGE_VAR" => $arResult["ALIASES"]["page"],
      "PATH_TO_GROUP" => $arResult["PATH_TO_GROUP"],
      "PATH_TO_GROUP_MODS" => $arResult["PATH_TO_GROUP_MODS"],
      "PATH_TO_GROUP_USERS" => $arResult["PATH_TO_GROUP_USERS"],
      "PATH_TO_GROUP_EDIT" => $arResult["PATH_TO_GROUP_EDIT"],
      "PATH_TO_GROUP_REQUEST_SEARCH" => $arResult["PATH_TO_GROUP_REQUEST_SEARCH"],
      "PATH_TO_GROUP_REQUESTS" => $arResult["PATH_TO_GROUP_REQUESTS"],
      "PATH_TO_GROUP_REQUESTS_OUT" => $arResult["PATH_TO_GROUP_REQUESTS_OUT"],
      "PATH_TO_GROUP_BAN" => $arResult["PATH_TO_GROUP_BAN"],
      "PATH_TO_GROUP_BLOG" => $arResult["PATH_TO_GROUP_BLOG"],
      "PATH_TO_GROUP_PHOTO" => $arResult["PATH_TO_GROUP_PHOTO"],
      "PATH_TO_GROUP_FORUM" => $arResult["PATH_TO_GROUP_FORUM"],
      "PATH_TO_GROUP_CALENDAR" => $arResult["PATH_TO_GROUP_CALENDAR"],
      "PATH_TO_GROUP_FILES" => $arResult["PATH_TO_GROUP_FILES"],
      "PATH_TO_GROUP_TASKS" => $arResult["PATH_TO_GROUP_TASKS"],
      "GROUP_ID" => $arResult["VARIABLES"]["group_id"],
      "PAGE_ID" => "group_support",
   ),
   $component
);
?>
<br />
<?
use ALTASIB\Support;
use Bitrix\Main;

if(!Main\Loader::includeModule("altasib.support"))
{
        ShowError("ALTASIB_SUPPORT_MODULE_NOT_INSTALL");
        return;
}

$arParams['ID'] = $arResult["VARIABLES"]["TICKET_ID"];
$arParams["GROUP_ID"] = $arResult["VARIABLES"]["group_id"];
if(!Support\Tools::prepareComponentParams($arParams,$arResult))
    return false;

$arParams["URL_LIST"] = str_replace('#group_id#',$arParams["GROUP_ID"],COption::GetOptionString("altasib.support",'path_group_list'));
$APPLICATION->AddHeadScript("/local/components/altasib/support/templates/.default/script.js");
$APPLICATION->SetAdditionalCSS("/local/components/altasib/support/templates/.default/style.css");
require_once($_SERVER['DOCUMENT_ROOT'].'/local/components/altasib/support/templates/.default/ticket_edit.php');
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
        CURRENT_URL : new_href
    };
</script>');
?>