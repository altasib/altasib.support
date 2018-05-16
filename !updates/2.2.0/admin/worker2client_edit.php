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
$CLIENT_USER_ID = intval($request["CLIENT_USER_ID"]);

$aTabs = array(
    array("DIV" => "main",       "TAB" => Loc::getMessage("ALTASIB_SUPPORT_CONN_WORKER2CLIENT_TAB_MAIN"),       "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_CONN_WORKER2CLIENT_TAB_MAIN_TITLE"))
);
$tabControl = new CAdminForm("altasib_support_worker2client_edit", $aTabs);

if($request->isPost() && ($request["save"] <> '' || $request["apply"] <> '') && check_bitrix_sessid())
{
    $data = Support\CategoryTable::getList();
    while($category = $data->fetch())
    {
        $arFields = Array(
            'CATEGORY_ID' => $category['ID'],
            "USER_ID"=>$request['USER_ID'],
            "CLIENT_USER_ID"=>$request['CLIENT_USER_ID'],
            "R_VIEW"=>$request['R_VIEW'][$category['ID']],
            "R_ANSWER"=>$request['R_ANSWER'][$category['ID']],
            "R_CHANGE_R"=>$request['R_CHANGE_R'][$category['ID']],
            "R_CHANGE_A"=>$request['R_CHANGE_A'][$category['ID']],
            "R_CHANGE_S"=>$request['R_CHANGE_S'][$category['ID']],
            "R_CHANGE_P"=>$request['R_CHANGE_P'][$category['ID']],
            "R_CHANGE_C"=>$request['R_CHANGE_C'][$category['ID']],
        );
        $IDs = (int)$request['ID'][$category['ID']];
            
        if($IDs>0 && Support\WtCTable::getById($IDs)->fetch())
        {
            $result = Support\WtCTable::update($IDs, $arFields);
        }
        else
        {
            $result = Support\WtCTable::add($arFields);
        }
        
    	if(!$result->isSuccess())
    		$errors = $result->getErrorMessages();
    }
    if(empty($errors))
    {
		if($request["save"] <> '')
			LocalRedirect("/bitrix/admin/altasib_support_worker2client.php?lang=".LANGUAGE_ID);
		else
			LocalRedirect("/bitrix/admin/altasib_support_worker2client_edit.php?lang=".LANGUAGE_ID."&USER_ID=".$request['USER_ID']."&CLIENT_USER_ID=".$request['CLIENT_USER_ID']."&".$tabControl->ActiveTabParam());        
    }
}

if(empty($errors))
{
	$wtc = false;
	if($CLIENT_USER_ID > 0)
	{
		$data = Support\WtCTable::getList(array(
            'filter' => array(
                'CLIENT_USER_ID' => $CLIENT_USER_ID
            )
        ));
        
        $wtc = array();
        $wtcIDs = array();
        while($dataWtC = $data->fetch())
        {
            $wtcIDs[$dataWtC['CATEGORY_ID']] = $dataWtC['ID'];
            $wtc['USER_ID'] = $dataWtC['USER_ID'];
            $wtc['CLIENT_USER_ID'] = $dataWtC['CLIENT_USER_ID'];
            $wtc['R_VIEW'][$dataWtC['CATEGORY_ID']] = $dataWtC['R_VIEW'];
            $wtc['R_ANSWER'][$dataWtC['CATEGORY_ID']] = $dataWtC['R_ANSWER'];
            $wtc['R_CHANGE_R'][$dataWtC['CATEGORY_ID']] = $dataWtC['R_CHANGE_R'];
            $wtc['R_CHANGE_A'][$dataWtC['CATEGORY_ID']] = $dataWtC['R_CHANGE_A'];
            $wtc['R_CHANGE_S'][$dataWtC['CATEGORY_ID']] = $dataWtC['R_CHANGE_S'];
            $wtc['R_CHANGE_P'][$dataWtC['CATEGORY_ID']] = $dataWtC['R_CHANGE_P'];
            $wtc['R_CHANGE_C'][$dataWtC['CATEGORY_ID']] = $dataWtC['R_CHANGE_C'];
        }
	}
}
else
{
	$wtc = $request->getPostList()->toArray();
}

$APPLICATION->SetTitle(($CLIENT_USER_ID > 0? Loc::getMessage("ALTASIB_SUPPORT_WORKER2CLIENT_TITLE") : Loc::getMessage("ALTASIB_SUPPORT_WORKER2CLIENT_TITLE_EDIT")));

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
?>
<?
$link = DeleteParam(array("mode"));
$link = $GLOBALS["APPLICATION"]->GetCurPage()."?mode=settings".($link <> ""? "&".$link:"");

