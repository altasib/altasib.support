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

<?if($arParams['ID']===0):?>
	<script>
		BX.ready(function () {
		AltasibSupport.PopupWindow.Init();
		});  
		BX.message({
			
			});
		AltasibSupport.textSave = "<?=GetMessage('ALTASIB_SUPPORT_EDIT_FORM_SAVE')?>";
		AltasibSupport.textCancel = "<?=GetMessage('ALTASIB_SUPPORT_EDIT_FORM_CANCEL')?>";	
	</script>
<?endif;?>

<?if($arParams["HAVE_ANSWER"] || ($arParams['ID']==0 && $arParams["HAVE_CREATE"])):?>
<br style="clear:both;"/>
	<?if (!empty($arResult["ERRORS"])):?>
			<?=ShowError(implode("<br />", $arResult["ERRORS"]));?>
	<br style="clear:both;"/>
	<?endif?>
	<?CJSCore::Init(array('add_js_css'));?>
	<?CJSCore::Init(array('jq_chosen'));?>
<?if($arParams['IS_SUPPORT_TEAM']):?>

	<div id="support-change-qa" style="display: none;"></div>
	<div class="choose_p support-change-qa-popup" id="support-change-qa-popup" style="display: none;">                
		<div class="choose_popup popup_menuItem">
			<select name="QuickResponse" id="quickresponsesel" class="qr">
				<option value="0"></option>
				<?foreach($arParams['QuickResponse'] as $k=>$v):?>
				<option value="<?=$v['ID']?>"><?=$v['NAME']?></option>
				<?endforeach;?>
			</select>
		</div>                
	</div>
	<?foreach($arParams['QuickResponse'] as $k=>$v):?>
	<div style="display: none;" id="QuickResponse-<?=$v['ID']?>"><?=$v['DESCRIPTION']?></div>
	<?endforeach;?>
	<?if($arParams["ID"] > 0):?>
	<script>
	BX.ready(function () {
		BX.addCustomEvent(window, "OnEditorInitedBefore", function(e){
			
			e.AddButton({
				id : 'quickAnswer',
				name : '<?=GetMessage('ALTASIB_SUPPORT_EDIT_QUICK_ANSWER_TITLE')?>',
				iconClassName : 'bxhtmled-button-quick_answer',
				handler : function () {$("#quickresponsesel").chosen();AltasibSupport.PopupMenu.Show(BX('support-change-qa'));},
				src : '/local/components/altasib/support/images/quick_answer.gif'
			});
		});
		
		$('#quickresponsesel').on('change', function(event, params) {
			window["BXHtmlEditor"].Get('MESSAGE').SetContent(window["BXHtmlEditor"].Get('MESSAGE').GetContent()+$('#QuickResponse-'+params.selected).html());
			AltasibSupport.PopupMenu.ToggleMenu('support-change-qa-popup');
	  });    
	});  
	</script>
	<?endif;?>	
<?endif;?>
<span id="errors" style="display: none; color: red;"></span>
<a name="message"></a>
<?if($arParams["ID"]==0 && $arResult['SLA']):?>
<?ShowNote(GetMessage('ALTASIB_SUPPORT_EDIT_FORM_SLA',array('#SLA_NAME#'=>$arResult['SLA']['NAME'],'#SLA_TIME#'=>$arResult['SLA']['RESPONSE_TIME'])))?>
<?endif;?>

