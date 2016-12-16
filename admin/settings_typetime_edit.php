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
use Bitrix\Main\Type;

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
    array("DIV" => "main",       "TAB" => Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_EDIT_TAB_MAIN"),       "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_EDIT_TAB_MAIN_TITLE")),
    array("DIV" => "cost",       "TAB" => Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_EDIT_TAB_COST"),       "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_EDIT_TAB_COST_TITLE"))
);
$tabControl = new CAdminForm("altasib_support_settings_type_time_edit", $aTabs);

if($request->isPost() && ($request["save"] <> '' || $request["apply"] <> '') && check_bitrix_sessid())
{
    $arFields = Array(
            "NAME"=>$request['NAME'],
            'DEFAULT' => $request['DEFAULT']
    );

    if($ID>0)
        $result = Support\TypeTimeTable::update($ID, $arFields);
    else
    {
        $result = Support\TypeTimeTable::add($arFields);
        $ID = $result->getId();
    }

	if($result->isSuccess())
	{
	   //add cost if
       $arCostField = array(
        'TYPE_TIME_ID' =>$ID,
        'COST' =>(int)$request['COST'],
        'DATE_START' =>new Type\Date($request['DATE_START'],'d.m.Y'),
        'DATE_END' =>new Type\Date($request['DATE_END'],'d.m.Y'),
       );
       if(strlen($request['DATE_END'])==0)
        unset($arCostField['DATE_END']);

       if($arCostField['COST']>0) 
	       $result = Support\TypeTimeCostTable::add($arCostField);
       
		if($request["save"] <> '')
			LocalRedirect(BX_ROOT."/admin/altasib_support_settings_typetime.php?lang=".LANGUAGE_ID);
		else
			LocalRedirect(BX_ROOT."/admin/altasib_support_settings_typetime_edit.php?lang=".LANGUAGE_ID."&ID=".$ID."&".$tabControl->ActiveTabParam());
	}
	else
	{
		$errors = $result->getErrorMessages();
	}
}

if(empty($errors))
{
	$typeTime = false;
	if($ID > 0)
	{
		$typeTimeId = $ID;
		$typeTime = Support\TypeTimeTable::getById($typeTimeId)->fetch();
	}

	if($typeTime == false)
		$typeTime = array();
}
else
{
	$typeTime = $request->getPostList()->toArray();
}

$APPLICATION->SetTitle(($ID > 0? Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_TITLE_EDIT") : Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_TITLE")));

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
?>
<?
$link = DeleteParam(array("mode"));
$link = $GLOBALS["APPLICATION"]->GetCurPage()."?mode=settings".($link <> ""? "&".$link:"");

$aMenu = Array(array(
       "TEXT"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_EDIT_CONTEXT_LIST"),
       "LINK"=>"altasib_support_settings_typetime.php?lang=".LANG,
       "ICON"=>"btn_list",
       "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_EDIT_CONTEXT_LIST"),
    )
);
if($ID>0)
{
           $aMenu[] = array(
                   "TEXT"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_EDIT_CONTEXT_ADD"),
                   "LINK"=>"altasib_support_settings_typetime_edit.php?lang=".LANG,
                   "ICON"=>"btn_new",
                   "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_EDIT_CONTEXT_ADD"),
           );
}
//settings
        $aMenu[] = array(
                "TEXT"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_EDIT_CONTEXT_SETTINGS"),
                "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_EDIT_CONTEXT_SETTINGS_TITLE"),
                "LINK"=>"javascript:".$tabControl->GetName().".ShowSettings('".htmlspecialchars(CUtil::addslashes($link))."')",
                "ICON"=>"btn_settings",
        );

$context = new CAdminContextMenu($aMenu);
$context->Show();

if(!empty($errors))
	CAdminMessage::ShowMessage(join("\n", $errors));
    
$typeTimeField = array();
foreach($typeTime as $key => $val)
	$typeTimeField[$key] = htmlspecialcharsbx($val);    

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/iblock/classes/general/subelement.php');

$sTableID = 'altasib_support_settings_typetime_sublist';
$lAdmin = new CAdminSubList($sTableID,false);

$arHeader = array(
    array("id"=>"COST", "content"=>GetMessage("ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_EDIT_FORM_COST_LIST"), "sort"=>"COST", "default"=>true),
    array("id"=>"DATE_START", "content"=>GetMessage("ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_EDIT_FORM_DATE_START"), "sort"=>"DATE_START", "default"=>true),
    array("id"=>"DATE_END", "content"=>GetMessage("ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_EDIT_FORM_DATE_END"), "sort"=>"DATE_END", "default"=>true)
);

$lAdmin->AddHeaders($arHeader);
$data = Support\TypeTimeCostTable::getList(array('filter'=>array('TYPE_TIME_ID'=>$ID,'USER_ID'=>false)));
$data = new CAdminSubResult($data, $sTableID, $lAdmin->GetListUrl(true));
//$data->NavStart();
//$adminList->NavText($data->GetNavPrint());

while($typeTimeCost = $data->fetch())
{
        $EditLink = "altasib_support_settings_typetime_edit.php?lang=".LANG."&ID=".$typeTimeCost['ID'];
        $row =& $lAdmin->AddRow($typeTimeCost['ID'], $typeTimeCost,$EditLink);
        //$row->AddInputField("COST",array("size"=>25));
        $row->AddViewField("COST",$typeTimeCost['COST']);
}
$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$data->SelectedRowsCount()),
		array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
	)
);


$tabControl->BeginEpilogContent();
?>
        <?=bitrix_sessid_post()?>
        <input type="hidden" name="ID" value="<?=$ID?>">
<?
$tabControl->EndEpilogContent();
$tabControl->Begin(Array("FORM_ACTION"=>htmlspecialcharsbx($request->getRequestedPage())));
$tabControl->BeginNextFormTab(); //MAIN
if($ID>0)
    $tabControl->AddViewField("ID","ID",$typeTimeField['ID']);

$tabControl->AddEditField("NAME", Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_EDIT_FORM_NAME'), true, array("size"=>50), $typeTimeField['NAME']);
$tabControl->AddCheckBoxField("DEFAULT", Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_EDIT_FORM_DEFAULT'),false,"Y",($typeTimeField['DEFAULT']=="Y"));

$tabControl->BeginNextFormTab(); //COST
$tabControl->AddSection('ADD',Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_EDIT_FORM_COST_ADD'));
$tabControl->AddEditField("COST", Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_EDIT_FORM_COST'), true, array("size"=>10), $typeTimeField['COST']);
$tabControl->AddCalendarField('DATE_START',Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_EDIT_FORM_DATE_START'),'',true); 
$tabControl->AddCalendarField('DATE_END',Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_EDIT_FORM_DATE_END'),'',false);

$tabControl->AddSection('L',Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_EDIT_FORM_COST_LIST'));

$tabControl->BeginCustomField('COST_LIST', GetMessage("ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_EDIT_FORM_COST_LIST"), false);
?>
<tr><td colspan="2">
<?
	$lAdmin->CheckListMode();
	$lAdmin->DisplayList();
?>
</td></tr>
<?        
$tabControl->EndCustomField('COST_LIST','');

$tabControl->Buttons(array("disabled" => false, "back_url"=>'altasib_support_settings_type_time.php?lang='.LANGUAGE_ID));
?>
<?$tabControl->Show();?>

<?echo BeginNote();?>
<span class="required">*</span> - <?=Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_TYPE_TIME_REQUIRED_FIELDS");?><br />
<?echo EndNote();?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>