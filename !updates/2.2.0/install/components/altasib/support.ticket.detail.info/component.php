<?php
/**
 * ALTASIB
 * @site http://www.altasib.ru
 * @email dev@altasib.ru
 * @copyright 2006-2018 ALTASIB
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use ALTASIB\Support;
use Bitrix\Main;

if(!Main\Loader::includeModule("altasib.support"))
{
        ShowError("ALTASIB_SUPPORT_MODULE_NOT_INSTALL");
        return;
}
$arParams["ID"] = (int)$arParams["ID"];

if(!($arParams['Right'] instanceof ALTASIB\Support\Rights))
    $arParams['Right'] = new ALTASIB\Support\Rights($USER->getId(),$arParams['ID']);

$arParams["ROLE"] = $Role = $arParams['Right']->getRole();
if($arParams['Right']->getRole() == 'D')
    $APPLICATION->AuthForm('');

$arParams['ALLOW'] = ($arParams["ROLE"]>= 'W');
$arParams['IS_SUPPORT_TEAM'] = $arParams['Right']->isSupportTeam();
$arParams['SHOW_GROUP_SELECTOR'] = false;
if(IsModuleInstalled('intranet'))
    $arParams['SHOW_GROUP_SELECTOR'] = true;
    
$arParams['SHOW_DEAL_SELECTOR'] = false;    
if(IsModuleInstalled('crm') && $arParams["ROLE"]=='W')
    $arParams['SHOW_DEAL_SELECTOR'] = true;

$arResult = $this->__parent->arResult;

if($arParams["ID"]>0)
{
    $select = array('*',
        'CATEGORY_NAME'=>'CATEGORY.NAME',
        'STATUS_NAME'=>'STATUS.NAME',
        'OWNER_USER_SHORT_NAME'=>'OWNER_USER.SHORT_NAME',
        'OWNER_USER_NAME'=>'OWNER_USER.NAME',
        'OWNER_USER_LAST_NAME'=>'OWNER_USER.LAST_NAME',
        'OWNER_USER_LOGIN'=>'OWNER_USER.LOGIN',
        'RESPONSIBLE_USER_SHORT_NAME'=>'RESPONSIBLE_USER.SHORT_NAME',
        'SLA_NAME'=>'SLA.NAME',
        'SLA_DESCRIPTION'=>'SLA.DESCRIPTION',
        'SLA_RESPONSE_TIME'=>'SLA.RESPONSE_TIME',
        'SUM_ELAPSED_TIME',
        'COMMENT',
    );
    if(Main\Loader::includeModule('socialnetwork'))
    {
        $select['GROUP_NAME'] = 'GROUP.NAME';
        $select['GROUP_OWNER_ID'] = 'GROUP.OWNER_ID';
    }
    $obTicket = Support\TicketTable::getList(array('filter'=>array('ID'=>$arParams["ID"]),'select' => $select));
    if(!$arTicket = $obTicket->fetch())
    {
            $arParams["ID"] = 0;
            $APPLICATION->AuthForm(GetMessage('ALTASIB_SUPPORT_CMP_TICKET_NOT_FOUND'));
    return false;
    }
    else
    {
        $arTicket["PRIORITY_NAME"] = $arTicket['PRIORITY_ID'] >0 ? Support\Priority::getName($arTicket['PRIORITY_ID']) : '';
        $arResult["TICKET_INFO"] = $arTicket;
        $arResult["CATEGORY"] = array();
        $arResult["STATUS"] = array();
        $arResult["PRIORITY"] = array();

        if($arParams['Right']->allow('CHANGE_CATEGORY'))
        {        
            $obCategory = Support\CategoryTable::getList();
            while($arCategory = $obCategory->fetch())
                $arResult["CATEGORY"][$arCategory["ID"]] = $arCategory;      
        }
        if($arParams['Right']->allow('CHANGE_STATUS'))
        {
            $obStatus = Support\StatusTable::getList();
            while($arStatus = $obStatus->fetch())
                $arResult["STATUS"][$arStatus["ID"]] = $arStatus;            
        }

        if($arParams['Right']->allow('CHANGE_PRIORITY'))
            $arResult["PRIORITY"] = Support\Priority::get();
        
        $dataMember = Support\TicketMemberTable::getList(array('select'=>array('*','USER_SHORT_NAME'=>'USER.SHORT_NAME'),'filter'=>array('TICKET_ID'=>$arParams['ID'])));
        while($member = $dataMember->fetch())
            $arResult['MEMBERS'][$member['USER_ID']] = $member;
         
        if(!IsModuleInstalled('intranet'))
        {
            $arParams['SUPPORT_TEAM'] = Support\Tools::getSupportTeam();
        }        
        $this->IncludeComponentTemplate();         
    }
}
?>