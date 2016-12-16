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

$sTableID = "altasib_support_settings_status_list";
$oSort = new CAdminSorting($sTableID, "ID", "DESC");
$adminList = new CAdminList($sTableID, $oSort);

$request = Main\Context::getCurrent()->getRequest();

if($adminList->EditAction())
{
       foreach($FIELDS as $ID=>$arFields)
       {
           if(!$adminList->IsUpdated($ID))
                   continue;
                      
            $result = Support\StatusTable::update($ID, $arFields); 
    		if(!$result->isSuccess())
    		{
    			$adminList->AddUpdateError("(ID=".$ID.") ".implode("<br>", $result->getErrorMessages()), $ID);
    		}                       

       }
}
if($arID = $adminList->GroupAction())
{
       if($_REQUEST['action_target']=='selected')
       {
           $obC = Support\StatusTable::getList(array('order'=>Array($by=>$order), 'select'=>$arFilter));
           while($arRes = $obC->fetch())
                   $arID[] = $arRes['ID'];
       }

       foreach($arID as $ID)
       {
           if(strlen($ID)<=0)
                   continue;

           switch($_REQUEST['action'])
           {
               case "delete":
                    $result = Support\StatusTable::delete($ID);
                    if(!$result->isSuccess())
                    {
                    	$adminList->AddGroupError("(ID=".$ID.") ".implode("<br>", $result->getErrorMessages()), $ID);
                    }               
               break;
           }
       }
}
$APPLICATION->SetTitle(Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_STATUS_TITLE"));

$adminList->AddHeaders(array(
        array("id"=>"NAME",              "content"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_STATUS_LIST_NAME"),          "sort"=>"name",        "default"=>true),
        array("id"=>"SORT",              "content"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_STATUS_LIST_SORT"),          "default"=>true),
        array("id"=>"ID",                "content"=>"ID",                                                        "sort"=>"id",        "default"=>true),
));

$data = Support\StatusTable::getList(/*Array($by=>$order)*/);
$data = new CAdminResult($data, $sTableID);
$data->NavStart();
$adminList->NavText($data->GetNavPrint());

while($status = $data->fetch())
{
    	$id = htmlspecialcharsbx($status["ID"]);
    	$name = htmlspecialcharsbx($status["NAME"]);    
        $EditLink = "altasib_support_settings_status_edit.php?lang=".LANG."&ID=".$id;
        $row =& $adminList->AddRow($id, $status,$EditLink);
        $row->AddInputField("NAME",array("size"=>25));
        $row->AddViewField("NAME",$name);
        $row->AddInputField("SORT",array("size"=>25));
        $row->AddViewField("SORT",$status["SORT"]);
        $row->AddViewField("ID","<a href=\"$EditLink\">".$id."</a>");
}

$adminList->AddFooter(
        array(
                array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$data->SelectedRowsCount()),
                array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
        )
);

$aContext = array(
        array(
                "TEXT"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_STATUS_CONTEXT_ADD"),
                "ICON"=>"btn_new",
                "LINK"=>"altasib_support_settings_status_edit.php?lang=".LANG,
                "TITLE"=>Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_STATUS_CONTEXT_ADD"),
                "LINK_PARAM"=>"",
        ),
);
$adminList->AddAdminContextMenu($aContext);
$adminList->AddGroupActionTable(Array("delete"=>Loc::getMessage("MAIN_ADMIN_LIST_DELETE")));
$chain = $adminList->CreateChain();
$adminList->ShowChain($chain);
$adminList->CheckListMode();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
$adminList->DisplayList();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>