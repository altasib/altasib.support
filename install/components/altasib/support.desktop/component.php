<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Evgenió Pedan                    #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2014 ALTASIB             #
#################################################
?>
<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use ALTASIB\Support;
use Bitrix\Main;

if(!Main\Loader::includeModule("altasib.support"))
{
        ShowError("ALTASIB_SUPPORT_MODULE_NOT_INSTALL");
        return;
}
if(!$USER->IsAdmin())
    return;
    
$arParams['SUPPORT_TEAM'] = Support\Tools::getSupportTeam();

$obStatus = Support\StatusTable::getList();
while($arStatus = $obStatus->fetch())
    $arParams["STATUS"][$arStatus["ID"]] = $arStatus;            

$obCategory = Support\CategoryTable::getList();
while($arCategory = $obCategory->fetch())
    $arParams["CATEGORY"][$arCategory["ID"]] = $arCategory;      


$arResult = array();
$arResult['BY_TEAM'] = array();
$arResult['BY_STATUS'] = array();
$arResult['BY_CATEGORY'] = array();
foreach($arParams['SUPPORT_TEAM'] as $USER_ID=>$USER_NAME)
{
    $arResult['BY_TEAM'][$USER_ID] = array('CLOSE'=>0,'OPEN'=>0,'ALL'=>0);
    
    $ob = Support\TicketTable::getList(array(
        'filter'=> array('RESPONSIBLE_USER_ID'=>$USER_ID),
        'select'=> array('ID','IS_CLOSE','STATUS_ID','CATEGORY_ID')
    ));
    while($data = $ob->fetch())
    {
        if($data['IS_CLOSE']=='Y')
        {
            $arResult['BY_TEAM'][$USER_ID]['CLOSE']++;
            $arResult['BY_STATUS'][$data['STATUS_ID']]['CLOSE']++;
            $arResult['BY_CATEGORY'][$data['CATEGORY_ID']]['CLOSE']++;
        }
        else
        {
            $arResult['BY_TEAM'][$USER_ID]['OPEN']++;
            $arResult['BY_STATUS'][$data['STATUS_ID']]['OPEN']++;
            $arResult['BY_CATEGORY'][$data['CATEGORY_ID']]['OPEN']++;
        }
            
        $arResult['BY_TEAM'][$USER_ID]['ALL']++;
        $arResult['BY_STATUS'][$data['STATUS_ID']]['ALL']++;
        $arResult['BY_CATEGORY'][$data['CATEGORY_ID']]['ALL']++;
    } 
}
$this->IncludeComponentTemplate();
?>