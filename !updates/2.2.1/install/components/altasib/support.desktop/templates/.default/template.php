<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Evgenió Pedan                    #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2014 ALTASIB             #
#################################################
?>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<table border="0" cellpadding="0" cellspacing="0" class="support-list">
        <tr>
                <th style="text-align:center;"><?=GetMessage('ALTASIB_SUPPORT_TMPL_RESPONSIBLE')?></th>
                <th>open</th>
                <th>close</th>
                <th>all</th>
        </tr>
<?
$i=0;
foreach ($arResult['BY_TEAM'] as $USER_ID=>$team):?>
        <?if ($i==1):?>
        <tr class="bg_light">
        <?else:?>
        <tr>
        <?endif;?>
                <td><?=$arParams['SUPPORT_TEAM'][$USER_ID]?></td>
                <td width="5%"><?=$team['OPEN']?></td>
                <td width="5%"><?=$team['CLOSE']?></td>
                <td width="5%"><?=$team['ALL']?></td>
        </tr>
        <?if ($i==1):?>
                <?$i=0;?>
        <?else:?>
                <?$i++;?>
        <?endif?>
<?endforeach;?>
</table>


<table border="0" cellpadding="0" cellspacing="0" class="support-list">
        <tr>
                <th style="text-align:center;"><?=GetMessage('ALTASIB_SUPPORT_TMPL_STATUS')?></th>
                <th>open</th>
                <th>close</th>
                <th>all</th>
        </tr>
<?
$i=0;
foreach ($arResult['BY_STATUS'] as $STATUS_ID=>$v):?>
        <?if ($i==1):?>
        <tr class="bg_light">
        <?else:?>
        <tr>
        <?endif;?>
                <td><?=$arParams['STATUS'][$STATUS_ID]['NAME']?></td>
                <td width="5%"><?=$v['OPEN']?></td>
                <td width="5%"><?=$v['CLOSE']?></td>
                <td width="5%"><?=$v['ALL']?></td>
        </tr>
        <?if ($i==1):?>
                <?$i=0;?>
        <?else:?>
                <?$i++;?>
        <?endif?>
<?endforeach;?>
</table>

<table border="0" cellpadding="0" cellspacing="0" class="support-list">
        <tr>
                <th style="text-align:center;"><?=GetMessage('ALTASIB_SUPPORT_TMPL_CATEGORY')?></th>
                <th>open</th>
                <th>close</th>
                <th><?=GetMessage('MAIN_ALL')?></th>
        </tr>
<?
$i=0;
foreach ($arResult['BY_CATEGORY'] as $CATEGORY_ID=>$v):?>
        <?if ($i==1):?>
        <tr class="bg_light">
        <?else:?>
        <tr>
        <?endif;?>
                <td><?=$arParams['CATEGORY'][$CATEGORY_ID]['NAME']?></td>
                <td width="5%"><?=$v['OPEN']?></td>
                <td width="5%"><?=$v['CLOSE']?></td>
                <td width="5%"><?=$v['ALL']?></td>
        </tr>
        <?if ($i==1):?>
                <?$i=0;?>
        <?else:?>
                <?$i++;?>
        <?endif?>
<?endforeach;?>
</table>