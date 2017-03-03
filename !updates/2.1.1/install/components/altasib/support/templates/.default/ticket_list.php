<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
$arParams['EDIT_URL'] =$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["ticket_edit"];
$APPLICATION->IncludeComponent("altasib:support.ticket.list", "", 
    $arParams,
    $component
);
?>