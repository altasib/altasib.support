<?php
/**
 * ALTASIB
 * @site http://www.altasib.ru
 * @email dev@altasib.ru
 * @copyright 2006-2018 ALTASIB
 */

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

if(!Main\Loader::includeModule("altasib.support"))
    return;

use ALTASIB\Support;
use ALTASIB\Support\Tools;
Loc::loadMessages(__FILE__);

$UserRight = $APPLICATION->GetUserRight("altasib.support");

if($UserRight < "W")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$request = Main\Context::getCurrent()->getRequest();

$errors = array();
$ID = intval($request["ID"]);

$aTabs = array(
    array("DIV" => "main",       "TAB" => Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_QUICK_RESPONSE_EDIT_TAB_MAIN"),       "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_QUICK_RESPONSE_EDIT_TAB_MAIN_TITLE"))
);
$tabControl = new CAdminForm("altasib_support_settings_quick_response_edit", $aTabs);

if($request->isPost() && ($request["save"] <> '' || $request["apply"] <> '') && check_bitrix_sessid())
{
    $arFields = Array(
        "NAME"=>$request['NAME'],
        "DESCRIPTION"=>$request['DESCRIPTION'],
        "SORT"=>$request['SORT'],
    );

    if($ID>0)
        $result = Support\QuickResponseTable::update($ID, $arFields);
    else
    {
        $result = Support\QuickResponseTable::add($arFields);
        $ID = $result->getId();
    }

	if($result->isSuccess())
	{
		if($request["save"] <> '')
			LocalRedirect(BX_ROOT."/admin/altasib_support_settings_quick_response.php?lang=".LANGUAGE_ID);
		else
			LocalRedirect(BX_ROOT."/admin/altasib_support_settings_quick_response_edit.php?lang=".LANGUAGE_ID."&ID=".$ID."&".$tabControl->ActiveTabParam());
	}
	else
	{
		$errors = $result->getErrorMessages();
	}
}

if(empty($errors))
{
	$quickResponse = false;
	if($ID > 0)
	{
		$quickResponseId = $ID;
		$quickResponse = Support\QuickResponseTable::getRowById($quickResponseId);
	}

	if($quickResponse == false)
	{
		$quickResponse = array();
	}
}
else
{
	$quickResponse = $request->getPostList()->toArray();
}

$APPLICATION->SetTitle(($ID > 0? Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_QUICK_RESPONSE_TITLE_EDIT") : Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_QUICK_RESPONSE_TITLE")));

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
?>
<?
$link = DeleteParam(array("mode"));
$link = $GLOBALS["APPLICATION"]->GetCurPage()."?mode=settings".($link <> ""? "&".$link:"");

$aMenu = Array(array(
       "TEXT"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_QUICK_RESPONSE_EDIT_CONTEXT_LIST"),
       "LINK"=>"altasib_support_settings_quick_response.php?lang=".LANG,
       "ICON"=>"btn_list",
       "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_QUICK_RESPONSE_EDIT_CONTEXT_LIST"),
    )
);
if($ID>0)
{
           $aMenu[] = array(
                   "TEXT"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_QUICK_RESPONSE_EDIT_CONTEXT_ADD"),
                   "LINK"=>"altasib_support_settings_quick_response_edit.php?lang=".LANG,
                   "ICON"=>"btn_new",
                   "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_QUICK_RESPONSE_EDIT_CONTEXT_ADD"),
           );
}
//settings
        $aMenu[] = array(
                "TEXT"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_QUICK_RESPONSE_EDIT_CONTEXT_SETTINGS"),
                "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_QUICK_RESPONSE_EDIT_CONTEXT_SETTINGS_TITLE"),
                "LINK"=>"javascript:".$tabControl->GetName().".ShowSettings('".htmlspecialchars(CUtil::addslashes($link))."')",
                "ICON"=>"btn_settings",
        );

$context = new CAdminContextMenu($aMenu);
$context->Show();

if(!empty($errors))
	CAdminMessage::ShowMessage(join("\n", $errors));
    
$quickResponseField = array();
foreach($quickResponse as $key => $val)
	$quickResponseField[$key] = htmlspecialcharsbx($val);    

$tabControl->BeginEpilogContent();
?>
        <?=bitrix_sessid_post()?>
        <input type="hidden" name="ID" value="<?=$ID?>">
<?
$tabControl->EndEpilogContent();
$tabControl->Begin(Array("FORM_ACTION"=>htmlspecialcharsbx($request->getRequestedPage())));
$tabControl->BeginNextFormTab(); //MAIN
if($ID>0)
{
    $tabControl->AddViewField("ID","ID",$quickResponseField['ID']);
    $tabControl->AddViewField("TIMESTAMP_X",Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_QUICK_RESPONSE_EDIT_FORM_TIMESTAMP_X"),$DB->FormatDate($quickResponseField['TIMESTAMP_X'],"YYYY.MM.DD HH:MI:SS"));
}
$tabControl->AddEditField("NAME", Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_QUICK_RESPONSE_EDIT_FORM_NAME'), true, array("size"=>50), $quickResponseField['NAME']);
$tabControl->AddEditField("SORT", Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_QUICK_RESPONSE_EDIT_FORM_SORT'), true, array("size"=>50), $quickResponseField['SORT']);

$tabControl->AddSection('D',Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_QUICK_RESPONSE_EDIT_FORM_DESC'));
$tabControl->BeginCustomField("DESCR",'');
?>
		<tr>
				<td colspan="2">
                    <?=Tools::ShowLHE('DESCRIPTION',$quickResponseField['DESCRIPTION'],'DESCRIPTION');?>
				</td>
		</tr>
<?
$tabControl->EndCustomField("DESCR");


//$tabControl->AddTextField("DESCRIPTION", Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_QUICK_RESPONSE_EDIT_FORM_DESC'), $quickResponseField['DESCRIPTION'],Array("rows"=>7,"cols"=>38),true);

$tabControl->Buttons(array("disabled" => false, "back_url"=>'altasib_support_settings_quick_response.php?lang='.LANGUAGE_ID));
?>
<?$tabControl->Show();?>

<?echo BeginNote();?>
<span class="required">*</span> - <?=Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_FIELD_REQUIRED_FIELDS");?><br />
<?echo EndNote();?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>