$aMenu = Array(array(
       "TEXT"=>Loc::getMessage("ALTASIB_SUPPORT_BACK"),
       "LINK"=>"altasib_support_worker2client.php?lang=".LANG,
       "ICON"=>"btn_list",
    )
);
if($CLIENT_USER_ID>0)
{
   $aMenu[] = array(
           "TEXT"=>GetMessage("MAIN_ADMIN_MENU_ADD"),
           "LINK"=>"altasib_support_worker2client_edit.php?lang=".LANG,
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
    
$wtcField = array();
foreach($wtc as $key => $val)
{
    if(!is_array($val))
	   $wtcField[$key] = intval($val);
    else
    {
        foreach($val as $k=>$v)
            $wtcField[$key][$k] = htmlspecialchars($v);
    }    
}

$category = Support\CategoryTable::getList();
$arMap = Support\WtCTable::getMap();
$tabControl->BeginEpilogContent();
?>
    <?=bitrix_sessid_post()?>
<?foreach($wtcIDs as $k=>$wtcID):?>
    <input type="hidden" name="ID[<?=$k?>]" value="<?=$wtcID?>">
<?endforeach;?>        
<?
$tabControl->EndEpilogContent();
$tabControl->Begin(Array("FORM_ACTION"=>htmlspecialcharsbx($request->getRequestedPage())));
$tabControl->BeginNextFormTab(); //MAIN

$tabControl->BeginCustomField("USER_ID");
?>
		<tr>
				<td><?=Loc::getMessage('ALTASIB_SUPPORT_CONN_WORKER2CLIENT_FORM_WORKER')?>:</td>
				<td>
                    <?=FindUserID('USER_ID',$wtcField['USER_ID'],'','altasib_support_worker2client_edit_form');?>
				</td>
		</tr>
<?
$tabControl->EndCustomField("USER_ID");
$tabControl->BeginCustomField("WORKER_USER_ID");
?>
		<tr>
				<td><?=Loc::getMessage('ALTASIB_SUPPORT_CONN_WORKER2CLIENT_FORM_CLIENT')?>:</td>
				<td>
                    <?=FindUserID('CLIENT_USER_ID',$wtcField['CLIENT_USER_ID'],'','altasib_support_worker2client_edit_form');?>
				</td>
		</tr>
<?
$tabControl->EndCustomField("WORKER_USER_ID");

while($dataCategory = $category->fetch())
{
    $tabControl->AddSection('RIGHT_'.$dataCategory['ID'],Loc::getMessage('ALTASIB_SUPPORT_CONN_WORKER2CLIENT_FORM_RIGHT').': '.$dataCategory['NAME']);
    $tabControl->AddCheckBoxField('R_VIEW['.$dataCategory['ID'].']',$arMap['R_VIEW']['title'],false,'Y',($wtcField['R_VIEW'][$dataCategory['ID']]=='Y'));
    $tabControl->AddCheckBoxField('R_ANSWER['.$dataCategory['ID'].']',$arMap['R_ANSWER']['title'],false,'Y',($wtcField['R_ANSWER'][$dataCategory['ID']]=='Y'));
    $tabControl->AddCheckBoxField('R_CHANGE_R['.$dataCategory['ID'].']',$arMap['R_CHANGE_R']['title'],false,'Y',($wtcField['R_CHANGE_R'][$dataCategory['ID']]=='Y'));
    $tabControl->AddCheckBoxField('R_CHANGE_A['.$dataCategory['ID'].']',$arMap['R_CHANGE_A']['title'],false,'Y',($wtcField['R_CHANGE_A'][$dataCategory['ID']]=='Y'));
    $tabControl->AddCheckBoxField('R_CHANGE_S['.$dataCategory['ID'].']',$arMap['R_CHANGE_S']['title'],false,'Y',($wtcField['R_CHANGE_S'][$dataCategory['ID']]=='Y'));
    $tabControl->AddCheckBoxField('R_CHANGE_P['.$dataCategory['ID'].']',$arMap['R_CHANGE_P']['title'],false,'Y',($wtcField['R_CHANGE_P'][$dataCategory['ID']]=='Y'));
    $tabControl->AddCheckBoxField('R_CHANGE_C['.$dataCategory['ID'].']',$arMap['R_CHANGE_C']['title'],false,'Y',($wtcField['R_CHANGE_C'][$dataCategory['ID']]=='Y'));
}
$tabControl->Buttons(array("disabled" => false, "back_url"=>'altasib_support_worker2client.php?lang='.LANGUAGE_ID));
?>
<?$tabControl->Show();?>

<?echo BeginNote();?>
<span class="required">*</span> - <?=Loc::getMessage("ALTASIB_SUPPORT_REQUIRED_FIELDS");?><br />
<?echo EndNote();?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>