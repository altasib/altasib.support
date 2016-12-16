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
$ID = intval($request["ID"]);

$aTabs = array(
    array("DIV" => "main",       "TAB" => Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_CATEGORY_EDIT_TAB_MAIN"),       "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_CATEGORY_EDIT_TAB_MAIN_TITLE"))
);
$tabControl = new CAdminForm("altasib_support_settings_category_edit", $aTabs);

if($request->isPost() && ($request["save"] <> '' || $request["apply"] <> '') && check_bitrix_sessid())
{
    $arFields = Array(
            "NAME"=>$request['NAME'],
            "DESCRIPTION"=>$request['DESCRIPTION'],
            "RESPONSIBLE_USER_ID"=>$request['RESPONSIBLE_USER_ID'],
            "USE_DEFAULT"=>$request['USE_DEFAULT'],
            "NOT_CLOSE"=>$request['NOT_CLOSE'],
    );

    if($ID>0)
        $result = Support\CategoryTable::update($ID, $arFields);
    else
    {
        $result = Support\CategoryTable::add($arFields);
        $ID = $result->getId();
    }

	if($result->isSuccess())
	{
		if($request["save"] <> '')
			LocalRedirect(BX_ROOT."/admin/altasib_support_settings_category.php?lang=".LANGUAGE_ID);
		else
			LocalRedirect(BX_ROOT."/admin/altasib_support_settings_category_edit.php?lang=".LANGUAGE_ID."&ID=".$ID."&".$tabControl->ActiveTabParam());
	}
	else
	{
		$errors = $result->getErrorMessages();
	}
}

if(empty($errors))
{
	$category = false;
	if($ID > 0)
	{
		$categoryId = $ID;
		$category = Support\CategoryTable::getRowById($categoryId);
	}

	if($category == false)
	{
		$category = array();
	}
}
else
{
	$category = $request->getPostList()->toArray();
}

$APPLICATION->SetTitle(($ID > 0? Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_CATEGORY_TITLE_EDIT") : Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_CATEGORY_TITLE")));

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
?>
<?
$link = DeleteParam(array("mode"));
$link = $GLOBALS["APPLICATION"]->GetCurPage()."?mode=settings".($link <> ""? "&".$link:"");

$aMenu = Array(array(
       "TEXT"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_CATEGORY_EDIT_CONTEXT_CATEGORY_LIST"),
       "LINK"=>"altasib_support_settings_category.php?lang=".LANG,
       "ICON"=>"btn_list",
       "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_CATEGORY_EDIT_CONTEXT_CATEGORY_LIST"),
    )
);
if($ID>0)
{
           $aMenu[] = array(
                   "TEXT"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_CATEGORY_EDIT_CONTEXT_CATEGORY_ADD"),
                   "LINK"=>"altasib_support_settings_category_edit.php?lang=".LANG,
                   "ICON"=>"btn_new",
                   "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_CATEGORY_EDIT_CONTEXT_CATEGORY_ADD"),
           );
}
//settings
        $aMenu[] = array(
                "TEXT"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_CATEGORY_EDIT_CONTEXT_SETTINGS"),
                "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_CATEGORY_EDIT_CONTEXT_SETTINGS_TITLE"),
                "LINK"=>"javascript:".$tabControl->GetName().".ShowSettings('".htmlspecialchars(CUtil::addslashes($link))."')",
                "ICON"=>"btn_settings",
        );

$context = new CAdminContextMenu($aMenu);
$context->Show();

if(!empty($errors))
	CAdminMessage::ShowMessage(join("\n", $errors));
    
$categoryField = array();
foreach($category as $key => $val)
	$categoryField[$key] = htmlspecialcharsbx($val);    

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
    $tabControl->AddViewField("ID","ID",$categoryField['ID']);
    $tabControl->AddViewField("TIMESTAMP_X",Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_CATEGORY_EDIT_FORM_TIMESTAMP_X"),$DB->FormatDate($categoryField['TIMESTAMP_X'],"YYYY.MM.DD HH:MI:SS"));
}
$tabControl->AddEditField("NAME", Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_CATEGORY_EDIT_FORM_NAME'), true, array("size"=>50), $categoryField['NAME']);
$tabControl->AddTextField("DESCRIPTION", Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_CATEGORY_EDIT_FORM_DESC'), $categoryField['DESCRIPTION'],Array("rows"=>7,"cols"=>38));
$tabControl->AddDropDownField("RESPONSIBLE_USER_ID", Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_CATEGORY_EDIT_FORM_RESPONSIBLE_USER_ID'),true,Support\Tools::getSupportTeam(),$categoryField['RESPONSIBLE_USER_ID']);
$tabControl->AddCheckBoxField("USE_DEFAULT", Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_CATEGORY_EDIT_FORM_DEFAULT'),false,"Y",($categoryField['USE_DEFAULT']=="Y"));
$tabControl->AddCheckBoxField("NOT_CLOSE", Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_CATEGORY_EDIT_FORM_NOT_CLOSE'),false,"Y",($categoryField['NOT_CLOSE']=="Y"));
$tabControl->Buttons(array("disabled" => false, "back_url"=>'altasib_support_settings_category.php?lang='.LANGUAGE_ID));
?>
<?$tabControl->Show();?>

<?echo BeginNote();?>
<span class="required">*</span> - <?=Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_FIELD_REQUIRED_FIELDS");?><br />
<?echo EndNote();?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>