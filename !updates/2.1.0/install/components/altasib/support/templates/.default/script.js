BX.altasibSupport = {'selectedText': ''};
// ****************************************************** init BX.reaady
BX.ready(function() {
	AltasibSupport.Content.MoreButton (0);
	$('body').keydown(function(eventObject){
		if (eventObject.which == 27)
			{
			if (AltasibSupport.PopupMenu.currentOpenMenu)
				AltasibSupport.PopupMenu.Close(AltasibSupport.PopupMenu.currentOpenMenu);
			if (AltasibSupport.PopupWindow.currentOpenWindow)
				AltasibSupport.PopupWindow.Close(AltasibSupport.PopupWindow.currentOpenWindow);				
			if(AltasibSupport.CommentTicket.openNow)
				AltasibSupport.CommentTicket.CloseEdit();
			}
	});
	$('body').click(function(e){
			if (AltasibSupport.PopupMenu.currentOpenMenu)
				if(AltasibSupport.PopupMenu.currentOpenMenu != 'support-change-qa-popup')
					if ($(e.target).parents().filter('#'+AltasibSupport.PopupMenu.currentOpenMenu).length != 1) 
						AltasibSupport.PopupMenu.Close(AltasibSupport.PopupMenu.currentOpenMenu);	
	});
	$(window).resize(function(){
		AltasibSupport.PopupWindow.OnResizeDucument();
	});	
	$('#support-change-category').on('click',function(e){
		AltasibSupport.PopupMenu.Show(this);
		return false
		});	
	$('#support-change-priority').on('click',function(e){
		AltasibSupport.PopupMenu.Show(this);
		return false
		});	
	$('#support-change-status').on('click',function(e){
		AltasibSupport.PopupMenu.Show(this);
		return false
		});
	$('#al-sup-load-mess').on('click',function(e){
		e.preventDefault();
		alSupDetail.loadMessages();
		});
	$('#al-sup-load-mess-all').on('click',function(e){
		e.preventDefault();
		alSupDetail.loadMessages(1);
		});		

    $('#altasib-support-ticket-comment-click').on('click',function(e){
		$('#altasib-support-ticket-comment-click').hide(200);
		AltasibSupport.CommentTicket.EditComment();
		});
	
    $('#add-member-choice').on('click',function(e){
        e.preventDefault();
        showMemberSelector(this);
    });	
    $('#altasib_supportToggleBar').on('click',function(e){
        e.preventDefault();
        BX.altasibSupport.ToggleBar();
		
    });

    $('.feed-com-text-inner-inner').on("mouseup", function(e){
		AltasibSupport.Content.Quote(e);
    });	
	
    $('#support-change-project').on('click',function(e){
        e.preventDefault();
		/*AltasibSupport.PopupWindow.Show(this);*/
        /*$('#support-change-project-tr').show();*/
		AltasibSupport.Bar.ToggleBlock('support-change-project-tr');
        /*$('#support-change-project').hide();*/
        $('#project_id').focus();
    });    	

	BX('altasib-sidebar-float-block').onmouseover = function() {AltasibSupport.Bar.Hover_showButton();}
	BX('altasib-sidebar-float-block').onmouseout = function() {AltasibSupport.Bar.Hover_hideButton();}
	BX('altasib_sidebar_resizer').onmouseover = function() {AltasibSupport.Bar.Hover_showButton();}
	BX('altasib_sidebar_resizer').onmouseout = function() {AltasibSupport.Bar.Hover_hideButton();}

	AltasibSupport.PopupWindow.Init();
	
	BX.bind(window, "scroll", AltasibSupport.Bar.Scroll);					

	$('#altasib-sidebar-float-block').width('260px');
	$('#altasib_sidebar').width('260px');

	

	AltasibSupport.Bar.Scroll();
	AltasibSupport.Content.Highlight();
		
    var ticketOnline = {};
    BX.addCustomEvent("onPullEvent-altasib.support", BX.delegate(function(command,params){
        if(supportVar.TICKET_ID == params['TICKET_ID'])
        {
            if(command == 'showview')
            {
                if(BX('ticketOnlineBlock'))
                {
                    if(!ticketOnline[params['ID']])
                    {
                        ticketOnline[params['ID']] = params['ID'];
                        BX('ticketOnlineBlock').style.display = 'block';
                        BX('ticketOnline').innerHTML += params['SHORT_NAME']+' ';
                    }
                }
            }
            if(command == 'message')
            {
               /* BX.show(BX('tr-support-desc'),'table-row');*/
                //BX.show(BX('tr-support-mess-list'),'table-row');
                $('#support-messages').append(params['MESSAGE']);
                BX('message'+params['ID']).style.backgroundColor = 'cornsilk';
                BX.bind(BX('message'+params['ID']), "mouseover", function(){
                        setTimeout( function(){
                            BX('message'+params['ID']).style.backgroundColor = 'transparent';
                        },2000)
                    }
                );
            }
            
            if(command == 'close')
            {
                $('#ticketn').hide();
                $('#supportFormNote').hide();
            }
            if(command == 'open')
            {
                $('#ticketn').show();
                $('#supportFormNote').show();
            }            
        }
    }, this));

    BX.viewElementBind(
        BX('support-detail-main'),
          {showTitle: true, lockScroll: false},
          function(node){
             return BX.type.isElementNode(node) && (node.getAttribute('data-bx-viewer') || node.getAttribute('data-bx-image'));
          }    
    );   


    alSupDetail.updateS();
    setInterval(function (){
        alSupDetail.updateS();
    },90000
    );	
	
});

