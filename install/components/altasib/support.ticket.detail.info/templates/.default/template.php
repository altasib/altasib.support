<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Evgeniy Pedan, ESVSerge          #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2013 ALTASIB             #
#################################################
?>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
use Bitrix\Main;
use ALTASIB\Support;
?>
<?CJSCore::Init(array('jq_chosen'));?>
<?if($arParams['HAVE_CHANGE_CATEGORY'] || $arParams['HAVE_CHANGE_PRIORITY'] || $arParams['HAVE_CHANGE_STATUS'])
    include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/script.php");?>
<script type="text/javascript">
		AltasibSupport.textSave = "<?=GetMessage('ALTASIB_SUPPORT_DETAIL_INFO_SAVE')?>";
		AltasibSupport.textCancel = "<?=GetMessage('ALTASIB_SUPPORT_DETAIL_INFO_CANCEL')?>";
</script>	
<div id="support-detail-info">
    <table cellspacing="0" class="info-table">
        <tr>
                <td colspan="2" class="section" onclick="AltasibSupport.Bar.ToggleBlock('altasibSupportBar-info')" ><?=GetMessage("ALTASIB_SUPPORT_FORM_INFO_TXT")?></td>
        </tr>
		<tbody id="altasibSupportBar-info">
        <tr>
                <?/* from: */?>
				<td colspan = "2">
                    <div class = "altasib-column-support-title" onclick="AltasibSupport.Bar.ToggleBlock('responsible-popup')" ><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_OWNER")?>:</div>
					<div class = "altasib-column-support-info" style="background-color: #e2e6e9">
						<div class="altasib-column-support-info-td">
							<a class="altasib-support-info-user-avatar">
                            <?$ava = Support\User::getAvatar($arResult["TICKET_INFO"]["OWNER_USER_ID"]);?>
                            <?if(strlen($ava)>0):?>
                                <img width="30" height="30" src="<?=$ava?>">
                            <?else:?>
							 <img width="30" height="30" src="/bitrix/images/1.gif">
                            <?endif;?>
							</a>						
							<div class="altasib-column-support-user-info">
                                    <?if($arParams["ROLE"]>='W'):?>
									<a href="javascript:void(0)" onclick="AltasibSupport.Bar.ToggleBlock('responsible-popup')" class="altasib-dashed"><b><?=$arResult["TICKET_INFO"]["OWNER_USER_NAME"];?> <?=$arResult["TICKET_INFO"]["OWNER_USER_LAST_NAME"];?></b></a><br /><?=$arResult["TICKET_INFO"]["OWNER_USER_LOGIN"];?>
									<div class="altasib-column-support-more" id="responsible-popup">                
												- <a href="<?=$arParams['TICKET_LIST_URL']?>?setFilter=Y&set_support_filter=1&OWNER_USER_ID=<?=$arResult["TICKET_INFO"]["OWNER_USER_ID"]?>&CLOSE=ALL" title="<?=GetMessage('ALTASIB_SUPPORT_USER_TICKET')?>" target="_blank"><?=GetMessage('ALTASIB_SUPPORT_USER_TICKET')?></a>
												<?if(IsModuleInstalled('sale')):?>
													<br />- <a href="/bitrix/admin/sale_order.php?lang=<?=LANG?>&set_filter=Y&filter_user_id=<?=$arResult["TICKET_INFO"]["OWNER_USER_ID"]?>" target="_blank"><?=GetMessage('ALTASIB_SUPPORT_USER_ORDERS')?></a>
												<?endif;?>
											
										
									</div>
                                    <?else:?>
                                    <a href="javascript:void(0)"><b><?=$arResult["TICKET_INFO"]["OWNER_USER_NAME"];?> <?=$arResult["TICKET_INFO"]["OWNER_USER_LAST_NAME"];?></b></a><br /><?=$arResult["TICKET_INFO"]["OWNER_USER_LOGIN"];?>
                                    <?endif;?>
							</div>		
						</div>
					</div>
                </td>        
        </tr>
        <tr>
                <?/* responsible */?>
                <?if($arParams['HAVE_CHANGE_RESPONSIBLE']):?>
                    <td colspan = "2">
                        <?if(IsModuleInstalled('intranet')):?>
						<div class = "altasib-column-support-title"><?=GetMessage("ALTASIB_SUPPORT_DETAIL_CRM_ASSIGNED_BY")?>: <a href="javascript:void(0)" id="altasib-single-user-choice"> <?=GetMessage('ALTASIB_SUPPORT_EDIT_CHANGE_T')?></a></div>
						<div class = "altasib-column-support-info">
							<div class="altasib-column-support-info-td">
								<a class="altasib-support-info-user-avatar">
                                <?$ava = Support\User::getAvatar($arResult["TICKET_INFO"]["RESPONSIBLE_USER_ID"]);?>
                                <?if(strlen($ava)>0):?>
                                    <img id="altasib-support-resp-ava" width="30" height="30" src="<?=$ava?>">
                                <?else:?>
    							 <img id="altasib-support-resp-ava" width="30" height="30" src="/bitrix/images/1.gif">
                                <?endif;?>
                                
								</a>						
								<div class="altasib-column-support-user-info">						
									<input type="hidden" name="RESPONSIBLE_ID" id="RESPONSIBLE_ID" value="<?=$arResult["TICKET_INFO"]["RESPONSIBLE_USER_ID"];?>" />

									<a href="javascript:void(0)" class="altasib-dashed" onclick="if (BX.IM) { BXIM.openMessenger( BX('RESPONSIBLE_ID').value ); return false; } else { window.open('', '', 'status=no,scrollbars=yes,resizable=yes,width=700,height=550,top='+Math.floor((screen.height - 550)/2-14)+',left='+Math.floor((screen.width - 700)/2-5)); return false; }"><b><span id="RESPONSIBLE_NAME"><?=$arResult["TICKET_INFO"]["RESPONSIBLE_USER_SHORT_NAME"]?></b></span></a><br />&nbsp;
									<?/*<br /><a href="javascript:void(0)" id="altasib-single-user-choice"> <?=GetMessage('ALTASIB_SUPPORT_EDIT_CHANGE_T')?></a>*/?>


									<script type="text/javascript" src="/bitrix/components/bitrix/intranet.user.selector.new/templates/.default/users.js"></script>
									<script type="text/javascript">BX.loadCSS('/bitrix/components/bitrix/intranet.user.selector.new/templates/.default/style.css');</script>
									<?$APPLICATION->IncludeComponent(
													"bitrix:intranet.user.selector.new", ".default", array(
															"MULTIPLE" => "N",
															"NAME" => 'responsible',
															"VALUE" => $arResult["TICKET_INFO"]["RESPONSIBLE_USER_ID"],
															"POPUP" => "Y",
															"ON_SELECT" => "changeResponsible",
															"SITE_ID" => SITE_ID,
															"SHOW_EXTRANET_USERS" => "NONE",
													), null, array("HIDE_ICONS" => "Y")
									);?>
								</div>
							</div>
						</div>	
                        <?else:?>
							<div class = "altasib-column-support-title"><?=GetMessage("ALTASIB_SUPPORT_DETAIL_CRM_ASSIGNED_BY")?>:</div>
                            <div id="change-responsible-d">
                                <a href="javascript:void(0);" id="support-change-responsible"><?=$arResult["TICKET_INFO"]["RESPONSIBLE_USER_SHORT_NAME"];?></a>
                            </div>                            
                            <div id="change-responsible-ds" style="display: none;">
                                <input type="hidden" name="RESPONSIBLE_ID" id="RESPONSIBLE_ID" value="<?=$arResult["TICKET_INFO"]["RESPONSIBLE_USER_ID"];?>" />
                                <select id="RESPONSIBLE_ID_ST"  class="chosen-responsible" onchange="changeResponsible(this)">
                                    <?foreach($arParams['SUPPORT_TEAM'] as $SID=>$sUser):?>
                                    <option value="<?=$SID?>" <?if($arResult["TICKET_INFO"]["RESPONSIBLE_USER_ID"]==$SID):?>selected<?endif;?>><?=$sUser;?></option>
                                    <?endforeach;?>
                                </select>
                                
                            </div>
                        <?endif;?>
                    </td>
                <?else:?>
                <td colspan="2">
                <div class = "altasib-column-support-title"><?=GetMessage("ALTASIB_SUPPORT_DETAIL_CRM_ASSIGNED_BY")?>:</div>
						<div class = "altasib-column-support-info">
							<div class="altasib-column-support-info-td">
								<a class="altasib-support-info-user-avatar">
                                <?$ava = Support\User::getAvatar($arResult["TICKET_INFO"]["RESPONSIBLE_USER_ID"]);?>
                                <?if(strlen($ava)>0):?>
                                    <img width="30" height="30" src="<?=$ava?>">
                                <?else:?>
    							 <img width="30" height="30" src="/bitrix/images/1.gif">
                                <?endif;?>
								</a>
                                <?=$arResult["TICKET_INFO"]["RESPONSIBLE_USER_SHORT_NAME"];?>
                               </div>
                         </div>                
                </td>
                <?endif;?>
        </tr>	
        <?if($arParams['HAVE_CHANGE_RESPONSIBLE'] || $arResult["TICKET_INFO"]["RESPONSIBLE_USER_ID"]==$USER->GetID()):?>
        <tr>
            <?if(IsModuleInstalled('intranet')):?>
            <?/*  more people */?>
            <td colspan="2">
            <div class = "altasib-column-support-title"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_MEMBER");?>: <a href="#" id='add-member-choice' ><?=GetMessage('ALTASIB_SUPPORT_EDIT_EDIT_T')?></a></div>
            <div class="altasib-column-support-info">
				<div class="altasib-column-support-info-td">
					<div id="altasib-support-ticket-members">
					
						<?foreach($arResult['MEMBERS'] as $member):?>
							<div id="member_<?=$member['USER_ID']?>" class="altasib_member_list">
								- <a href="javascript:void(0)" class="altasib-dashed" onclick="if (BX.IM) { BXIM.openMessenger(<?=$member['USER_ID']?>); return false; } else { window.open('', '', 'status=no,scrollbars=yes,resizable=yes,width=700,height=550,top='+Math.floor((screen.height - 550)/2-14)+',left='+Math.floor((screen.width - 700)/2-5)); return false; }"><?=$member['USER_SHORT_NAME']?></a>
							</div>
						<?endforeach;?>
					</div>  
				</div>
			</div>	
            <script type="text/javascript" src="/bitrix/components/bitrix/intranet.user.selector.new/templates/.default/users.js"></script>
            <script type="text/javascript">BX.loadCSS('/bitrix/components/bitrix/intranet.user.selector.new/templates/.default/style.css');</script>
            <?$APPLICATION->IncludeComponent(
                            "bitrix:intranet.user.selector.new", ".default", array(
                                    "MULTIPLE" => "Y",
                                    "NAME" => 'member',
                                    "VALUE" => array_keys($arResult['MEMBERS']),
                                    "POPUP" => "Y",
                                    //"ON_SELECT" => "addMember",
                                    "ON_CHANGE" => 'addMember',
                                    "SITE_ID" => SITE_ID,
                                    "SHOW_EXTRANET_USERS" => "NONE",
                            ), null, array("HIDE_ICONS" => "Y")
                    );?>
            </td>
            <?else:?>   
				<?$arrSelectetUser = array("NONE")?>

            <td colspan="2">
				<div class = "altasib-column-support-title"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_MEMBER");?>: <a href="javascript:void(0)" id="support-add-member">...</a></div>
            <div class="altasib-column-support-info">
				<div class="altasib-column-support-info-td">
					<div id="altasib-support-ticket-members">
					
						<?foreach($arResult['MEMBERS'] as $member):?>
							<?$arrSelectetUser[] = $member['USER_ID'];?>
							- <?=$member['USER_SHORT_NAME']?><br />
						<?endforeach;?>
					</div> 
					<div id="support-add-member-sel" style="display: none; width: 99%">
							<div>
							<select id="MEMBER_ID" multiple class="chosen-member" data-placeholder="<?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_MEMBER_NONE");?>" style="width: 99%"><?/* onchange="addMember2(this.selectedIndex)"*/?>
								<?foreach($arParams['SUPPORT_TEAM'] as $SID=>$sUser):?>
								<?						
								$sel = (array_search($SID, $arrSelectetUser)) ? " selected " : ""?>
								<option value="<?=$SID?>" <?=$sel?>><?=$sUser;?></option>
								<?endforeach;?>
							</select>			
							</div>
					</div>						
				</div>
			</div>					
            </td>                        
            <?endif;?>                    
        </tr>
        <?endif;?>
        <tr class="colum-tr">
            <td class="colum-name"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_CREATED");?>:</td>
            <td><?=$arResult["TICKET_INFO"]["DATE_CREATE"]->toString();?></td>
        </tr>			
        <tr class="colum-tr">
            <td class="colum-name"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_CATEGORY");?>:</td>
            <td>
            <?if(!$arParams['HAVE_CHANGE_CATEGORY']):?>
            <span id="support-change-category"><?=$arResult["TICKET_INFO"]["CATEGORY_NAME"];?></span>
            <?else:?>
                <div>
                    <div id="change-category-d">
                        <a href="javascript:void(0);" id="support-change-category" class="altasib-dashed"><?=$arResult["TICKET_INFO"]["CATEGORY_NAME"];?></a>
                    </div>
                    <div class="choose_p" id="support-change-category-popup" style="display: none;">                
                        <div class="choose_popup">

							<ul class="popup_menuItem">
                                <?foreach($arResult["CATEGORY"] as $arCategory):?>
									<?$ClassItem = ($arResult["TICKET_INFO"]["CATEGORY_NAME"] == $arCategory['NAME']) ? "choose_item_selected" : ""?>
									<li class="<?=$ClassItem?>" onclick="changeCaregory(<?=$arCategory['ID']?>)" id="category-info-<?=$arCategory['ID']?>"><?=$arCategory['NAME']?></li>	
                                <?endforeach;?>
							</ul>
                        </div>                
                    </div>
                </div>                
            <?endif;?>
            </td>
        </tr>
        <tr class="colum-tr">
            <td class="colum-name"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_PRIORITY");?>:</td><?/* priority */?>
            <td class="colum-info">
            <?if(!$arParams['HAVE_CHANGE_PRIORITY']):?>
                <span id="support-change-priority"><?=strlen($arResult["TICKET_INFO"]["PRIORITY_NAME"])>0 ? $arResult["TICKET_INFO"]["PRIORITY_NAME"] : GetMessage("ALTASIB_SUPPORT_EDIT_FORM_PRIORITY_EMPTY");?></span>
            <?else:?>
                <div>
                    <div id="change-priority-d">
                        <a href="#" class="altasib-dashed" id="support-change-priority"><?=$PriorityName = $arResult["TICKET_INFO"]["PRIORITY_NAME"] ? $arResult["TICKET_INFO"]["PRIORITY_NAME"] : GetMessage("ALTASIB_SUPPORT_EDIT_FORM_PRIORITY_EMPTY");?></a>
                    </div>
                    <div class="choose_p" id="support-change-priority-popup" style="display: none;"><?//priority-popup?>
                        <div class="choose_popup">
                        	<ul class="popup_menuItem">
                                <?foreach($arResult["PRIORITY"] as $pid=>$pName):?>
									<?$ClassItem = ($PriorityName == $pName) ? "choose_item_selected" : ""?>
                                    <li class="<?=$ClassItem?>" onclick="changePriority(<?=$pid?>)" id="priority-info-<?=$pid?>"><?=$pName?></li>	
                                <?endforeach;?> 
							</ul>							
                        </div>                
                    </div>                
                </div>                
            <?endif;?>
            </td>
        </tr>
        <tr class="colum-tr">
            <td class="colum-name"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_STATUS");?>:</td><?/* status */?>
            <td>
            <?if(!$arParams['HAVE_CHANGE_STATUS']):?>
                <span id="support-change-status"><?=$arResult["TICKET_INFO"]["STATUS_NAME"];?></span>
            <?else:?>
                <div>
                    <div id="change-status-d">
                        <a href="#" class="altasib-dashed" id="support-change-status"><?=$arResult["TICKET_INFO"]["STATUS_NAME"];?></a>
                    </div>
                    <div class="choose_p" id="support-change-status-popup" style="display: none;">                
                        <div class="choose_popup">
                        	<ul class="popup_menuItem">
                                <?foreach($arResult["STATUS"] as $key => $arStatus):?>
									<?$ClassItem = ($arResult["TICKET_INFO"]["STATUS_NAME"] == $arStatus['NAME']) ? "choose_item_selected" : ""?>
                                    <li class="<?=$ClassItem?>" onclick="changeStatus(<?=$arStatus['ID']?>)" id="status-info-<?=$arStatus['ID']?>"><?=$arStatus['NAME']?></li>	
                                <?endforeach;?> 
							</ul>								
                        </div>                
                    </div>                    
                </div>
            <?endif;?>
            </td>
        </tr>            
        <?if($arResult["TICKET_INFO"]["SLA_ID"]>0):?>
        <tr class="colum-tr">
            <td class="colum-name"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_SLA");?>:</td>
            <td><?=$arResult["TICKET_INFO"]["SLA_NAME"];?></td>
        </tr>            
        <tr class="colum-tr">
            <td class="colum-name"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_SLA_TIME");?>:</td>
            <td><?=$arResult["TICKET_INFO"]["SLA_RESPONSE_TIME"];?> <?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_SLA_TIME_h");?></td>
        </tr>
        <?endif;?>
        
        <?if($arParams['IS_SUPPORT_TEAM']):?>
		<?// comments ?>
		<?
		if(trim($arResult["TICKET_INFO"]["COMMENT"])!="")
			$comment = $arResult["TICKET_INFO"]["COMMENT"];
		else	
			$comment = "...";
		?>
        <tr>
		
            <td colspan="2">
				<div class = "altasib-column-support-title"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_COMMENT");?>: <a href="javascript:void(0)" id="altasib-support-ticket-comment-click">...</a></div>
				<div class="altasib-column-support-info">
					<div class="altasib-column-support-info-td">
						<div id="altasib-support-ticket-comment"><?=str_replace("\n", "<br />", $comment);?></div><span id="altasib-support-ticket-comment-edit"></span>
					</div>
				</div>			
			</td>
        </tr>            
        <?endif;?>
		</tbody>
    </table>
</div>