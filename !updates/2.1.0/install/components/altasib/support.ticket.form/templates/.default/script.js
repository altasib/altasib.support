function CAltasibSupportForm()
{
    this.create = function()
    {
        window['BXHtmlEditor'].Get('MESSAGE').SaveContent();
        $('#ticket_add').submit();
    }
    this.Send = function()
    { 
          window["BXHtmlEditor"].Get('MESSAGE').SaveContent();
          BX.removeCustomEvent(window["BXHtmlEditor"].Get('MESSAGE'),'OnCtrlEnter',alSupForm.Send);
          if($('#TICKET_ID').val()>0)
          {
            formData = $('#ticket_add').serialize() + "&SUPPORT_AJAX=Y&t_submit=Y";
            showW = BX.showWait(BX('ticket_add'));
            BX.ajax({
                url:supportVar.CURRENT_URL,
                data:formData,
                dataType: 'json',
                method: 'POST',
                onsuccess: function(data)
                {                               
                    if(data.status)
                    {
                        window.onbeforeunload = function (e){};
                        if(data.redirect)
                        {
                            $('#ticket_add').hide();
                            window.location.href = data.redirect_url;
                        }
                        else
                        {  
                            if(window["BXHtmlEditor"].Get('MESSAGE'))
                            {
                                window["BXHtmlEditor"].Get('MESSAGE').SetContent('');
                            }

                            if(BX('divSupportFormShowNote')!=null)
                                $('#divSupportFormShowNote').show();
                                
                            if(BX('divSupportFormShow')!=null)
                                $('#divSupportFormShow').hide();
                                
                            $('#errors').hide();
                            
                            if($('#NOT_CHANGE')!=null)
                            {
                                $('#NOT_CHANGE').removeAttr("checked");
                                $('#IS_HIDDEN').removeAttr("checked");
								$('#ticketn').removeClass('hidden-mess-form');
                            }
                            
                            fileIds = $('[name="FILES[]"]');
                            for (var i = 0; i < fileIds.length; i++) {
                                $('#wd-doc'+fileIds[i].value).remove();
                                $('#file-doc'+fileIds[i].value).remove();
                            }

                            fileEl = BX.findChildren(BX('ticketn'),{className:"saved"},true);
                            for(var i = 0; i < fileEl.length; i++)
                            {
                                BX.remove(fileEl[i]);
                            }
                            
                            setTimeout( function(){
                                BX('message'+data.messageId).style.backgroundColor = 'transparent';
                                },5000);
                        }
                    }
                    else
                    {
                        $('#errors').html(data.error);
                        $('#errors').show();
                    }
                    BX.closeWait(BX('ticket_add'),showW);
                },
                onfailure: function()
                {
                    alert('session expire. reload page');
                }
            }
            );                
          }
          else
          {
            document.forms["ticket_add"].submit();
          }        
    }
    this.showFrom = function(e)
    {
        if($('#divSupportFormShow').css('display')=='none')
        {
            div = BX('divSupportFormShow');
            /*BX.show(BX('cancel-form'));*/
			$('#cancel-form').show();
            BX.hide(BX('divSupportFormShowNote'));
                BX.adjust(div, {style:{display:"block", overflow:"hidden", height:"20px", opacity:0.1}});
    			(new BX.easing({
    				duration : 200,
    				start : { opacity : 10, height : 20 },
    				finish : { opacity: 100, height : div.scrollHeight},
    				transition : BX.easing.makeEaseOut(BX.easing.transitions.quad),
    				step : function(state) {
    					div.style.height = state.height + "px";
    					div.style.opacity = state.opacity / 100; 
                    },
    				complete : BX.proxy(function(){
                        div.style.height = 'auto';
                        window["BXHtmlEditor"].Get('MESSAGE').Focus();
                        BX.addCustomEvent(window["BXHtmlEditor"].Get('MESSAGE'),'OnCtrlEnter',alSupForm.Send);    
    				}, this)
    			})).animate();
                
            window.onbeforeunload = function (e) {
                var e = e || window.event; 
                if (e) 
                    e.returnValue = ''; 
            return ''; 
            }
        }        
    }
    
}

var alSupForm = new CAltasibSupportForm();

function altasibSelectGroup (group)
{
	if (!!group && group.length > 0)
	{
		group = group[0];
		$('#support_ticket_group').html(BX.util.htmlspecialchars(group.title) + '<input type="hidden" name="GROUP_ID" value="'+group.id+'" />');
	}
	else
        $('#support_ticket_group').html('');
}

function sendSupportMessage()
{
    alert(sendSupportMessage);
}

