<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
CUtil::InitJSCore();
CUtil::InitJSCore(array('popup'));
?>
<script type="text/javascript">

    function sendMemberList(list)
    {
            BX.ajax({
                url:supportVar.CURRENT_URL,
                data:{"AJAX_CALL" : "Y","AJAX_ACTION": "SET_MEMBER_LIST","UID" : list,"sessid":supportVar.bsmsessid},
                dataType: 'json',
                method: 'POST',
                onsuccess: function(data)
                {
					if(data.status)
                    {				
					}
					
                },
                onfailure: function()
                {
                }
            }
            );                
    } 
	
<?if(($arParams['HAVE_CHANGE_RESPONSIBLE'] || $arResult["TICKET_INFO"]["RESPONSIBLE_USER_ID"]==$USER->GetID()) && !IsModuleInstalled('intranet')):?>
    function changeResponsible(select)
    {
        if(select.value>0)
        {
    		BX.ajax.post(supportVar.CURRENT_URL,{"AJAX_CALL" : "Y","AJAX_ACTION": "RESPONSIBLE","CID" : select.value,"sessid":supportVar.bsmsessid},function (res) {
    			eval(res);
    			if(jsRes.RESULT)
    			{
    			    BX('support-change-responsible').innerHTML = select.options[select.selectedIndex].text;
                    changeResponsibleSelector(false);
    			}
    		}
    		);
        }
    }

    function changeResponsibleSelector(show)
    {
        AltasibSupport.Bar.ToggleBlock('change-responsible-d');
		AltasibSupport.Bar.ToggleBlock('change-responsible-ds');
    }
    
BX.ready(function() {    

    $('#support-change-responsible').on('click',function(e){
		$('body').keydown(function(eventObject){
			if (eventObject.which == 27)
				{
				$('body').off('keydown');
				changeResponsibleSelector();
				}
		});
		
		e.preventDefault();	
		changeResponsibleSelector();
		$(".chosen-responsible").chosen();
    });	
	
    $('#support-add-member').on('click',function(e){
		$('body').keydown(function(eventObject){
			if (eventObject.which == 27)
				{
				$('body').off('keydown');
				AltasibSupport.Member.ShowMemberSelector();	
				$('#altasib-support-member-save').remove();				
				$('#altasib-support-member-cancel').remove();	
				}
			if (eventObject.which == 13)
				{
				AltasibSupport.Member.SaveSel();
				}				
		});
		
		e.preventDefault();	
        AltasibSupport.Member.ShowMemberSelector();
		$(".chosen-member").chosen();
		
		$('<a/>', {
		    class: 'altasib-support-button-save ButtonMin',
            id:'altasib-support-member-save',
            text: AltasibSupport.textSave,
            click: function(){
				AltasibSupport.Member.SaveSel();
            }
        }).appendTo('#support-add-member-sel'); 		
		$('<a/>', {
		    class: 'altasib-support-button-cancel ButtonMin',
            id:'altasib-support-member-cancel',
            text: AltasibSupport.textCancel,
            click: function(){
				AltasibSupport.Member.ShowMemberSelector();	
				$('#altasib-support-member-save').remove();				
				$('#altasib-support-member-cancel').remove();	
            }
        }).appendTo('#support-add-member-sel'); 		
    });		
	
});


AltasibSupport.Member = {
	
	SaveSel : function() //AltasibSupport.Member.SaveSel
	{
		var str = "";
		$( "#MEMBER_ID :selected" ).each(function() {
		  str += '- ' + $( this ).text() + " <br /> ";
		});	
		$('#altasib-support-ticket-members').html(str);
		
		$('body').off('keydown');
		this.SendMemberList($('#MEMBER_ID').val());				
		//$('#altasib-support-member-save').remove();				
		//$('#altasib-support-member-cancel').remove();	
	},	
	ShowMemberSelector : function() //AltasibSupport.Member.ShowMemberSelector
    {
		AltasibSupport.Bar.ToggleBlock('support-add-member');
		AltasibSupport.Bar.ToggleBlock('support-add-member-sel');
		AltasibSupport.Bar.ToggleBlock('altasib-support-ticket-members');
		this.WaitLoaderHide ();		
    },
    SendMemberList: function(list)
    {
		
		this.WaitLoader('support-add-member-sel');
		//alert(list)
		BX.ajax({
			url:supportVar.CURRENT_URL,
			data:{"AJAX_CALL" : "Y","AJAX_ACTION": "SET_MEMBER_LIST","UID" : list,"sessid":supportVar.bsmsessid},
			dataType: 'json',
			method: 'POST',
			onsuccess: function(data)
			{
				if(data.status)
				{	
					AltasibSupport.Member.ShowMemberSelector();	
				}
				
			},
			onfailure: function()
			{
			}
		}
		);                
    },
	WaitLoader: function (idBlock)
		{
			$('#altasib-support-member-save').animate({
				opacity: 0,	
				height: 0,
				lineHeight: 0
				}, 250, function(){this.remove()});
			$('#altasib-support-member-cancel').animate({
				opacity: 0,	
				height: 0,
				lineHeight: 0
				}, 250, function(){this.remove()});			
			$("<div />", {
				id:'altasib_choose_item_wait',
				class: 'altasib_choose_item_wait',
				text: '',
			}).appendTo('#' + idBlock).show(300);
		},
	WaitLoaderHide: function ()
		{
			$('.altasib_choose_item_wait').remove();
		}	
	
}	


