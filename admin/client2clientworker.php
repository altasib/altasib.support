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

$sTableID = "altasib_support_client2clientworker_list";
$oSort = new CAdminSorting($sTableID, "USER_ID", "DESC");
$adminList = new CAdminList($sTableID, $oSort);

$request = Main\Context::getCurrent()->getRequest();
$arFilter = array();
if($adminList->EditAction())
{
       foreach($FIELDS as $ID=>$arFields)
       {
           if(!$adminList->IsUpdated($ID))
                   continue;
                      
            $result = Support\C2CWTable::update($ID, $arFields); 
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
           $obC = Support\C2CWTable::getList(array('order'=>Array($by=>$order),'filter'=> $arFilter));
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
                    $result = Support\C2CWTable::delete($ID);
                    if(!$result->isSuccess())
                    {
                    	$adminList->AddGroupError("(ID=".$ID.") ".implode("<br>", $result->getErrorMessages()), $ID);
                    }               
               break;
           }
       }
}
$APPLICATION->SetTitle(Loc::getMessage("ALTASIB_SUPPORT_CONN_CLIENT2CLIENTWORKER_TITLE"));

$adminList->AddHeaders(array(
        array("id"=>"USER_ID",              "content"=>Loc::getMessage("ALTASIB_SUPPORT_CONN_CLIENT2CLIENTWORKER_LIST_CLIENT"),          "default"=>true),
        array("id"=>"WORKER_USER_ID",              "content"=>Loc::getMessage("ALTASIB_SUPPORT_CONN_CLIENT2CLIENTWORKER_LIST_CLIENT_WORKER"),          "default"=>true),
        //array("id"=>"RIGHT",              "content"=>Loc::getMessage("ALTASIB_SUPPORT_CONN_CLIENT2CLIENTWORKER_LIST_RIGHT"),          "default"=>true),
        //array("id"=>"ID",                "content"=>"ID",                                                        "sort"=>"id",        "default"=>true),
));

$data = Support\C2CWTable::getList(array('order'=>Array($by=>$order),'filter'=> $arFilter,'select'=>array('USER_ID','WORKER_USER_ID','USER_SHORT_NAME'=>'USER.SHORT_NAME','WORKER_USER_SHORT_NAME'=>'WORKER_USER.SHORT_NAME'),'group'=>array('USER_ID')));
$data = new CAdminResult($data, $sTableID);
$data->NavStart();
$adminList->NavText($data->GetNavPrint());
while($c2cw = $data->fetch())
{
        $EditLink = "altasib_support_client2clientworker_edit.php?lang=".LANG."&USER_ID=".$c2cw['USER_ID']."&WORKER_USER_ID=".$c2cw['WORKER_USER_ID'];
        $row =& $adminList->AddRow($id, $c2cw,$EditLink);
        
        $row->AddInputField("USER_ID",array("size"=>25));
        $row->AddViewField("USER_ID",$c2cw['USER_SHORT_NAME']);

        $row->AddInputField("WORKER_USER_ID",array("size"=>25));
        $row->AddViewField("WORKER_USER_ID",$c2cw['WORKER_USER_SHORT_NAME']);        
}

$adminList->AddFooter(
    array(
            array("title"=>Loc::getMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$data->SelectedRowsCount()),
            array("counter"=>true, "title"=>Loc::getMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
    )
);

$aContext = array(
        array(
                "TEXT"=>GetMessage("MAIN_ADMIN_MENU_ADD"),
                "ICON"=>"btn_new",
                "LINK"=>"altasib_support_client2clientworker_edit.php?lang=".LANG,
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