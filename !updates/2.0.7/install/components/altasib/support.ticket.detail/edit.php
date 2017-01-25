<?
#################################################
#        Company developer: ALTASIB
#        Developer: Evgeniy Pedan
#        Site: http://www.altasib.ru
#        E-mail: dev@altasib.ru
#        Copyright (c) 2006-2016 ALTASIB
#################################################
?>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$APPLICATION->RestartBuffer();
$arParams["NOT_HIDE_FORM"]="Y";
$arParams["USE_CAPTCHA"] = "N";
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/interface/admin_lib.php');
//CUtil::JSPostUnescape();
$popupWindow = new CJSPopup('', '');
$popupWindow->ShowTitlebar(GetMessage("MAIN_EDIT"));
$popupWindow->StartContent();
?>
<?$APPLICATION->IncludeComponent("altasib:support.ticket.form", "edit", $arParams);?>
<?
if($strWarning <> "")
	$popupWindow->ShowValidationError($arResult["ERROR_MESSAGE"]);
    
$popupWindow->ShowStandardButtons();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");die();?>