<?    
endif;    
if(($arParams['HAVE_CHANGE_RESPONSIBLE'] || $arResult["TICKET_INFO"]["RESPONSIBLE_USER_ID"]==$USER->GetID()) && IsModuleInstalled('intranet')):
?>
    var ResponsiblePopup;
    
    function changeResponsible(arUser)
    {
    	BX.ajax.post(supportVar.CURRENT_URL,{"AJAX_CALL" : "Y","AJAX_ACTION": "RESPONSIBLE","CID" : arUser.id,"sessid":supportVar.bsmsessid},function (res) {
    		eval(res);
    		if(jsRes.RESULT)
    		{
                BX("RESPONSIBLE_ID").value = arUser.id;
                BX("RESPONSIBLE_NAME").innerHTML = BX.util.htmlspecialchars(arUser.name);
                photo = '/bitrix/images/1.gif';
                if(arUser.photo.length>0)
                    photo = arUser.photo;
                
                $('#altasib-support-resp-ava').attr('src', photo);
                ResponsiblePopup.close();
    		}
    	}
    	);
    }
    
    function ShowResponsibleSelector(e)
    {
            if(!e) e = window.event;
            if (!ResponsiblePopup)
            {
                    ResponsiblePopup = new BX.PopupWindow("responsible-popup", this, {
                            offsetTop : 1,
                            autoHide : true,
                            content : BX("responsible_selector_content"),
                            zIndex: 3000
                    });
            }
            else
            {
                    ResponsiblePopup.setContent(BX("responsible_selector_content"));
                    ResponsiblePopup.setBindElement(this);
            }
    
            if (ResponsiblePopup.popupContainer.style.display != "block")
            {
                    ResponsiblePopup.show();
            }
    
            return BX.PreventDefault(e);
    }
    
    function ClearP()
    {
            O_responsible.setSelected();
    }
    
    BX.ready(function() {
            BX.bind(BX("altasib-single-user-choice"), "click", ShowResponsibleSelector);
            BX.bind(BX("clear-user-choice"), "click", ClearP);
    });
    
    //member
    
	BX.message({
		SUPPORT_CHANGE_MEMBER_CANCEL : '<?php echo CUtil::JSEscape(GetMessage("SUPPORT_CHANGE_MEMBER_CANCEL")); ?>',
		SUPPORT_CHANGE_MEMBER_SELECT : '<?php echo CUtil::JSEscape(GetMessage("SUPPORT_CHANGE_MEMBER_SELECT")); ?>'
	});    
    var memberPopup;
    var arMembers = [];
    function showMemberSelector(el)
    {
    	if (!window.SupportMemberSelector)
    	{
    		window.SupportMemberSelector = BX.PopupWindowManager.create("add-member-popup", el, {
    			offsetTop : 1,
    			autoHide : true,
    			content : BX("member_selector_content"),
					buttons : [
						new BX.PopupWindowButton({
							text : BX.message("SUPPORT_CHANGE_MEMBER_SELECT"),
							className : "popup-window-button-accept",
							events : {click : function(e) {
								if(!e) e = window.event;

								var empIDs = [];
								BX.cleanNode(BX("altasib-support-ticket-members"));
								for(i = 0; i < arMembers.length; i++)
								{
									if (arMembers[i])
									{
										BX("altasib-support-ticket-members").appendChild(BX.create("div", {
											props : {
												className : "task-responsible-employee-item"
											},
											children : [
												BX.create("a", {
													props : {
														className : "task-responsible-employee-link",
														href : BX.message("TASKS_PATH_TO_USER_PROFILE").replace("#user_id#", arMembers[i].id),
														target : "_blank",
														title : arMembers[i].name
													},
													text : arMembers[i].name
												})
											]
										}));
										empIDs.push(arMembers[i].id);
									}
								}
                                sendMemberList(empIDs);

								this.popupWindow.close();
							}}
						}),

						new BX.PopupWindowButtonLink({
							text : BX.message("SUPPORT_CHANGE_MEMBER_CANCEL"),
							className : "popup-window-button-link-cancel",
							events : {click : function(e) {
								if(!e) e = window.event;

								this.popupWindow.close();

								BX.PreventDefault(e);
							}}
						})
					]                
    		});
    	}
    
    	if (window.SupportMemberSelector.popupContainer.style.display != "block")
    	{
    		window.SupportMemberSelector.show();
    	}            
    }
    
    function addMember(u)
    {
        arMembers = u;       
    }
       