<?
if($arParams["ID"]>0 && isset($_SESSION["TICKET_MESSAGE_OK"]) &&$_SESSION["TICKET_MESSAGE_OK"])
{
    echo ShowNote(GetMessage('ALTASIB_SUPPORT_MESSAGE_ADD_OK'));
    unset($_SESSION["TICKET_MESSAGE_OK"]);
}
?>
<form name="ticket_add" id="ticket_add" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
<input type="hidden" name="TICKET_ID" id="TICKET_ID" value="<?=$arParams["ID"];?>" />
<input type="hidden" name="PARRENT_MESSAGE_ID" value="<?=$arResult['PARRENT_MESSAGE_ID'];?>" />
<input type="hidden" name="SEND_MESSAGE" id="SEND_MESSAGE" value="Y" />
<input type="hidden" name="t_submit_go" id="t_submit_go" value="" />
<?=bitrix_sessid_post()?>
<div class="altasib_ticketnform" id='ticketn'>
<?if($arParams["ID"]==0 || ($arParams["ID"]>0 && $arParams["REPLY_ON"]=="Y" && !$arResult["IS_CLOSE"])):?>
	<?if($arParams["ID"]==0):?>
		<div class="dop_title_block_form"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_ADD_NEW_MESSAGE")?></div><br /><br />
		<?if(count($arParams['CUSTOMER_LIST'])):?>
			<div class="left_title_pole"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_OWNER")?>:<span class="starrequired">*</span></div>
			<div class="addTicket-owner_chosen">
					<select id="OWNER_ID" name="OWNER_ID" class="owner_id">
						<option value="0">-</option>
						<?foreach($arParams['CUSTOMER_LIST'] as $SID=>$sUser):?>
						<option value="<?=$SID?>" <?if($SID == $USER->GetID()):?>selected<?endif;?>><?=$sUser;?></option>
						<?endforeach;?>
					</select>
			</div>
		<?endif;?>
		<div  class="left_title_pole"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_NAME")?>:<span class="starrequired">*</span></div>
		<div class="addTicket-titleInputtext">
			<input type="text" name="TITLE" size="50" value="<?=$arResult["TITLE"];?>" class="inputtext"/>
		</div>
		<div  class="left_title_pole"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_CATEGORY")?>:<span class="starrequired">*</span></div>
		<div class="addTicket-select">
			<select name="CATEGORY_ID" class="owner_id">
				<option value="0"> </option>
				<?foreach($arResult["CATEGORY"] as $arCategory):?>
				<option value="<?=$arCategory["ID"]?>" <?if($arResult["CATEGORY_ID"]==$arCategory["ID"] || (!isset($arResult["CATEGORY_ID"]) && $arCategory["USE_DEFAULT"]=="Y"))echo "selected";?>><?=$arCategory["NAME"];?></option>
				<?endforeach;?>
			</select>
		</div>
	<?endif;?>		
	<?if ($arParams["ID"] == 0):?>
		<?if ($arParams['SHOW_GROUP_SELECTOR']):?>
		<div class="left_title_pole_projects"><b><?=GetMessage('ALTASIB_SUPPORT_EDIT_FORM_GROUP')?>:</b>
		<?$APPLICATION->IncludeComponent('bitrix:socialnetwork.group.selector', 'group', array(
				'SELECTED' => $arParams['GROUP_ID'],
				'BIND_ELEMENT' => 'support_ticket_group',
				'ON_SELECT' => 'altasibSelectGroup',
				'SUPPORT_ROLE' => $arParams['ROLE'],
			), $component, array('HIDE_ICONS' => 'Y')
		);
		?>
		<span id="support_ticket_group"></span> 
			<?if(($arParams['GROUP_ID']==0 && $arParams['ROLE'] == 'C') || $arParams['ROLE'] == 'W'):?>
				<a href="javascript:void(0)" onclick="groupsPopup.show()"><?if($arParams['GROUP_ID']>0):?><?=GetMessage('ALTASIB_SUPPORT_EDIT_FORM_PROJECT_CHANGE')?><?else:?><?=GetMessage('ALTASIB_SUPPORT_EDIT_FORM_PROJECT_SELECT')?><?endif;?></a>
			<?endif;?>
		</div>			
	<?endif;?>	
	<div  class="left_title_pole"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_PRIORITY")?>:</div>
	<div class="addTicket-select">
		<select name="PRIORITY_ID" class="owner_id">
			<option value="0"> </option>
			<?foreach($arResult["PRIORITY"] as $PRIORITY_ID=>$PRIORITY_NAME):?>
				<option value="<?=$PRIORITY_ID?>" <?if($arResult["PRIORITY_ID"]==$PRIORITY_ID)echo "selected";?>><?=$PRIORITY_NAME;?></option>
			<?endforeach;?>
		</select>
	</div>
                                
	<?foreach($arParams["USER_FIELDS"] as $FIELD_NAME=>$arUserField):?>
	<div>
		<div  class="left_title_pole"><?if ($arUserField["MANDATORY"]=="Y"):?><span class="requred_txt">*</span><?endif;?> <?=$arUserField["EDIT_FORM_LABEL"]?>:</div>
		<div>
			<?$APPLICATION->IncludeComponent(
			   "bitrix:system.field.edit",
			   $arUserField["USER_TYPE"]["USER_TYPE_ID"],
			   array("bVarsFromForm" => $arResult, "arUserField" => $arUserField), null, array("HIDE_ICONS"=>"Y"));
			?>
		</div>
	</div>
	<?endforeach;?>                                
	<div><div class="lastPadding"></div></div>