// *******************************************************************************************

function CAltasibSupportDetail()
{
    var messPage = 2;
    this.loadFull = 'N';
    
    this.loadMessages = function(all)
    {
        if (all > 0)
			this.loadFull = 'Y';
		if(messPage>4)
            this.loadFull = 'Y';
        BX.ajax({
            url:supportVar.CURRENT_URL,
            data:{"AJAX_CALL" : "Y","PART_LOAD": "Y",'page':messPage,"sessid":supportVar.bsmsessid,'LOAD_FULL':this.loadFull},
            dataType: 'json',
            method: 'POST',
            onsuccess: function(result)
            {
                if(result.status)
                {
                    html = result.html+$('#support-messages').html();
                    if(messPage>4)
                        html = result.html;
                        
                    $('#support-messages:first').html(html);
                    
                    if(messPage==4)
                        $('#sup-loadm').html(BX.message("SUPPORT_LOAD_MESSAGE_ALL"));
                    if(!result.end)
                    {
                        messPage++;
                        $('#sup-loadm-cnt').html(result.leftCnt);
                    }
                    else
                    {
                        $('#support-load-line').remove();
						$('div').removeClass( "dashed-bottom" );											
                    }
					AltasibSupport.Content.Highlight();						
					BX.closeWait(BX('support-messages'),show);	
                }
            },
            onfailure: function()
            {
            }
        }
        );            
    }
    
    this.getSelectedText = function()
    {
        if (window.getSelection){
            selectedText = window.getSelection();
        }
        else if (document.getSelection){
            selectedText = document.getSelection();
        }
        else if (document.selection){
            selectedText = document.selection.createRange().text;
        }
        else return; 
    return selectedText;       
    }
    
    this.updateS = function()
    {
        BX.ajax({
            url:'/local/components/altasib/support/ajax.php',
            data:{"AJAX_CALL" : "Y","AJAX_ACTION": "update-s","sessid":supportVar.bsmsessid,'TICKET_ID':supportVar.TICKET_ID},
            dataType: 'json',
            method: 'POST',
            onsuccess: function(result)
            {
                if(result.exp)
                {
                    BX('sessid').value = result.sessid;
                    supportVar.bsmsessid = result.sessid;
                }
            },
            onfailure: function()
            {
                //alert('oops :(');
            }
        }
        );        
    }
    
    this.messageToFav = function(messageId)
    {
        if($('#message'+messageId).hasClass('post-fav'))
		{
			this.messageDelFav(messageId);
			$('#message'+messageId).removeClass('post-fav');
			return;			
		}
		BX.ajax({
            url:supportVar.CURRENT_URL,
            data:{"AJAX_CALL" : "Y","AJAX_ACTION": "ADD_FAVORITE","sessid":supportVar.bsmsessid,'MESSAGE_ID':messageId},
            dataType: 'json',
            method: 'POST',
            onsuccess: function(data)
            {
                if(data.result)
                {
					$('#message'+messageId).addClass('post-fav');
                }
            },
            onfailure: function()   {  }
        }
        );        
    }
    this.messageDelFav = function(messageId)
    {
        BX.ajax({
            url:supportVar.CURRENT_URL,
            data:{"AJAX_CALL" : "Y","AJAX_ACTION": "DEL_FAVORITE","sessid":supportVar.bsmsessid,'MESSAGE_ID':messageId},
            dataType: 'json',
            method: 'POST',
            onsuccess: function(data)
            {
                if(data.result)
                {
                    $('#message'+messageId+'-fav').remove();
                }
            },
            onfailure: function()   {  }
        }
        );        
    }    
}
var alSupDetail = new CAltasibSupportDetail();

