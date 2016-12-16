<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Evgeniy Pedan                    #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2014 ALTASIB             #
#################################################
?>
<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

if(!Main\Loader::includeModule("altasib.support"))
    return;

use ALTASIB\Support;
Loc::loadMessages(__FILE__);

$UserRight = $APPLICATION->GetUserRight("altasib.support");

if($UserRight < "W")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$request = Main\Context::getCurrent()->getRequest();

$errors = array();
$USER_ID = intval($request["USER_ID"]);
$WORKER_USER_ID = intval($request["WORKER_USER_ID"]);

$aTabs = array(
    array("DIV" => "main",       "TAB" => Loc::getMessage("ALTASIB_SUPPORT_CONN_CLIENT2CLIENTWORKER_TAB_MAIN"),       "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_CONN_CLIENT2CLIENTWORKER_TAB_MAIN_TITLE"))
);
$tabControl = new CAdminForm("altasib_support_client2clientworker_edit", $aTabs);

if($request->isPost() && ($request["save"] <> '' || $request["apply"] <> '') && check_bitrix_sessid())
{
    $data = Support\CategoryTable::getList();
    while($category = $data->fetch())
    {
        $arFields = Array(
            'CATEGORY_ID' => $category['ID'],
            "USER_ID"=>$request['USER_ID'],
            "WORKER_USER_ID"=>$request['WORKER_USER_ID'],
            "R_VIEW"=>$request['R_VIEW'][$category['ID']],
            "R_ANSWER"=>$request['R_ANSWER'][$category['ID']],
            "R_CREATE"=>$request['R_CREATE'][$category['ID']],
        );
        $IDs = (int)$request['ID'][$category['ID']];
            
        if($IDs>0 && Support\C2CWTable::getById($IDs)->fetch())
        {
            $result = Support\C2CWTable::update($IDs, $arFields);
        }
        else
        {
            $result = Support\C2CWTable::add($arFields);
        }
        
    	if(!$result->isSuccess())
    		$errors = $result->getErrorMessages();
    }

	if(empty($errors))
	{
		if($request["save"] <> '')
			LocalRedirect(BX_ROOT."/admin/altasib_support_client2clientworker.php?lang=".LANGUAGE_ID);
		else
			LocalRedirect(BX_ROOT."/admin/altasib_support_client2clientworker_edit.php?lang=".LANGUAGE_ID."&USER_ID=".$USER_ID."&WORKER_USER_ID=".$WORKER_USER_ID."&".$tabControl->ActiveTabParam());
	}
	else
	{
		$errors = $result->getErrorMessages();
	}
}

if(empty($errors))
{
	$c2w = array();
	if($USER_ID > 0 && $WORKER_USER_ID>0)
	{
		$data = Support\C2CWTable::getList(array(
            'filter' => array(
                'USER_ID' => $USER_ID,
                'WORKER_USER_ID' => $WORKER_USER_ID,
            )
        ));
        
        $c2wIDs = array();
        while($dataC2W = $data->fetch())
        {
            $c2wIDs[$dataC2W['CATEGORY_ID']] = $dataC2W['ID'];
            $c2w['USER_ID'] = $dataC2W['USER_ID'];
            $c2w['WORKER_USER_ID'] = $dataC2W['WORKER_USER_ID'];
            $c2w['R_VIEW'][$dataC2W['CATEGORY_ID']] = $dataC2W['R_VIEW'];
            $c2w['R_ANSWER'][$dataC2W['CATEGORY_ID']] = $dataC2W['R_ANSWER'];
            $c2w['R_CREATE'][$dataC2W['CATEGORY_ID']] = $dataC2W['R_CREATE'];
        }
	}    
}
else
{
	$c2w = $request->getPostList()->toArray();
}

$APPLICATION->SetTitle(($ID > 0? Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_STATUS_TITLE_EDIT") : Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_STATUS_TITLE")));

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
?>
<?
$link = DeleteParam(array("mode"));
$link = $GLOBALS["APPLICATION"]->GetCurPage()."?mode=settings".($link <> ""? "&".$link:"");