<?
endif;
if($arParams['ALLOW']):
?>
    function deleteMessage(id)
    {
        if(id>0)
        {
            BX.ajax({
                url:supportVar.CURRENT_URL,
                data:{"AJAX_CALL" : "Y","ajax_support_message_delete": "Y","delete_support_message_id" : id,"sessid":supportVar.bsmsessid},
                dataType: 'json',
                method: 'POST',
                onsuccess: function(json)
                {
                    if(json.status)
                    {
                        BX('message'+id).remove();
                    }
                },
                onfailure: function()
                {
                    alert('oops :(');
                }
            }
            );
        }    
    }
<?endif;
global $USER;
if($arParams['ALLOW'] || $arParams['IS_SUPPORT_TEAM']):
?>    
    function editMessage(id)
    {
        if(id>0)
        {
            BX.ajax({
                url:supportVar.CURRENT_URL,
                data:{"SUPPORT_AJAX" : "Y","edit_support_message_id": escape(id),"ajax_support_message_edit" : 'Y','check_support_message_edit':'Y',"sessid":supportVar.bsmsessid},
                dataType: 'json',
                method: 'POST',
                onsuccess: function(json)
                {
                    if(json.result)
                    {
                        window.oMessageEditDialog = wred = new BX.CDialog({
                        	content_url: supportVar.CURRENT_URL,
                            content_post: "edit_support_message_id="+escape(id)+"&ajax_support_message_edit=Y&AJAX_CALL=Y&sessid="+supportVar.bsmsessid,
                        	width: 800,
                        	height: 400,
                        	min_height: 300,
                        	min_width: 800,
                        	resizable: true
                        });
                	    window.oMessageEditDialog.Show();
                        //onWindowClose
                        BX.addCustomEvent(BX.WindowManager.Get(),'onWindowRegister',function () {
                            if (window.JCLightHTMLEditor !== undefined)
                            {
                                if (JCLightHTMLEditor.items['MESSAGE_e'] !== undefined)            
                                        delete JCLightHTMLEditor.items['MESSAGE_e'];
                            }            
                        });
                        BX.addCustomEvent(window, 'LHE_OnInit', function(obj){obj.SetEditorContent(obj.content);obj.SetContent(obj.content);});            
                    }
                    else
                    {
                        console.warn('error');
                    }
                },
            }
            );            
        }    
    }
<?
endif;
if($arParams['ALLOW']):
?>    
    function newTicketMessage(id)
    {
        if(id>0)
        {
            location.href = '<?=$arParams["CREATE_BY_MESSAGE_URL"]?>'+id;
        }
    }
    
    function OnSelect_changeDeal(v)
    {
        id = v[0].ID;
        if(id>0)
        {
            BX.ajax({
                url:supportVar.CURRENT_URL,
                data:{"AJAX_CALL" : "Y","AJAX_ACTION": "SET_DEAL","DEAL_ID" : id,"sessid":supportVar.bsmsessid},
                dataType: 'json',
                method: 'POST',
                onsuccess: function(json)
                {
                    if(json.status)
                    {
                        BX('ticket-deal').innerHTML = v[0].NAME;
                    }
                },
                onfailure: function()
                {
                    alert('oops :(');
                }
            }
            );            
        }
    }    
<?endif;?>

</script>                                                