function supportChangeGroup(group)
{	
	if (!!group && group.length > 0)
	{
		group = group[0];
		BX('support_ticket_group', true).innerHTML = BX.util.htmlspecialchars(group.title);
        
        BX.ajax({
            url:supportVar.CURRENT_URL,
            data:{"AJAX_CALL" : "Y","AJAX_ACTION": "SET_GROUP","GROUP_ID" : group.id,"sessid":supportVar.bsmsessid},
            dataType: 'json',
            method: 'POST',
            onsuccess: function(json)
            {
                if(json.status)
                {
                    BX.remove(BX('support_ticket_group_selector'));
                }
            },
            onfailure: function()
            {
                alert('oops :(');
            }
        }
        );            
	}
	else
	{
		BX('support_ticket_group', true).innerHTML = '';
	}
}
	

function showAltasibTicketPost(id, source)
{
	var el = BX.findChild(BX('altasib_text_mess_td_' + id), {className: 'altasib_text_div'}, true, false);
	el2 = BX.findChild(BX('altasib_text_mess_td_' + id), {className: 'altasib_text_div_inner'}, true, false);
	BX.remove(source);

	if(el)
	{
		var fxStart = 300;
		var fxFinish = el2.offsetHeight;
		(new BX.fx({
			time: 1.0 * (fxFinish - fxStart) / (1200-fxStart),
			step: 0.05,
			type: 'linear',
			start: fxStart,
			finish: fxFinish,
			callback: BX.delegate(__AltasibticketExpandSetHeight, el),
			callback_complete: BX.delegate(function() { this.style.maxHeight = 'none'; }, el)
		})).start();
	}
}							
function __AltasibticketExpandSetHeight(height)
{
	this.style.maxHeight = height + 'px';
}	



// *****************************  AltasibSupport ********************