BX.ready(function () {
    
    if(supportVar.TICKET_ID==0)
    {
        $(".owner_id").chosen();
        $("#OWNER_ID").chosen().change(function (event, params){
			AltasibSupportReport.ProjectsFromForm.SelectedUser = params.selected;
			
		//OWNER_ID
		/*BX.ajax({
                url:'/local/components/altasib/support/ajax.php',
                data:{"AJAX_CALL" : "Y","AJAX_ACTION": "getProjectList","sessid":supportVar.bsmsessid,'USER_ID':params.selected},
                dataType: 'json',
                method: 'POST',
                onsuccess: function(data)
      			{
      			   if(!data.error)
                   {
                        $("#PROJECT_ID").empty();
                        $.each(data.list, function (i, item) {
                            $('#PROJECT_ID').append($('<option>', { 
                                value: item.projectId,
                                text : item.value 
                            }));
                        });
                        $("#PROJECT_ID").trigger("chosen:updated");
                        console.log('ok')
                   }
                },
                onfailure: function()
                {
        	
                }
            }
            );   */         
            
        });
    }    
    BX.bind(BX('altasib-support-submit-form'),'click',function(e){
        e.preventDefault();
        alSupForm.Send();
    });
    BX.bind(BX('altasib-support-submit-form-and-go'),'click',function(e){
        e.preventDefault();
        $('#t_submit_go').val('Y');
        alSupForm.Send();
    });
    
    BX.addCustomEvent("uploadStart", BX.delegate(function(data){
        if(BX('t_submit_href'))
            BX('t_submit_href').style.display = 'none';
    }, this));

    BX.addCustomEvent("uploadFinish", BX.delegate(function(data){
        if(BX('t_submit_href'))
            BX('t_submit_href').style.display = 'inline';
    }, this));
    
    BX.bind(BX('cancel-form'),'click', function (e){
		e.preventDefault();
        BX.show(BX('divSupportFormShowNote'));
        BX.hide(BX('divSupportFormShow'));
        BX.hide(BX('errors'));
        if(BX('NOT_CHANGE')!=null)
        {
            BX('NOT_CHANGE').checked = '';
            BX('IS_HIDDEN').checked = '';
        }
        if(window["BXHtmlEditor"].Get('MESSAGE'))
        {
            window["BXHtmlEditor"].Get('MESSAGE').SetContent('');
        }
        BX.hide(BX('cancel-form'));
        window.onbeforeunload = function (e){};                
    });
    BX.bind(BX('close-ticket'),'click', function (e){
    });

    BX.bind(BX('divSupportFormShowNote'),'click', function (e){
        alSupForm.showFrom(e);
    });
    BX.bind(BX('IS_HIDDEN'),'click', function (e){
		$('#ticketn').toggleClass('hidden-mess-form');
        /*if(BX('IS_HIDDEN').checked)
            BX('ticketn').style.backgroundColor = 'antiquewhite';
        else
            BX('ticketn').style.backgroundColor = '#eef2f4';*/
    });
    
//BX.addCustomEvent(window, "OnEditorBaseControlsDefined", function(e){
    //BX.addCustomEvent(window, "OnEditorInitedBefore", function(e){
        
/*
            e.push({
                id:'quickAnswer',
                compact:true,
                hidden:false,
                sort:10,
                action: function (ee){console.log(ee)}
            });
*/        
     /*   BX.addCustomEvent(window, "GetControlsMap", function(e){

    console.log(11);
    console.log(e);    
});*/            


//BX.addCustomEvent(window, "OnEditorInitedAfter", function(e){
/*BX.addCustomEvent(window, "GetTopButtons", function(e){
    //window.BXHtmlEditor.Controls.quickAnswer = quickAnswerButton
    
    var QAbut = function (editor,wrap)
    {
        this.id = 'quickAnswer';
        this.title = 'QA...';
        this.className = 'bxhtmled-top-bar-btn bxhtmled-button-bbcode';
        this.action = 'qa';
        this.create();
    }
    //BX.extend(QAbut,window.BXHtmlEditor.Button);
    //window.BXHtmlEditor.Controls.quickAnswer = QAbut;
    console.log(e);
}
);*/

function quickAnswerButton()
{
    var but = function (editor,wrap)
    {
        this.id = 'quickAnswer';
        this.title = 'QA';
        this.className = 'bxhtmled-top-bar-btn bxhtmled-button-bbcode';
        this.action = 'alert(1)';
        this.create();
    }
    BX.extend(but,window.BXHtmlEditor.Button);
    window.BXHtmlEditor.Controls[quickAnswer]  = but;
}
});