<?endif;?>
	<div>
		<div>
		<?if($arParams["ID"]>0 && !$arParams['SHOW_FULL_FORM']):?>
			<div class="support-form-note" id="divSupportFormShowNote"><div><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_ADD_MESSAGE")?></div></div>
			<div id="divSupportFormShow" style="display:none;">                                        
				<?ALTASIB\Support\Tools::ShowLHE("MESSAGE",$arResult["MESSAGE"],"MESSAGE");?>
			</div>
		<?else:?>
		  <?ALTASIB\Support\Tools::ShowLHE("MESSAGE",$arResult["MESSAGE"],"MESSAGE");?>
		<?endif;?>
		</div>
	</div>
	<div class="support-form-tab">
	<a  onclick="BX.onCustomEvent(BX('support-form-loadFiles'), 'BFileDLoadFormController'); return false;" href="#" class="altasib-form-tab"><i class="icon-paper-clip"> </i><span><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_FILE")?></span></a>  
	<?if($arParams["ID"]>0):?>
		<a onClick = "AltasibSupport.Bar.ToggleBlock('altasib-param-form'); return false;" class="altasib-form-tab" href="#"><i class="icon-ok-sign"> </i><span><?=GetMessage("ALTASIB_SUPPORT_FORM_PARAM_ANSW")?></span></a> 
	<?endif;?>
		<div>
			<div id="support-form-loadFiles"> <?
				$APPLICATION->IncludeComponent("bitrix:main.file.input","drag_n_drop",Array(
					"ALLOW_UPLOAD"=>"F",
					"ALLOW_UPLOAD_EXT" => $arParams["UPLOAD_FILE_TYPE"],
					//"MAX_FILE_SIZE" => $arParams["UPLOAD_FILE_SIZE"],
					"INPUT_NAME"=>"FILES",
					"INPUT_NAME_UNSAVED"=>"FILES_TMP",
					"MULTIPLE"=>"Y",
					"MODULE_ID"=>"altasib.support",
					)
				);
				?>
			</div>
		</div>
		
	</div>

	<?if($arParams["USE_CAPTCHA"] == "Y" && $arParams["ID"] == 0):?>
		<div>
			<div  class="left_title_pole"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_CAPTCHA_TITLE")?>:</div>
			<div>
					<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
					<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
			</div>
		</div>
		<div>
			<div  class="left_title_pole"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_CAPTCHA_PROMPT")?><span class="starrequired">*</span>:</div>
			<div><input type="text" class="inputtext" name="captcha_word" maxlength="50" value=""></div>
		</div>
	<?endif?>
<?endif;?>
<?if($arParams["ID"]==0):?>
<div>
	<div  class="left_title_pole">&nbsp;</div>
	<div>
			<input type="hidden" name="t_submit" id="t_submit" value="Y" />
			<a class="altasib-support-button" id="altasib-support-create-button" onclick="alSupForm.create();"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_SUBMIT_CREATE")?></a>
			<a class="cancel-form" href="<?=$arParams["TICKET_LIST_URL"]?>"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_SUBMIT_CANCEL")?></a>
	</div>
	</div>
<?endif;?>
<?if($arParams["ID"]>0 && $arResult["IS_CLOSE"]):?>
	<div>
		<div  class="left_title_pole">&nbsp;</div>
		<div>
			<input type="hidden" name="OPEN" value="Y" />
			<input type="hidden" name="t_submit" id="t_submit" value="Y" />
			<a class="altasib-support-button" onclick="document.forms['ticket_add'].submit();"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_OPEN")?></a>                                    
		</div>
	</div>
<?elseif($arParams["ID"]>0 && $arParams["REPLY_ON"]=="Y"):?>
	<?//if($arParams['IS_SUPPORT_TEAM']):?>
	<div id="altasib-param-form">
	<div class="dop_title_block_2"><?=GetMessage("ALTASIB_SUPPORT_FORM_PARAM_ANSW")?></div>		
		<div class="altasib-param-form-inner">
			<?if($arParams['IS_SUPPORT_TEAM']):?>							
			<div class="altasib-param-form1">
			<label><input type="checkbox" name="NOT_CHANGE" id="NOT_CHANGE" value="Y" /><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_NOT_CHANGE")?></label><br />
			<label><input type="checkbox" name="IS_HIDDEN" id="IS_HIDDEN" value="Y" /><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_HIDDEN")?></label>
			</div>
			<?endif;?>								
			<div class="altasib-param-form2">
				<label><input type="checkbox" name="IS_DEFERRED" value="Y" /><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_IS_DEFERRED_TITLE")?></label><br />
				<label><input type="checkbox" name="CLOSE" value="Y" /><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_CLOSE")?></label>								
			</div>
		</div>	
	</div>						

	<div>
			<div  class="left_title_pole">&nbsp;</div>
			<div>
			<a class="altasib-support-button" id="altasib-support-submit-form" href="#"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_SUBMIT")?></a>
			<a id="altasib-support-submit-form-and-go" href="#"><?=GetMessage("ALTASIB_SUPPORT_EDIT_FORM_SUBMIT_AND_GO")?></a>
			<span class="span-cancel-form"><a href="#" id="cancel-form"><?=GetMessage('ALTASIB_SUPPORT_EDIT_FORM_SUBMIT_CANCEL')?></a></span>
			</div>
	</div>
	<?endif;?>
</div>
<?if($arParams["ID"] == 0):?>
        <div class="altasib_support_edit_required"><?=GetMessage("ALTASIB_SUPPORT_EDIT_REQUIRED");?></div>
<?endif;?>		
        <?if(!$arParams['IS_SUPPORT_TEAM'] && $arParams['SHOW_GROUP_SELECTOR']):?>
        <div id="supportFormNote">
        <?=ShowNote(GetMessage('ALTASIB_SUPPORT_EDIT_FORM_GROUP_NOTE'));?>
        </div>
        <?endif;?>
<br />
<?endif;?>
<?if($arParams["ID"]>0 && $USER->IsAuthorized()):?>
<a href="<?=$arParams["URL_LIST"]?>"><?=GetMessage('ALTASIB_SUPPORT_PATH_TO_LIST')?></a>
<?endif;?>