var AltasibSupport = new Object();
AltasibSupport.PopupWindow = { // ************ AltasibSupport.PopupWindow
	currentOpenWindow : false,
	WindowButtonSave : false,
	Show : function(idWindow) 
		{
			if (!idWindow)
				return false;

			this.ToggleWindow (idWindow);
			
			if (AltasibSupport.PopupWindow.currentOpenWindow)
			{	
		
			}
			

		},
	Close: function() //AltasibSupport.PopupWindow.Close
		{
		if(AltasibSupport.PopupWindow.currentOpenWindow)
			idWindow = AltasibSupport.PopupWindow.currentOpenWindow;
		else
			return;
		AltasibSupport.PopupWindow.currentOpenWindow = false;

		/*marginTopWindow = parseInt($('#'+idWindow).css('marginTop'))+15;*/

		//$('#'+idWindow + ' .win_popup').animate({
		$('.win_popup').animate({
				opacity: 0,	
				marginTop: 0 + 'px'
			  }, 150, function(){
					AltasibSupport.PopupWindow.WindowButtonSaveRemove();
					AltasibSupport.PopupWindow.WindowButtonCancelRemove();					
					
					$('#altasib-projects-title-win').html('');
					$('#shadow-popup').fadeOut(200).addClass('hidden-el');
					$('#'+idWindow).append($('#win_popup .win_content'));
					}).hide('1500');	

		},
		
	ToggleWindow: function (idWindow)	//AltasibSupport.PopupWindow.ToggleWindow
		{

			if (!idWindow || idWindow == AltasibSupport.PopupWindow.currentOpenWindow)
			{
				AltasibSupport.PopupWindow.Close();
				return false;
			}
			else
			{
				if($('#'+idWindow).attr('title'))
				{
					$('#altasib-projects-title-win').html($('#'+idWindow).attr('title'));
				}
				if (AltasibSupport.PopupWindow.currentOpenWindow)
					{
					AltasibSupport.PopupWindow.Close();
					}			
				$('.win_popup').css('marginLeft', Math.max(10, parseInt($(window).width()/2 - $(' .win_popup').width()/2)) + 'px');
				$('.win_popup').append( $('#'+idWindow + ' .win_content'));
				$('.win_popup').show().animate({
						opacity: 1,	
						marginTop: '-40px',
					  }, 150 );			
			
				if($.browser.msie) 
					$('#shadow-popup').height($(document).height()).toggleClass('hidden-el')
				else  
					$('#shadow-popup').height($(document).height()).toggleClass('hidden-el').fadeTo(200, 0.2);
				//idWindow
			}
			this.currentOpenWindow = idWindow;
			this.WindowButtonCancelAdd(idWindow);
		},	
	WindowButtonCancelRemove: function ()
		{
			$('#altasib-window-button-cancel-projects').delay('1000').remove();
		},		
	WindowButtonCancelAdd: function ()
		{
			$("<a />", {
				id:'altasib-window-button-cancel-projects',
				class: 'altasib-support-button-cancel button-opacity0',
				text: AltasibSupport.textCancel,
				click: function(){
					AltasibSupport.PopupWindow.Close();
					/*$('#altasib-window-button-cancel-projects').delay('1000').remove();
					$('#altasib-support-button-save-projects').delay('1000').remove();
					AltasibSupport.PopupWindow.WindowButtonSave = false;		*/			
					},
			}).appendTo('.win_content').animate({opacity: 1}, 800 );
		},
	WindowButtonSaveRemove: function (funcResult) //AltasibSupport.PopupWindow.MenuButtonSaveAdd
		{
			$('#altasib-support-button-save-projects').delay('1000').remove();
			AltasibSupport.PopupWindow.WindowButtonSave = false;				
		},
	WindowButtonSaveAdd: function (funcResult) //AltasibSupport.PopupWindow.MenuButtonSaveAdd
		{
			if(AltasibSupport.PopupWindow.WindowButtonSave)return;
			$('#altasib-window-button-cancel-projects').delay('100').remove();
			$("<a />", {
				id:'altasib-support-button-save-projects',
				class: 'altasib-support-button-save button-opacity0',
				text: AltasibSupport.textSave,
				click: function(){
					AltasibSupport.PopupWindow.Close();
					/*$('#altasib-window-button-cancel-projects').delay('1000').remove();
					$('#altasib-support-button-save-projects').delay('1000').remove();
					AltasibSupport.PopupWindow.WindowButtonSave = false;*/
					funcResult();
					},
			}).appendTo('.win_content').animate({opacity: 1}, 800 );;		
			AltasibSupport.PopupWindow.WindowButtonSave = true;
			AltasibSupport.PopupWindow.WindowButtonCancelAdd();
		},		
	OnResizeDucument: function ()
		{
			if(this.currentOpenWindow)
				$("#"+this.currentOpenWindow + ' .win_popup').css('marginLeft', Math.max(10, parseInt($(window).width()/2 - $('#'+this.currentOpenWindow + ' .win_popup').width()/2)) + 'px');
		},		
	WaitLoaderWindow: function (el)
		{
			$("<div />", {
				id:'altasib_choose_item_wait',
				class: 'altasib_choose_item_wait',
				text: '',
			}).appendTo('#' + el).show(300);
		},
	WaitLoaderWindowHide: function ()
		{
			$('.altasib_choose_item_wait').remove();
		},
	ToggleBlock: function (el) //AltasibSupport.PopupWindow.ToggleBlock
	{
		/*if (ar)
		{
			$('.switch-ico').toggleClass(ar[0] + ' ' + ar[1]);
		}	*/
		var _display = ($(el).css('display')== 'none') ? 'block' : 'none';	

		$(el).animate({
				height: "toggle",
				opacity: "toggle",	
				display: _display
			  }, 200 );
	},		
	Init: function ()
		{
			$("<div />", { 
				id:'shadow-popup',
				class: '',
				text: '',
			}).appendTo('body');		
			$("<div />", { 
				id:'win_popup',
				class: 'win_popup',
				text: '',
			}).appendTo('body');	
			$("<div />", {
				id:'altasib-projects-title-win-container',
				class: 'altasib-projects-title-win-container',
				text: '',
			}).appendTo('.win_popup');			
			$("<div />", {
				id:'altasib-projects-title-win',
				class: 'altasib-projects-title-win',
				text: '',
			}).appendTo('.altasib-projects-title-win-container');

			$("#win_popup").draggable({
				handle: '#altasib-projects-title-win'
			});			
		
		}	
			
};	
	
