<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Evgeniy Pedan, ESVSerge          #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2016 ALTASIB             #
#################################################
?>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
use ALTASIB\Support;
?>
<?if($arParams["ID"]>0):?>
<?
if($arParams['HAVE_CHANGE_CATEGORY'] || $arParams['HAVE_CHANGE_PRIORITY'] || $arParams['HAVE_CHANGE_STATUS'] || ($arParams['HAVE_CHANGE_RESPONSIBLE'] || $arResult["TICKET_INFO"]["RESPONSIBLE_USER_ID"]==$USER->GetID()) || $arParams['IS_SUPPORT_TEAM'])
    include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/script.php");
    
CJSCore::Init(Array("viewer"));    
include_once('message_template.php');
?>
<script>
	BX.message({
		SUPPORT_LOAD_MESSAGE_ALL : '<?php echo CUtil::JSEscape(GetMessage("SUPPORT_LOAD_MESSAGE_ALL")); ?>',
	});
</script>
<?if($arParams['SHOW_GROUP_SELECTOR']):
    $APPLICATION->IncludeComponent('bitrix:socialnetwork.group.selector', 'group', array(
        //'SELECTED' => $arResult["TICKET_INFO"]['GROUP_ID'],
        'BIND_ELEMENT' => 'support_ticket_group',
        'ON_SELECT' => 'supportChangeGroup',
        'SUPPORT_ROLE' => $arParams['ROLE'],
        ), 
        $component, 
        array('HIDE_ICONS' => 'Y')
    );
