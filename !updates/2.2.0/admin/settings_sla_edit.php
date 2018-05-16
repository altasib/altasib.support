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
    array("DIV" => "main",       "TAB" => Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_SLA_EDIT_TAB_MAIN"),       "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_SLA_EDIT_TAB_MAIN_TITLE")),
    array("DIV" => "group",       "TAB" => Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_SLA_EDIT_TAB_GROUP"),       "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_SLA_EDIT_TAB_GROUP_TITLE"))
);
$tabControl = new CAdminForm("altasib_support_settings_sla_edit", $aTabs);

if($request->isPost() && ($request["save"] <> '' || $request["apply"] <> '') && check_bitrix_sessid())
{
    $arFields = Array(
            "NAME"=>$request['NAME'],
            "DESCRIPTION"=>$request['DESCRIPTION'],
            "SORT"=>(int)$request['SORT'],
            "RESPONSE_TIME"=>(int)$request['RESPONSE_TIME'],
            "NOTICE_TIME"=>(int)$request['NOTICE_TIME'],
    );

    if($ID>0)
        $result = Support\SlaTable::update($ID, $arFields);
    else
    {
        $result = Support\SlaTable::add($arFields);
        $ID = $result->getId();
    }
    
	if($result->isSuccess())
	{
        Support\SlaGroupTable::set($ID,$request['GROUP']);
        
		if($request["save"] <> '')
			LocalRedirect(BX_ROOT."/admin/altasib_support_settings_sla.php?lang=".LANGUAGE_ID);
		else
			LocalRedirect(BX_ROOT."/admin/altasib_support_settings_sla_edit.php?lang=".LANGUAGE_ID."&ID=".$ID."&".$tabControl->ActiveTabParam());
	}
	else
	{
		$errors = $result->getErrorMessages();
	}
}

if(empty($errors))
{
	$sla = false;
	if($ID > 0)
	{
		$slaId = $ID;
		$sla = Support\SlaTable::getList(array('filter'=>array('ID'=>$slaId),'select'=>array('*','CREATED_USER_SHORT_NAME'=>'CREATED_USER.SHORT_NAME','MODIFIED_USER_SHORT_NAME'=>'MODIFIED_USER.SHORT_NAME')))->fetch();
        $sla['GROUP'] = array();
        $dataSlaGroup =Support\SlaGroupTable::getList(array('filter'=>array('SLA_ID'=>$slaId)))->fetchAll();
        foreach($dataSlaGroup as $SlaGroup)
        {
            $sla['GROUP'][] = $SlaGroup['GROUP_ID'];
        } 
	}

	if($sla == false)
	{
		$sla = array();
	}
}
else
{
	$sla = $request->getPostList()->toArray();
}
$supportGroup = Support\Tools::getModuleGroup();
$APPLICATION->SetTitle(($ID > 0? Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_SLA_TITLE_EDIT") : Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_SLA_TITLE")));

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
?>
<?
$link = DeleteParam(array("mode"));
$link = $GLOBALS["APPLICATION"]->GetCurPage()."?mode=settings".($link <> ""? "&".$link:"");

$aMenu = Array(array(
       "TEXT"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_SLA_EDIT_CONTEXT_SLA_LIST"),
       "LINK"=>"altasib_support_settings_sla.php?lang=".LANG,
       "ICON"=>"btn_list",
       "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_SLA_EDIT_CONTEXT_SLA_LIST"),
    )
);
if($ID>0)
{
           $aMenu[] = array(
                   "TEXT"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_SLA_EDIT_CONTEXT_SLA_ADD"),
                   "LINK"=>"altasib_support_settings_sla_edit.php?lang=".LANG,
                   "ICON"=>"btn_new",
                   "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_SLA_EDIT_CONTEXT_SLA_ADD"),
           );
}
//settings
        $aMenu[] = array(
                "TEXT"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_SLA_EDIT_CONTEXT_SETTINGS"),
                "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_SLA_EDIT_CONTEXT_SETTINGS_TITLE"),
                "LINK"=>"javascript:".$tabControl->GetName().".ShowSettings('".htmlspecialchars(CUtil::addslashes($link))."')",
                "ICON"=>"btn_settings",
        );

$context = new CAdminContextMenu($aMenu);
$context->Show();

if(!empty($errors))
	CAdminMessage::ShowMessage(join("\n", $errors));
    
$slaField = array();
foreach($sla as $key => $val)
	$slaField[$key] = htmlspecialcharsbx($val);    

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
    $tabControl->AddViewField("ID","ID",$slaField['ID']);
    $tabControl->AddViewField("DATE_CREATE",Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_SLA_EDIT_FORM_DATE_CREATE'),$slaField['DATE_CREATE'].' '.$slaField['CREATED_USER_SHORT_NAME']);
    $tabControl->AddViewField("MODIFIED",Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_SLA_EDIT_FORM_DATE_MODIFIED'),$slaField['TIMESTAMP'].' '.$slaField['MODIFIED_USER_SHORT_NAME']);
    
}
$tabControl->AddEditField("NAME", Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_SLA_EDIT_FORM_NAME'), true, array("size"=>50), $slaField['NAME']);
$tabControl->AddTextField("DESCRIPTION", Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_SLA_EDIT_FORM_DESCRIPTION'), $slaField['DESCRIPTION'], array("cols"=>52,'rows'=>5),true);
$tabControl->AddEditField("SORT", Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_SLA_EDIT_FORM_SORT'), true, array("size"=>50), $slaField['SORT']);
$tabControl->AddEditField("RESPONSE_TIME", Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_SLA_EDIT_FORM_RESPONSE_TIME'), true, array("size"=>2), $slaField['RESPONSE_TIME']);
$tabControl->AddEditField("NOTICE_TIME", Loc::getMessage('ALTASIB_SUPPORT_SETTINGS_SLA_EDIT_FORM_NOTICE_TIME'), true, array("size"=>2), $slaField['NOTICE_TIME']);
$tabControl->BeginNextFormTab(); //Group

foreach($supportGroup as $Group)
{
    $tabControl->AddCheckBoxField("GROUP[".$Group['ID']."]", $Group['NAME'], true, $Group['ID'], in_array($Group['ID'],$sla['GROUP']));
}

$tabControl->Buttons(array("disabled" => false, "back_url"=>'altasib_support_settings_sla.php?lang='.LANGUAGE_ID));


?>
<?$tabControl->Show();?>

<?echo BeginNote();?>
<span class="required">*</span> - <?=Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_SLA_REQUIRED_FIELDS");?><br />
<?echo EndNote();?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>