AltasibSupport.PopupMenu = {
	currentOpenMenu : false,
	
	Show : function(bindElement) 
		{
			if (!bindElement)
				return false;

			Menu = BX(bindElement.id+'-popup');
			this.ToggleMenu (Menu.id, bindElement);
			
			if (AltasibSupport.PopupMenu.currentOpenMenu)
			{	
		
			}
			

		},
	Close: function(idMenu) 
		{
		AltasibSupport.PopupMenu.currentOpenMenu = false;

		marginTopMenu = parseInt($('#'+idMenu).css('marginTop'))+15;
			
		$("#"+idMenu).animate({
				opacity: 0,	
				marginTop: marginTopMenu + 'px'
			  }, 250, function(){
							$("#"+idMenu).hide(); 
							$('#' + idMenu + ' .altasib-support-button-cancel').remove();
							});	

		},
	MenuItemSel : function(idItem, idMenu) 
		{	
			this.WaitLoaderMenuHide();
			this.ToggleMenu(idMenu)
			return true;
		},
	Wait : function(idItem, idMenu) 
		{
			$(idItem).siblings().removeClass("choose_item_selected");
			$(idItem).addClass("choose_item_selected");	
			this.WaitLoaderMenu(idMenu);

		},
	ToggleMenu: function (idMenu, bindElement)	//AltasibSupport.PopupMenu.ToggleMenu
		{
		if (!bindElement || idMenu == AltasibSupport.PopupMenu.currentOpenMenu)
		{
			AltasibSupport.PopupMenu.Close(idMenu);
			return false;
		}
		else
		{
			if (AltasibSupport.PopupMenu.currentOpenMenu)
				{
				AltasibSupport.PopupMenu.Close(AltasibSupport.PopupMenu.currentOpenMenu);
				}			
			
			topPosMenu = bindElement.getBoundingClientRect().top;
			$("#"+idMenu).css({'opacity':'0'})
			$("#"+idMenu).show();
			if($("#"+idMenu).parents().filter('#sidebar-block').length == 1)
				{
					if(($(".colum-info").width() - $("#"+idMenu).outerWidth() - 25) < 0)
						$("#"+idMenu).css({'marginLeft': '-'+($('.colum-name').width()-15)+'px', 'width' : ($('.info-table').width() - 60) +'px' })
					else 
						$("#"+idMenu).css({'marginLeft': '-15px'})
				}
			else 
				{
					var pos = $('.bxhtmled-button-quick_answer').offset();
					$("#"+idMenu).css({'marginLeft': '0', 'min-width' : '300px'})
					$("#"+idMenu).offset({top:pos.top + 15, left:pos.left - 150})
				}			
			if((parseInt($('#'+idMenu).height()) + bindElement.getBoundingClientRect().bottom) > BX("altasib-sidebar-float-block").getBoundingClientRect().bottom)
				{
				//move up
				$('#'+idMenu).css('marginTop', '-' + ($('#'+idMenu).height() +20)+'px')
				marginTopMenu = '-'+($('#'+idMenu).height() +40)+'px';
				}		
			else
				{
				marginTopMenu = '-25px';
				}		
			$("#"+idMenu).animate({
					opacity: 1,	
					marginTop: marginTopMenu
				  }, 150 );
		
		}
		this.currentOpenMenu = idMenu;
		this.MenuButtonCancelAdd(Menu.id);
		},
	MenuButtonCancelAdd: function (idMenu)
		{
			$("<a />", {
				id:'altasib-menu-button-cancel',
				class: 'altasib-support-button-cancel',
				text: AltasibSupport.textCancel,
				click: function(){
					AltasibSupport.PopupMenu.Close(AltasibSupport.PopupMenu.currentOpenMenu);
					$('#altasib-menu-button-cancel').delay('1000').remove();
					},
			}).appendTo('#' + idMenu + ' .popup_menuItem');		
		},
	WaitLoaderMenu: function (idMenu)
		{
			$('#altasib-menu-button-cancel').animate({
				opacity: 0,	
				height: 0,
				lineHeight: 0
				}, 250, function(){$(idMenu + ' .altasib-support-button-cancel').remove()});
			
			$("<div />", {
				id:'altasib_choose_item_wait',
				class: 'altasib_choose_item_wait',
				text: '',
			}).appendTo('#' + idMenu + ' .popup_menuItem').show(300);
		},
	WaitLoaderMenuHide: function (idMenu)
		{
			$('.altasib_choose_item_wait').remove();
		}		
		
}
// **************************************************************************
AltasibSupport.Bar = {
	minWidthBar : 200,// min width bar
	oldMouseMove : 0,
	oldMouseUp : 0,
	lastScrollTop : "",
	isOpen : "",
	sidebarBlockHover : 0,
	sidebarFix : 0,
	supportMode : '',
	

	Resizer : function(e)
	{
		if(!this.isOpen)return;
		if (e == null) { e = window.event }
		if (e.preventDefault) {
			e.preventDefault();
		};

		bar_resizer = BX('altasib_sidebar')

		startX = e.clientX;
		bar_width = bar_resizer.offsetWidth;

		this.oldMouseMove = document.onmousemove;
		this.oldMouseUp = document.onmouseup;
		document.onmousemove = this.Resizer_moveHandler;
		document.onmouseup = this.Resizer_cleanup;
		return false;
	},

	Resizer_moveHandler : function (e)
	{
	  if (e == null) { e = window.event }
	  if (e.button<=1){
		console.log(bar_width)		  
		 curX=(bar_width-(e.clientX-startX));
		 if (curX < this.minWidthBar) curX = this.minWidthBar;
		 bar_resizer.style.width=curX+'px';
		 console.log('-'+bar_resizer.offsetWidth + ' +'+curX)	
		 BX('altasib-sidebar-float-block').style.width=curX+'px';
		 /*if(bar_resizer.offsetWidth != curX)
			 return;
		 BX('altasib-sidebar-float-block').style.width=curX+'px';
		 return false;*/
	  }
	},


	Resizer_cleanup : function (e) 
	{
	  document.onmousemove = this.oldMouseMove;
	  document.onmouseup = this.oldMouseUp;
	},
	
	ToggleBlock: function (id) //AltasibSupport.Bar.ToggleBlock
	{
	var _display = ($('#'+id).css('display')== 'none') ? 'block' : 'none';	
	$("#"+id).animate({
			height: "toggle",
			opacity: "toggle",	
			display: _display
		  }, 250 );
	},

	ShowButton : function() //AltasibSupport.Bar.ShowButton
	{
			if (!this.isOpen)
				sidebarRec = BX('altasib_sidebar').getBoundingClientRect();
			else
				sidebarRec = BX('altasib-sidebar-float-block').getBoundingClientRect();
			
			if (sidebarRec.top > 0) 
				{
				heightSidebar = $(window).height() - sidebarRec.top;
				topButton = heightSidebar/2  - 45 + sidebarRec.top;							
				}
				else 
					if (sidebarRec.bottom < $(window).height())	topButton = sidebarRec.bottom/2 - 45;
						else topButton = $(window).height()/2 - 45;										
				if (sidebarRec.top + sidebarRec.height < 60 || topButton - sidebarRec.top < 0)// || 0 > (sidebarRec.top - topButton) < 60
				{
				BX.hide(BX('sidebar-btn'));
				return;
				}						

			$('#sidebar-btn').css("top", topButton +  "px");
			BX.show(BX('sidebar-btn'));						
			// end show button -----------						
			
	
	},

	SidebarMin : function() //AltasibSupport.Bar.SidebarMin
	{

		if(this.isOpen)
		{	
			$('#altasib-sidebar-float-block').addClass('altasib-sidebar-float-block-hide');
			$('#altasib_sidebar_resizer').addClass('altasib_sidebar_resizer-close');
			$('#altasib_sidebar').addClass('altasib_sidebar-close');		
			$('#sidebar-btn').addClass('sidebar-btn-show');		

			BX.userOptions.save('support', 'right-sidebar', 'min', 'true');

			this.isOpen = false;
			BX.show(BX('sidebar-btn'));
		}
		else
		{
			$('#altasib-sidebar-float-block').width('250px');
			$('#altasib_sidebar').width('260px');
			$('#altasib-sidebar-float-block').removeClass('altasib-sidebar-float-block-hide');
			$('#altasib_sidebar_resizer').removeClass('altasib_sidebar_resizer-close');
			$('#altasib_sidebar').removeClass('altasib_sidebar-close');
			$('#sidebar-btn').removeClass('sidebar-btn-show');

			BX.userOptions.save('support', 'right-sidebar', 'min', 'false');
			
			this.isOpen = true;
			BX.hide(BX('sidebar-btn'));
		}

	},	
	
	Scroll : function() //AltasibSupport.Bar.Scroll
	{				
					
		var SupportDetailInfoRect = BX('support-detail-info').getBoundingClientRect();
		
		var sidebar = BX('altasib-sidebar-float-block');
		var altasib_sidebar = BX('altasib_sidebar');
		var altasib_sidebarRect = altasib_sidebar.getBoundingClientRect();
		var SidebarRect = sidebar.getBoundingClientRect();
		
		SupportCont = BX('support-detail-main');
		SupportContRect = SupportCont.getBoundingClientRect();
	
		if (AltasibSupport.Bar.sidebarBlockHover)
		{
			if (($(window).height() - SidebarRect.bottom > -1) && (AltasibSupport.Bar.lastScrollTop < $(window).scrollTop()) && (SupportContRect.bottom  > SidebarRect.bottom + 5))
			{
				sidebar.style.marginTop = 0;	
				
				sidebar.style.position = 'fixed';
				sidebar.style.top = $(window).height() -SidebarRect.height + 'px';
				AltasibSupport.Bar.sidebarFix = 1;
				
			}
			else
			if ((altasib_sidebarRect.top < 0 && SidebarRect.top > 4) && (AltasibSupport.Bar.lastScrollTop > $(window).scrollTop()))
			{
				
				sidebar.style.marginTop = 0;									
				sidebar.style.position = 'fixed';
				sidebar.style.top = '5px';
				AltasibSupport.Bar.sidebarFix = 1;
			} 
			
			else 	
			if (AltasibSupport.Bar.sidebarFix)
			{	
				AltasibSupport.Bar.sidebarFix = 0;
				sidebar.style.position = 'relative';
				
				if (SupportContRect.bottom  < SidebarRect.bottom + 5)
				{
					sidebar.style.top = 0;
					sidebar.style.marginTop = SupportContRect.height - SidebarRect.height +'px';
					AltasibSupport.Bar.supportMode = 'bottom';
				}
				else 
				if (SupportContRect.top > 0)
				{
					sidebar.style.top = 0;				
				}								
				else
				{
					
					var marginBar = Math.abs(altasib_sidebarRect.top) - Math.abs(SidebarRect.top)
					sidebar.style.marginTop = marginBar + 'px'
					sidebar.style.top = '5px';									
				}
			}
		}
		if (!AltasibSupport.Bar.sidebarBlockHover)
		{	
			if (SupportContRect.bottom  < SidebarRect.height + 5)	
			{
				if (AltasibSupport.Bar.supportMode != 'bottom')
				{
					AltasibSupport.Bar.sidebarFix = 0;
					sidebar.style.position = 'relative';
					sidebar.style.top = 0;
					var _height = SupportContRect.height - SidebarRect.height;
					if (_height > 0)
						sidebar.style.marginTop =  _height + 'px';
					AltasibSupport.Bar.supportMode = 'bottom';
				}	
			}
			else	
			{
				if(SupportContRect.top < 0)
				{  
					if ((!AltasibSupport.Bar.sidebarFix || SupportDetailInfoRect.top > 5)  && AltasibSupport.Bar.supportMode != 'float'){
						sidebar.style.marginTop = 0;									
						sidebar.style.position = 'fixed';
						sidebar.style.top = '5px';
						AltasibSupport.Bar.sidebarFix = 1;
						AltasibSupport.Bar.supportMode = 'float';
						}
				}
				else
				{
				
					if (AltasibSupport.Bar.sidebarFix){
						if (AltasibSupport.Bar.supportMode != 'top')
							{
							sidebar.style.position = 'relative';
							sidebar.style.top = 0;
							AltasibSupport.Bar.sidebarFix = 0;
							AltasibSupport.Bar.supportMode = 'top';
							}
					}
					
					else if (SupportDetailInfoRect.top - SupportContRect.top > 5)
					{
						sidebar.style.marginTop = 0;										
						sidebar.style.position = 'relative';
						sidebar.style.top = 0;
						AltasibSupport.Bar.sidebarFix = 1;	
					}
					
			
				}
			}				
		}
		AltasibSupport.Bar.lastScrollTop = $(window).scrollTop();

		if (!AltasibSupport.Bar.isOpen)
			AltasibSupport.Bar.ShowButton();
	},		
	Hover_showButton : function()
	{

		AltasibSupport.Bar.ShowButton();				
		if ($(window).height() < BX('sidebar-block').getBoundingClientRect().height)
			AltasibSupport.Bar.sidebarBlockHover = 1;
			
	},
	
	Hover_hideButton : function()
	{
		if (AltasibSupport.Bar.isOpen)BX.hide(BX('sidebar-btn'))
			AltasibSupport.Bar.sidebarBlockHover = 0;
		AltasibSupport.Bar.supportMode = '';
	}		

}
// ***************************************************************************
AltasibSupport.Content = {
	altasib_resizeCode : "",
	
	
	MoreButton : function (id)
	{
		
		el = BX.findChild(BX('altasib_text_mess_td_' + id), {className: 'altasib_text_div'}, true, false);
		el2 = BX.findChild(BX('altasib_text_mess_td_' + id), {className: 'altasib_text_div_inner'}, true, false);
		if (el2.offsetHeight > el.offsetHeight)		
			BX.show(BX('altasibTicketPostMoreButton_'+id));

	},	
	Highlight : function ()
	{	
		$('.code pre').each(function(i, block) {
			hljs.highlightBlock(block);
		});	
	},
	Quote : function(SelText)
	{

        BX.altasibSupport.selectedText = alSupDetail.getSelectedText().toString();
        
        if(BX.altasibSupport.selectedText.length>3)
        {
            $('#altasib-support-quote').remove();
            $("<a>", {
                  "id": "altasib-support-quote",
				  "class": "altasib-support-quote",
                  text: " ",
                  css: {
                        'left':SelText.pageX+20+'px', 
                        'top':SelText.pageY+'px'
                  },
                  click: function(){
                        alSupForm.showFrom();
                      window["BXHtmlEditor"].Get('MESSAGE').SetContent(window["BXHtmlEditor"].Get('MESSAGE').GetContent()+'\n[QUOTE]'+BX.altasibSupport.selectedText+'[/QUOTE]');
                  }
            }).appendTo("body");
        }
        else
            $('#altasib-support-quote').remove();	
	
	},	
	ResizeContent : function(widthStart)
		{
		return;
		}	
}


