<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script type="text/javascript">
<?if($arParams['HAVE_CHANGE_CATEGORY']):?>
    function changeCaregory(id)
    {
        AltasibSupport.PopupMenu.Wait(BX('category-info-'+id), 'support-change-category-popup');
		BX.ajax({
            url:supportVar.CURRENT_URL,
            data:{"AJAX_CALL" : "Y","AJAX_ACTION": "CATEGORY","CID" : id,"sessid":supportVar.bsmsessid},
            dataType: 'json',
            method: 'POST',
            onsuccess: function(data)
            {
                if(data.status)
                {
                    BX('support-change-category').innerHTML = BX('category-info-'+id).innerHTML;
                    AltasibSupport.PopupMenu.MenuItemSel(BX('category-info-'+id), 'support-change-category-popup');
                }
                else
                    alert(data.error);
            },
            onfailure: function()
            {
            }
        }
        );                           
    }
<?endif;?>
<?if($arParams['HAVE_CHANGE_PRIORITY']):?>
    function changePriority(id)
    {
        AltasibSupport.PopupMenu.Wait(BX('priority-info-'+id), 'support-change-priority-popup');
		BX.ajax({
            url:supportVar.CURRENT_URL,
            data:{"AJAX_CALL" : "Y","AJAX_ACTION": "PRIORITY","CID" : id,"sessid":supportVar.bsmsessid},
            dataType: 'json',
            method: 'POST',
            onsuccess: function(data)
            {
                if(data.status)
                {
                    BX('support-change-priority').innerHTML = BX('priority-info-'+id).innerHTML;
					AltasibSupport.PopupMenu.MenuItemSel(BX('priority-info-'+id), 'support-change-priority-popup');
                }
                else
                    alert(data.error);
            },
            onfailure: function()
            {
            }
        }
        );                                    
    }

<?endif;?>
<?if($arParams['HAVE_CHANGE_STATUS']):?>
    function changeStatus(id)
    {
        AltasibSupport.PopupMenu.Wait(BX('status-info-'+id), 'support-change-status-popup');
		BX.ajax({
            url:supportVar.CURRENT_URL,
            data:{"AJAX_CALL" : "Y","AJAX_ACTION": "STATUS","CID" : id,"sessid":supportVar.bsmsessid},
            dataType: 'json',
            method: 'POST',
            onsuccess: function(data)
            {
                if(data.status)
                {
                    BX('support-change-status').innerHTML = BX('status-info-'+id).innerHTML;
                    //BX.hide(BX('status-popup'));
					AltasibSupport.PopupMenu.MenuItemSel(BX('status-info-'+id), 'support-change-status-popup');
                }
                else
                    alert(data.error);
            },
            onfailure: function()
            {
            }
        }
        );                                    
    }

<?endif;?>
<?if($arParams['ALLOW'] && \Bitrix\Main\Loader::includeModule("altasib.supportreport")):?>
/*
function supportGetListProject()
{
	BX.ajax({
        url:supportVar.CURRENT_URL,
        data:{"AJAX_CALL" : "Y","AJAX_ACTION": "GET_LIST_PROJECT","sessid":supportVar.bsmsessid},
        dataType: 'json',
        method: 'POST',
        onsuccess: function(data)
			{
			select = BX('SEL_PROJECT_ID_ST');
			var list = "";
			select.innerHTML = '';
			select.options[0] = new Option('', '');

			for (var j = 0, option; j < data.list.length; j++) {
				select.options[j+1] = new Option(data.list[j].value, data.list[j].projectId);
			}

			if(BX('SEL_PROJECT_ID_ST_chosen'))
			{	
				$('.sel_project_id_st').trigger('chosen:updated');
			}	
			else
			{
				$('.sel_project_id_st').chosen({
					no_results_text: 'Проект не найден. <a href="" onClick="AltasibSupportReport.Projects.PromptAddProject()">Добавить новый?</a>',
					allow_single_deselect: true,
					search_contains: true
				});
			}	
        },
        onfailure: function()
        {
	
        }
    }
    );    
}
function supportCreateProject(name)
{
	BX.ajax({
        url:supportVar.CURRENT_URL,
        data:{"AJAX_CALL" : "Y","AJAX_ACTION": "CREATE_PROJECT","projectName" : name,"sessid":supportVar.bsmsessid},
        dataType: 'json',
        method: 'POST',
        onsuccess: function(data)
        {
            if(data.result)
            {
//                $('#support-change-project').html(name);
//                $('#change-project-inp').hide();
//                $('#support-change-project').show();
//                $('#project_id').val('');
//                $('.autocomplete-no-suggestion').remove();

		
            }
        },
        onfailure: function()
        {
        }
    }
    );    
}*/
<?endif;?>
</script>