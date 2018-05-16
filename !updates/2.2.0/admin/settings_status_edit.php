<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Evgeniy Pedan                    #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2013 ALTASIB             #
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
$ID = intval($request["ID"]);

$aTabs = array(
    array("DIV" => "main",       "TAB" => Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_STATUS_EDIT_TAB_MAIN"),       "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_STATUS_EDIT_TAB_MAIN_TITLE"))
);
$tabControl = new CAdminForm("altasib_support_settings_status_edit", $aTabs);

if($request->isPost() && ($request["save"] <> '' || $request["apply"] <> '') && check_bitrix_sessid())
{
    $arFields = Array(
            "NAME"=>$request['NAME'],
            "SORT"=>$request['SORT'],
            'SKIP' => $request['SKIP'],
    );

    if($ID>0)
        $result = Support\StatusTable::update($ID, $arFields);
    else
    {
        $result = Support\StatusTable::add($arFields);
        $ID = $result->getId();
    }

	if($result->isSuccess())
	{
		if($request["save"] <> '')
			LocalRedirect(BX_ROOT."/admin/altasib_support_settings_status.php?lang=".LANGUAGE_ID);
		else
			LocalRedirect(BX_ROOT."/admin/altasib_support_settings_status_edit.php?lang=".LANGUAGE_ID."&ID=".$ID."&".$tabControl->ActiveTabParam());
	}
	else
	{
		$errors = $result->getErrorMessages();
	}
}

if(empty($errors))
{
	$status = false;
	if($ID > 0)
	{
		$statusId = $ID;
		$status = Support\StatusTable::getById($statusId)->fetch();
	}

	if($status == false)
	{
		$status = array();
	}
}
else
{
	$status = $request->getPostList()->toArray();
}

$APPLICATION->SetTitle(($ID > 0? Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_STATUS_TITLE_EDIT") : Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_STATUS_TITLE")));

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
?>
<?
$link = DeleteParam(array("mode"));
$link = $GLOBALS["APPLICATION"]->GetCurPage()."?mode=settings".($link <> ""? "&".$link:"");

$aMenu = Array(array(
       "TEXT"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_STATUS_EDIT_CONTEXT_STATUS_LIST"),
       "LINK"=>"altasib_support_settings_status.php?lang=".LANG,
       "ICON"=>"btn_list",
       "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_STATUS_EDIT_CONTEXT_STATUS_LIST"),
    )
);
if($ID>0)
{
           $aMenu[] = array(
                   "TEXT"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_STATUS_EDIT_CONTEXT_STATUS_ADD"),
                   "LINK"=>"altasib_support_settings_status_edit.php?lang=".LANG,
                   "ICON"=>"btn_new",
                   "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_STATUS_EDIT_CONTEXT_STATUS_ADD"),
           );
}
//settings
        $aMenu[] = array(
                "TEXT"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_STATUS_EDIT_CONTEXT_SETTINGS"),
                "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_STATUS_EDIT_CONTEXT_SETTINGS_TITLE"),
                "LINK"=>"javascript:".$tabControl->GetName().".ShowSettings('".htmlspecialchars(CUtil::addslashes($link))."')",
                "ICON"=>"btn_settings",
        );

$context = new CAdminContextMenu($aMenu);
$context->Show();

if(!empty($errors))
	CAdminMessage::ShowMessage(join("\n", $errors));
    
$statusField = array();
foreach($status as $key => $val)
	$statusField[$key] = htmlspecialcharsbx($val);    

$tabControl->BeginEpilogContent();
?>
        <?=bitrix_sessid_post()?>
        <input type="hidden" name="ID" value="<?=$ID?>">
<?
$tabControl->EndEpilogContent();
$tabControl->Begin(Array("FORM_ACTION"=>htmlspecialcharsbx($request->getRequestedPage())));
$tabControl->BeginNextFormTab(); //MAIN
if($ID>0)
    $tabControl->AddViewField("ID","ID",$statusField['ID']);

$tabControl->AddEditField("NAME", Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_STATUS_EDIT_FORM_NAME'), true, array("size"=>50), $statusField['NAME']);
$tabControl->AddEditField("SORT", Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_STATUS_EDIT_FORM_SORT'), true, array("size"=>50), $statusField['SORT']);
$tabControl->AddCheckBoxField("SKIP", Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_STATUS_EDIT_FORM_SKIP'),false,"Y",($statusField['SKIP']=="Y"));
$tabControl->Buttons(array("disabled" => false, "back_url"=>'altasib_support_settings_status.php?lang='.LANGUAGE_ID));
?>
<?$tabControl->Show();?>

<?echo BeginNote();?>
<span class="required">*</span> - <?=Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_STATUS_REQUIRED_FIELDS");?><br />
<?echo EndNote();?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>