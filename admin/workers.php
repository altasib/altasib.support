<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Evgeniy Pedan                    #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2015 ALTASIB             #
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

$sTableID = "altasib_support_settings_worker_list";
$oSort = new CAdminSorting($sTableID, "ID", "DESC");
$adminList = new CAdminList($sTableID, $oSort);

$request = Main\Context::getCurrent()->getRequest();

if($adminList->EditAction())
{
       foreach($FIELDS as $ID=>$arFields)
       {
           if(!$adminList->IsUpdated($ID))
                   continue;

            $userUpd = new CUser;
            $userUpd->Update($ID,$arFields);                    
       }
}
if($arID = $adminList->GroupAction())
{

}
$APPLICATION->SetTitle(Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_WORKERS_TITLE"));

$adminList->AddHeaders(array(
        array("id"=>"USER",              "content"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_WORKERS_LIST_W"), "default"=>true),
        array("id"=>"ID",                "content"=>"ID",                                                        "sort"=>"id",        "default"=>true),
));

$arFilter["ACTIVE"] = "Y";
$arFilter["GROUPS_ID"] = Support\Tools::getSupportGroup();
$data = CUser::GetList(($by="id"), ($order="asc"), $arFilter,array('FIELDS'=>Array('ID','LOGIN','NAME','LAST_NAME')));
$data = new CAdminResult($data, $sTableID);
$data->NavStart();
$adminList->NavText($data->GetNavPrint());

while($user = $data->fetch())
{
        $EditLink = "/bitrix/admin/user_edit.php?lang=".LANG."&ID=".$user['ID'];
        $row =& $adminList->AddRow($user['ID'], $user,$EditLink);
        $row->AddViewField("USER",'('.$user['LOGIN'].') '.$user['NAME'].' '.$user['LAST_NAME']);
        
        $row->AddViewField("ID","<a href=\"$EditLink\">".$user['ID']."</a>");
}

$adminList->AddFooter(
        array(
                array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$data->SelectedRowsCount()),
                array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
        )
);
$adminList->AddAdminContextMenu(array());
//$adminList->AddGroupActionTable(Array(/*"delete"=>Loc::getMessage("MAIN_ADMIN_LIST_DELETE")*/));
$chain = $adminList->CreateChain();
$adminList->ShowChain($chain);
$adminList->CheckListMode();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
$adminList->DisplayList();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>