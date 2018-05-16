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
Loc::loadMessages(__FILE__);

$UserRight = $APPLICATION->GetUserRight("altasib.support");
if($UserRight < "W")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$sTableID = "altasib_support_worker2client_list";
$oSort = new CAdminSorting($sTableID, "CLIENT_USER_ID", "DESC");
$adminList = new CAdminList($sTableID, $oSort);

$request = Main\Context::getCurrent()->getRequest();
$arFilter = array();
if($adminList->EditAction())
{
       foreach($FIELDS as $ID=>$arFields)
       {
           if(!$adminList->IsUpdated($ID))
                   continue;
                      
            $result = Support\WtCTable::update($ID, $arFields); 
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
           $obC = Support\WtCTable::getList(array('order'=>Array($by=>$order),'filter'=> $arFilter));
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
               $data = Support\WtCTable::getList(array('filter'=>array('CLIENT_USER_ID'=>$ID)));
               while($d = $data->fetch())
               {
                $dId = $d['ID'];
                    $result = Support\WtCTable::delete($dId);
                    if(!$result->isSuccess())
                    {
                    	$adminList->AddGroupError("(ID=".$dId.") ".implode("<br>", $result->getErrorMessages()), $dId);
                    }
               }               
               break;
           }
       }
}
$APPLICATION->SetTitle(Loc::getMessage("ALTASIB_SUPPORT_CONN_WORKER2CLIENT_TITLE"));

$adminList->AddHeaders(array(
        array("id"=>"USER_ID",              "content"=>Loc::getMessage("ALTASIB_SUPPORT_CONN_WORKER2CLIENT_LIST_WORKER"), 'sort'=>'USER_ID',          "default"=>true),
        array("id"=>"CLIENT_USER_ID",              "content"=>Loc::getMessage("ALTASIB_SUPPORT_CONN_WORKER2CLIENT_LIST_CLIENT"), 'sort'=>'CLIENT_USER_ID',          "default"=>true),
        //array("id"=>"RIGHT",              "content"=>Loc::getMessage("ALTASIB_SUPPORT_CONN_WORKER2CLIENT_LIST_RIGHT"),          "default"=>true),
));

$data = Support\WtCTable::getList(
    array(
        'order'=>Array($by=>$order),
        'filter'=> $arFilter,
        //'select'=>array('*','USER','CLIENT_USER'),
        'select'=>array('CLIENT_USER_ID','USER_SHORT_NAME'=>'USER.SHORT_NAME','CLIENT_USER_SHORT_NAME'=>'CLIENT_USER.SHORT_NAME'),
        'group'=> array('CLIENT_USER_ID'),
));
$data = new CAdminResult($data, $sTableID);
$data->NavStart();
$adminList->NavText($data->GetNavPrint('-'));
while($wtc = $data->Fetch())
{
        $EditLink = "altasib_support_worker2client_edit.php?lang=".LANG."&CLIENT_USER_ID=".$wtc['CLIENT_USER_ID'];
        $row =& $adminList->AddRow($wtc['CLIENT_USER_ID'], $wtc,$EditLink);
        
        $row->AddInputField("USER_ID",array("size"=>25));
        $row->AddViewField("USER_ID",$wtc['USER_SHORT_NAME']);

        $row->AddInputField("CLIENT_USER_ID",array("size"=>25));
        $row->AddViewField("CLIENT_USER_ID",$wtc['CLIENT_USER_SHORT_NAME']);        
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
                "LINK"=>"altasib_support_worker2client_edit.php?lang=".LANG,
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