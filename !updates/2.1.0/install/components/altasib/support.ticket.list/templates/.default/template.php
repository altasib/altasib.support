<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Evgeniy Pedan                    #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2010 ALTASIB             #
#################################################
?>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if($arParams["HAS_CREATE"]):?>
<a href="<?=$arParams["CREATE_URL"];?>" class="altasib-support-button"><?=GetMessage("ALTASIB_STL_T_CREATE_TICKET");?></a>
<br />
<?endif;?>
<?
$gridRows = array();
foreach ($arResult["TICKET"] as $arTicket)
{
    $overdue = ($arParams['SUPPORT_TEAM'] && $arTicket['IS_OVERDUE']=='Y') ? ' <span style="color: red; font-size: smaller;">'.GetMessage('ALTASIB_SUPPORT_LIST_IS_OVERDUE').'</span>' : '';
    $color = '<div class="altasib-support-block-'.$arTicket['COLOR'].'"></div>';
    $cols = array(
        'ID' => '<a href="'.$arTicket["URL_DETAIL"].'"'.(($arTicket['LAST_MESSAGE_BY_SUPPORT']=='N' && $arParams['SUPPORT_TEAM']) ? 'style="color:red"' : '').'>'.$arTicket["ID"].'</a>',
        'TITLE' => '<a href="'.$arTicket["URL_DETAIL"].'">'.$arTicket["TITLE"].'</a>'.$overdue,
        'CATEGORY'=> $arTicket["CATEGORY_NAME"],
        'STATUS' => $color.$arTicket["STATUS_NAME"],
        'RESPONSIBLE' => $arTicket['RESPONSIBLE_USER_SHORT_NAME'],
        'OWNER' => $arTicket['OWNER_USER_SHORT_NAME'],
        'PRIORITY' => $arTicket['PRIORITY_ID']>0 ? \ALTASIB\Support\Priority::getName($arTicket['PRIORITY_ID']) : '',
        'CREATED_USER' => $arTicket['CREATED_USER_SHORT_NAME'],
        'MODIFIED_USER' => $arTicket['MODIFIED_USER_SHORT_NAME'],
        'LAST_MESSAGE_USER' => $arTicket['LAST_MESSAGE_USER_SHORT_NAME'],
        'SLA' => $arTicket['SLA_NAME'],
        'PROJECT_ID' => $arTicket['PROJECT_NAME'],
    );
    
    $gridRows[] = array("data"=>$arTicket, "actions"=>$aActions, "columns"=>$cols, "editable"=>false);    
}
$APPLICATION->IncludeComponent(
   "bitrix:main.interface.grid",
   "",
   array(
      "GRID_ID"=>$arParams['GRID_ID'],
      "HEADERS"=>$arParams['GRID']['HEADER'],
      "SORT"=>$arResult["SORT"],
      "SORT_VARS"=>$arResult["SORT_VARS"],
      "ROWS"=>$gridRows,
      "FOOTER"=>array(array("title"=>GetMessage('ALTASIB_STL_T_LIST_ALL'), "value"=>$arResult["NAV_OBJECT"]->NavRecordCount)),
      "ACTION_ALL_ROWS"=>false,
      "EDITABLE"=>false,
      "NAV_OBJECT"=>$arResult["NAV_OBJECT"],
      "AJAX_MODE"=>"N",
      "AJAX_OPTION_JUMP"=>"N",
      "AJAX_OPTION_STYLE"=>"Y",
      "FILTER"=>$arResult['GRID_FILTER'],
/*      '~FILTER_ROWS'=>array('TITLE'),*/
      "USE_THEMES"=>false
   )
);    
?>

<div style="display: table;padding-top: 10px;">
    <?if($arParams['IS_SUPPORT_TEAM']):?>
    <div style="display: table-row;">
        <div style="display: table-cell;"><div class="altasib-support-block-red" style="width: 100px;"></div></div>
        <div style="display: table-cell;"> - <?=GetMessage('ALTASIB_SUPPORT_LIST_LAST_MESSAGE_CLIENT_SUP')?></div>
    </div>
    <div style="display: table-row;">
        <div style="display: table-cell;"><div class="altasib-support-block-green" style="width: 100px;"></div></div>
        <div style="display: table-cell;"> - <?=GetMessage('ALTASIB_SUPPORT_LIST_LAST_MESSAGE_SUPPORT')?></div>
    </div>    
    <?else:?>
    <div style="display: table-row;">
        <div style="display: table-cell;"><div class="altasib-support-block-red" style="width: 100px;"></div></div>
        <div style="display: table-cell;"> - <?=GetMessage('ALTASIB_SUPPORT_LIST_LAST_MESSAGE_SUPPORT')?></div>
    </div>
    <div style="display: table-row;">
        <div style="display: table-cell;"><div class="altasib-support-block-green" style="width: 100px;"></div></div>
        <div style="display: table-cell;"> - <?=GetMessage('ALTASIB_SUPPORT_LIST_LAST_MESSAGE_CLIENT')?></div>
    </div>
    <?endif;?>
    <div style="display: table-row;">
        <div style="display: table-cell;"><div class="altasib-support-block-brown" style="width: 100px;"></div></div>
        <div style="display: table-cell;"> - <?=GetMessage('ALTASIB_SUPPORT_LIST_IS_DEFERRED')?></div>
    </div>
    <div style="display: table-row;">
        <div style="display: table-cell;"><div class="altasib-support-block-gray" style="width: 100px;"></div></div>
        <div style="display: table-cell;"> - <?=GetMessage('ALTASIB_SUPPORT_LIST_LAST_MESSAGE_CLOSE')?></div>
    </div>
</div>