$aMenu = Array(array(
       "TEXT"=>Loc::getMessage("ALTASIB_SUPPORT_BACK"),
       "LINK"=>"altasib_support_client2clientworker.php?lang=".LANG,
       "ICON"=>"btn_list",
    )
);
if($ID>0)
{
           $aMenu[] = array(
                   "TEXT"=>GetMessage("MAIN_ADMIN_MENU_ADD"),
                   "LINK"=>"altasib_support_client2clientworker_edit.php?lang=".LANG,
                   "ICON"=>"btn_new",
           );
}
//settings
        $aMenu[] = array(
                "TEXT"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS"),
                "LINK"=>"javascript:".$tabControl->GetName().".ShowSettings('".htmlspecialchars(CUtil::addslashes($link))."')",
                "ICON"=>"btn_settings",
        );

$context = new CAdminContextMenu($aMenu);
$context->Show();

if(!empty($errors))
	CAdminMessage::ShowMessage(join("\n", $errors));
    
$c2wField = array();
foreach($c2w as $key => $val)
{
    if(!is_array($val))
	   $c2wField[$key] = intval($val);
    else
    {
        foreach($val as $k=>$v)
            $c2wField[$key][$k] = htmlspecialchars($v);
    }    
}

$category = Support\CategoryTable::getList();
$arMap = Support\C2CWTable::getMap();
$tabControl->BeginEpilogContent();
?>
        <?=bitrix_sessid_post()?>
<?foreach($c2wIDs as $k=>$c2wID):?>
    <input type="hidden" name="ID[<?=$k?>]" value="<?=$c2wID?>">
<?endforeach;?>        
<?
$tabControl->EndEpilogContent();
$tabControl->Begin(Array("FORM_ACTION"=>htmlspecialcharsbx($request->getRequestedPage())));
$tabControl->BeginNextFormTab(); //MAIN

$tabControl->BeginCustomField("USER_ID");
?>
		<tr>
				<td><?=Loc::getMessage('ALTASIB_SUPPORT_CONN_CLIENT2CLIENTWORKER_FORM_CLIENT')?>:</td>
				<td>
                    <?=FindUserID('USER_ID',$c2wField['USER_ID'],'','altasib_support_client2clientworker_edit_form');?>
				</td>
		</tr>
<?
$tabControl->EndCustomField("USER_ID");
$tabControl->BeginCustomField("WORKER_USER_ID");
?>
		<tr>
				<td><?=Loc::getMessage('ALTASIB_SUPPORT_CONN_CLIENT2CLIENTWORKER_FORM_CLIENT_WORKER')?>:</td>
				<td>
                    <?=FindUserID('WORKER_USER_ID',$c2wField['WORKER_USER_ID'],'','altasib_support_client2clientworker_edit_form');?>
				</td>
		</tr>
<?
$tabControl->EndCustomField("WORKER_USER_ID");

while($dataCategory = $category->fetch())
{
    $tabControl->AddSection('RIGHT_'.$dataCategory['ID'],Loc::getMessage('ALTASIB_SUPPORT_CONN_CLIENT2CLIENTWORKER_FORM_RIGHT').': '.$dataCategory['NAME']);

    $tabControl->AddCheckBoxField('R_VIEW['.$dataCategory['ID'].']',$arMap['R_VIEW']['title'],false,'Y',($c2wField['R_VIEW'][$dataCategory['ID']]=='Y'));
    $tabControl->AddCheckBoxField('R_ANSWER['.$dataCategory['ID'].']',$arMap['R_ANSWER']['title'],false,'Y',($c2wField['R_ANSWER'][$dataCategory['ID']]=='Y'));
    $tabControl->AddCheckBoxField('R_CREATE['.$dataCategory['ID'].']',$arMap['R_CREATE']['title'],false,'Y',($c2wField['R_CREATE'][$dataCategory['ID']]=='Y'));
}
$tabControl->Buttons(array("disabled" => false, "back_url"=>'altasib_support_client2clientworker.php?lang='.LANGUAGE_ID));
?>
<?$tabControl->Show();?>

<?echo BeginNote();?>
<span class="required">*</span> - <?=Loc::getMessage("ALTASIB_SUPPORT_REQUIRED_FIELDS");?><br />
<?echo EndNote();?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>