/* function for full scrin for phone - temp */

// ***********************************************************************************

AltasibSupport.SupportfullScreen = {
	flag : false,
	FullScreen : function ()
	{
		if(this.flag)
			{	
				$('#altasib-support-detail').animate({
						top: this.top,
						left: this.left,
						width: '100%'
					  }, 250, function(){
								$('#altasib-support-detail').removeClass('altasib-support-detail-fullScreen');		
							} );			
				
			}
			else
			{
				$('#altasib-support-detail').addClass('altasib-support-detail-fullScreen');
				this.top = $('#altasib-support-detail').css('top');
				this.left = $('#altasib-support-detail').css('left');



				$('#altasib-support-detail').animate({
						top: 0,
						left: 0,
						width: '100%'
					  }, 250 , function(){
									$("body,html").animate({"scrollTop":0},300);
							} );	
			}

		this.flag = !this.flag;	
	}
}

AltasibSupport.CommentTicket = {
	openNow : false,
    EditComment : function() //AltasibSupport.CommentTicket.EditComment
    {
        AltasibSupport.CommentTicket.openNow = true;	
		
		$("<textarea />", {
            id:'altasib-support-ticket-comment-edit-area',
			class: 'altasib_editCommentArea',
            text: $('#altasib-support-ticket-comment').html().replace(/<br>/g, "\n"),
        }).appendTo("#altasib-support-ticket-comment-edit");
		$('<a/>', {
            id:'altasib-support-button-save',            
			class:'altasib-support-button-save ButtonMin',
            text: AltasibSupport.textSave,
            click: function(){
				AltasibSupport.CommentTicket.WaitLoader('altasib-support-ticket-comment-edit');
                BX.ajax({
                    url:supportVar.CURRENT_URL,
                    data:{"AJAX_CALL" : "Y","AJAX_ACTION": "EDIT_COMMENT","TID" : supportVar.TICKET_ID,"sessid":supportVar.bsmsessid,'comment':$('#altasib-support-ticket-comment-edit-area').val()},
                    dataType: 'json',
                    method: 'POST',
                    onsuccess: function(json)
                    {
                        if(json.status)
                        {
                            $('#altasib-support-ticket-comment').html($('#altasib-support-ticket-comment-edit-area').val().replace(/\n/g, "<br>"));
							AltasibSupport.CommentTicket.WaitLoaderHide ();	
							AltasibSupport.CommentTicket.CloseEdit();
                        }
                    },
                    onfailure: function()
                    {
                        alert('oops :(');
                    }
                }
                );                
            }
        }).appendTo('#altasib-support-ticket-comment-edit');
		$('<a/>', {
            class:'altasib-support-button-cancel ButtonMin',
			id:'altasib-support-button-cancel',
            text: AltasibSupport.textCancel,
            click: function(){
				AltasibSupport.CommentTicket.CloseEdit();
            }
        }).appendTo('#altasib-support-ticket-comment-edit');		

		$('#altasib-support-ticket-comment-edit-area').height($('#altasib-support-ticket-comment').height());
		$('#altasib-support-ticket-comment').hide();
		AltasibSupport.Bar.ToggleBlock('altasib-support-ticket-comment-edit');		
    },
	CloseEdit : function()
	{
		AltasibSupport.Bar.ToggleBlock('altasib-support-ticket-comment-edit-area');
		AltasibSupport.Bar.ToggleBlock('altasib-support-ticket-comment');
		AltasibSupport.Bar.ToggleBlock('altasib-support-ticket-comment-edit');
	
		$('#altasib-support-button-save').remove();				
		$('#altasib-support-button-cancel').remove();	
		
		$('#altasib-support-ticket-comment-edit-area').remove();
		$('#altasib-support-ticket-comment-click').show(300);
		AltasibSupport.CommentTicket.openNow = false;
		//$('body').off('keydown');
	},
	WaitLoader: function (idBlock)
		{
			$('#altasib-support-button-save').animate({
				opacity: 0,	
				height: 0,
				lineHeight: 0
				}, 250, function(){$('#altasib-support-button-save').remove()});
			$('#altasib-support-button-cancel').animate({
				opacity: 0,	
				height: 0,
				lineHeight: 0
				}, 250, function(){$('#altasib-support-button-cancel').remove()});			
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