endif;?>
		<div class="ticket_title">#<?=$arResult["TICKET_INFO"]['ID']?> <?=$arResult["TICKET_INFO"]["TITLE"];?><?if($arResult["TICKET_INFO"]["IS_DEFERRED"]=='Y'):?> <span id="deffered">(<?=GetMessage("ALTASIB_SUPPORT_TICKET_DETAIL_IS_DEFERRED");?>)</span><?endif;?></div>
        <?/*<table border="0" cellpadding="0" cellspacing="2" width="100%" class="ticketn">
                <tr>
                        <th colspan="4" class="title_ticket_main">
                                <div class="title_block">
                                #<?=$arResult["TICKET_INFO"]['ID']?> <?=$arResult["TICKET_INFO"]["TITLE"];?><?if($arResult["TICKET_INFO"]["IS_DEFERRED"]=='Y'):?> <span id="deffered">(<?=GetMessage("ALTASIB_SUPPORT_TICKET_DETAIL_IS_DEFERRED");?>)</span><?endif;?>
                                <span style="float: right;">
                                <span id="support_ticket_group"><?=$arResult["TICKET_INFO"]['GROUP_NAME'];?></span>
                                <?if(IsModuleInstalled('socialnetwork')):?>
                                <?if($arParams['ALLOW'] || $USER->GetID()==$arResult["TICKET_INFO"]['OWNER_USER_ID']):?><a href="javascript:void(0)" onclick="groupsPopup.show()" id="support_ticket_group_selector"><?if($arResult["TICKET_INFO"]['GROUP_ID']>0):?><?=GetMessage('ALTASIB_SUPPORT_EDIT_FORM_PROJECT_CHANGE')?><?else:?><?=GetMessage('ALTASIB_SUPPORT_EDIT_FORM_PROJECT_SELECT')?><?endif;?></a><?endif;?>
                                <?endif;?>

                                </span>
                                </div>
                                <div class="line_dotted_main">&nbsp;</div>
                        </td>
                </tr>
        </table>*/?>			
			<table border="0" cellpadding="0" cellspacing="2" width="100%" class="ticketn ticketn_mess">
                <tr>
                        <td class="dop_title_td">
							<div class="dop_title_block"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_TT");?></div>
							<div style="float: right;">
								<span id="support_ticket_group"><?=$arResult["TICKET_INFO"]['GROUP_NAME'];?></span>
								<?if(IsModuleInstalled('socialnetwork')):?>
								<?if(IsModuleInstalled('intranet') && ($arParams['ALLOW'] || $USER->GetID()==$arResult["TICKET_INFO"]['OWNER_USER_ID'])):?><a href="javascript:void(0)" onclick="groupsPopup.show()" id="support_ticket_group_selector"><?if($arResult["TICKET_INFO"]['GROUP_ID']>0):?><?=GetMessage('ALTASIB_SUPPORT_EDIT_FORM_PROJECT_CHANGE')?><?else:?><?=GetMessage('ALTASIB_SUPPORT_EDIT_FORM_PROJECT_SELECT')?><?endif;?></a><?endif;?>
								<?endif;?>
							</div>							
						
						</td>
                </tr>
                <tr>
                        <td class="altasib_text_mess_td" id="altasib_text_mess_td_0">
							<div class="altasib_text_div" id="altasib_text_div_0"><div class="altasib_text_div_inner"><?=$arResult["TICKET_INFO"]["MESSAGE"];?>
								<div class="altasibTicketPostMoreButton" id="altasibTicketPostMoreButton_0" onclick="showAltasibTicketPost('0', this)">
									<div class="altasibTicketPostMoreBut"></div>
								</div>							
							</div></div>
							

							
						</td>
                </tr>
			<?if(count($arResult["TICKET_INFO"]["FILES"])>0 || count($arResult["TICKET_INFO"]["FILES_IMAGE"])>0):?>
				
                
				<tr>
						<td class="text_add_files_td">
						<br /><div class="dop_title_block_2"><?=GetMessage("ALTASIB_SUPPORT_TICKET_DETAIL_FILES");?>:</div>
							<?foreach($arResult["TICKET_INFO"]["FILES_IMAGE"] as $arFile):?>
							<?$minImage = CFile::ResizeImageGet($arFile, Array("width" => 75, "height" => 50),BX_RESIZE_IMAGE_EXACT);?>
							<?=CFile::ShowImage($minImage['src'], 9999, 50, 
								'border=0 
								data-bx-viewer="image" 
								data-bx-title="'.$arFile['ORIGINAL_NAME'].'" 
								data-bx-src="'.$arFile['SRC'].'" 
								data-bx-download="'.$arFile['SRC'].'" 
								data-bx-width="'.$arFile['WIDTH'].'" 
								data-bx-height="'.$arFile['HEIGHT'].'"', 
							"", false);?>							
							<?endforeach;?>						
                            <br />
							<?foreach($arResult["TICKET_INFO"]["FILES"] as $arFile):?>
							<a target="_blank" href="<?=$arFile["URL"]?>"><?=$arFile["ORIGINAL_NAME"]?></a> <small>(<?=$arFile["FORMAT_FILE_SIZE"]?>)</small><br />
							<?endforeach;?>
						</td>
				</tr>
				
			<?endif;?>
			</table><br />
				<?/*if(count($arResult["TICKET_INFO"]["FILES"])>0 || count($arResult["TICKET_INFO"]["FILES_IMAGE"])>0):?>
				<table border="0" cellpadding="0" cellspacing="2" width="100%" class="ticketn"> 				
                <tr>
                        <td align="center" class="dop_title_td"><div class="dop_title_block"><?=GetMessage("ALTASIB_SUPPORT_TICKET_DETAIL_FILES");?></div></td>
                </tr>
				<tr class="title_mess">
						<td>
							<?foreach($arResult["TICKET_INFO"]["FILES_IMAGE"] as $arFile):?>
							<?=CFile::ShowImage($arFile['SRC'], 9999, 50, "border=0", "", true);?>
							<?endforeach;?>
                            <br />
							<?foreach($arResult["TICKET_INFO"]["FILES"] as $arFile):?>
							<a target="_blank" href="<?=$arFile["URL"]?>"><?=$arFile["ORIGINAL_NAME"]?></a> <?=$arFile["FORMAT_FILE_SIZE"]?>
							<?endforeach;?>
						</td>
				</tr>
				</table><br />
				<?endif;*/?>

                <?if($arParams["USER_FIELDS_SHOW"]):?>
				<table border="0" cellpadding="0" cellspacing="2" width="100%" class="ticketn">  
				<tr>
                        <td align="center" class="dop_title_td"><div class="dop_title_block"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_MORE_INFO");?></div></td>
                </tr>
                            <?foreach($arParams["USER_FIELDS"] as $FIELD_NAME=>$arUserField):
                            if($arUserField["USER_TYPE_ID"] == "video" || $arUserField["USER_TYPE_ID"] == "file" || $arUserField["USER_TYPE_ID"] == "iblock_section" || $arUserField["USER_TYPE_ID"] == "iblock_element")
                                continue;
                            ?>
                <tr>
                    <td>
                            <?if ((is_array($arUserField["VALUE"]) && count($arUserField["VALUE"]) > 0) || (!is_array($arUserField["VALUE"]) && StrLen($arUserField["VALUE"]) > 0)):?>
                                    <b><?=$arUserField["EDIT_FORM_LABEL"]?>:</b>
                                        <?$APPLICATION->IncludeComponent(
                                                "bitrix:system.field.view",
                                                $arUserField["USER_TYPE"]["USER_TYPE_ID"],
                                                array("arUserField" => $arUserField), null, array("HIDE_ICONS"=>"Y")
                                            );
                                        ?>
                            <?endif;?>
                    </td>
                </tr>       

                            <?endforeach;?>
                </table><br />
				<?endif;?>
				<div class="dop_title_block_div <?if($arResult['MESSAGE_CNT']>5):?>dashed-bottom<?endif?>">
					<div class="dop_title_block"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_DISCUSSION");?></div>
					<?if($arResult['MESSAGE_CNT']>5):?>
						<div id="support-load-line">
							<a href="#" id="al-sup-load-mess" class="sup-loadAll"><?=GetMessage('ALTASIB_SUPPORT_LOAD_LAST')?></a> <span id="sup-loadm-cnt"><a href="#" title="<?=GetMessage('ALTASIB_SUPPORT_LOAD_MESS_ALL');?>" id="al-sup-load-mess-all" class="sup-loadAll-all"><?=$arResult['leftCnt'];?></a></span>
						</div>
						<?/*div class="dashed-bottom"></div>*/?>
					<?endif;?>				
				</div>
				<table border="0" cellpadding="0" cellspacing="2" width="100%" class="ticketn">                                

                <tr id="tr-support-mess-list"<?/*if($arResult['MESSAGE_CNT']==0):?> style="display:none"<?endif;*/?>>
                        <td class="messege_list_tic" id="support-message-list">
                            <div id="support-messages-favorite">
                            <?foreach($arResult["FAVORITE_MESSAGES"] as $arMessage):?>								
                                <?=getMessageSupport($arMessage,$arParams,$arResult['TICKET_INFO'], $lastDate)?>
                            <?endforeach;?>
                            </div>
                            <div id="support-messages">
							<?$lastDate = ""?>
                            <?foreach($arResult["MESSAGES"] as $arMessage):?>								
                                <?=getMessageSupport($arMessage,$arParams,$arResult['TICKET_INFO'], $lastDate)?>
								<?$lastDate = $arMessage['DATE_CREATE']?>
                            <?endforeach;?>
                            </div>
                            </td>
                 </tr>
            </table>
            <?if($arParams['Right']->isSupportTeam()):?>
            <br />
            <div style="display: none; font-weight: bold;" id="ticketOnlineBlock"><?=GetMessage('ALTASIB_SUPPORT_ONLINE')?>: <span id="ticketOnline"></span></div>
            <?endif;?>
<?endif;?>