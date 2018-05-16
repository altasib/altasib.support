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
$arParams["SHOW_FILTER"] = 'Y';
$arParams["GROUP_ID"] = $arResult["VARIABLES"]["group_id"];
$arParams["URL_DETAIL"] = str_replace('#group_id#',$arParams["GROUP_ID"],COption::GetOptionString("altasib.support",'path_group_detail'));
$APPLICATION->IncludeComponent("altasib:support.ticket.list", "", 
    $arParams,
    $component
);
?>