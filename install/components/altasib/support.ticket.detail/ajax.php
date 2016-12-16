<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
define("NO_KEEP_STATISTIC", true);
define("NO_AGENT_STATISTIC", true);
define("NO_AGENT_CHECK", true);

use ALTASIB\Support;

if($request['ajax_support_message_edit']=='Y' && (int)$request['edit_support_message_id']>0 && ($arParams['ALLOW']|| $arParams['IS_SUPPORT_TEAM']))
{
    require_once('edit.php');
}

if($request['AJAX_ACTION']=='EDIT_COMMENT' && isset($request['comment']) && $arParams['HAVE_ANSWER'])
{
    CUtil::JSPostUnescape();
    $result = Support\TicketTable::update($arParams["ID"],array('COMMENT'=>$_POST['comment']));
    if($result->isSuccess())
        echo CUtil::PhpToJSObject(Array('status'=>true));  
    else
        echo CUtil::PhpToJSObject(Array('status'=>false,'error'=>'unknown'));
}

if($request['ajax_support_message_delete']=='Y' && (int)$request['delete_support_message_id']>0 && $arParams['ALLOW'])
{
    Support\TicketMessageTable::delete((int)$request['delete_support_message_id']);
    $APPLICATION->RestartBuffer();
    echo CUtil::PhpToJSObject(Array('status'=>true,'message'=>' '));
    die();
}

if($request['AJAX_ACTION']=='CATEGORY' && (int)$request['CID']>0 && $arParams['HAVE_CHANGE_CATEGORY'])
{
    $ID = (int)$request['CID'];
    $result = Support\TicketTable::update($arParams["ID"],array('CATEGORY_ID'=>$ID,'MODIFIED_USER_ID'=>$USER->GetID()));
    if($result->isSuccess())
    {
        Support\Event::changeCaregory($arParams["ID"]);
        echo CUtil::PhpToJSObject(Array('status'=>true));  
    }
    else
    {
        echo CUtil::PhpToJSObject(Array('status'=>false,'error'=>'unknown'));
    }
}

if($request['AJAX_ACTION']=='PRIORITY' && (int)$request['CID']>0 && $arParams['HAVE_CHANGE_PRIORITY'])
{
    $ID = (int)$request['CID'];
    $data = Support\TicketTable::getRow(array('filter'=>array('ID'=>$arParams["ID"]),'select'=>array('PRIORITY_ID')));
    $result = Support\TicketTable::update($arParams["ID"],array('PRIORITY_ID'=>$ID,'MODIFIED_USER_ID'=>$USER->GetID()));
    if($result->isSuccess())
    {
        $Priority = Support\Priority::get();
        $strCurrent = '';
        foreach($Priority as $IDp => $Name)
        {
            if($ID == $IDp)
            {
                $strCurrent = $Name;
                continue;
            }    
        }           
        Support\Event::changePriority($arParams["ID"],$data['PRIORITY_ID']);
        echo CUtil::PhpToJSObject(Array('status'=>true,'current'=>$strCurrent));
    }
    else
    {
        echo CUtil::PhpToJSObject(Array('status'=>false,'error'=>'unknown'));
    }
}

if($request['AJAX_ACTION']=='STATUS' && (int)$request['CID']>0 && $arParams['HAVE_CHANGE_STATUS'])
{
    $ID = (int)$request['CID'];
    $result = Support\TicketTable::update($arParams["ID"],array('STATUS_ID'=>$ID,'MODIFIED_USER_ID'=>$USER->GetID()));
    if($result->isSuccess())
    {
        Support\Event::changeStatus($arParams["ID"]); 
        echo CUtil::PhpToJSObject(Array('status'=>true));
    }
    else
    {
        echo CUtil::PhpToJSObject(Array('status'=>false,'error'=>'unknown'));
    }
}

if($request['AJAX_ACTION']=='RESPONSIBLE' && (int)$request['CID']>0 && $arParams['HAVE_CHANGE_RESPONSIBLE'])
{
    $ID = (int)$request['CID'];
    $result = Support\TicketTable::update($arParams["ID"],array('RESPONSIBLE_USER_ID'=>$ID,'MODIFIED_USER_ID'=>$USER->GetID()));
    if($result->isSuccess())
    {
        Support\Event::changeResponsible($arParams["ID"]);
        echo "jsRes = " .CUtil::PhpToJSObject(Array('RESULT'=>'true'));
    }
    else
    {
        echo "jsRes = " .CUtil::PhpToJSObject(Array('RESULT'=>'false','VALUE'=>''));
    }
}


if($request['AJAX_ACTION']=='ADD_MEMBER' && (int)$request['UID']>0 && ($arParams["HAVE_CHANGE_ASSISTANTS"] || $arTicket["RESPONSIBLE_USER_ID"]==$USER->GetID()))
{
    $res = Support\TicketMemberTable::add(array('USER_ID'=>$request['UID'],'TICKET_ID'=>$arParams["ID"]));
    if($res->isSuccess())
    {
        $user = Support\UserTable::getByPrimary($request['UID'],array('select'=>array('SHORT_NAME')))->fetch();
        $obRes = array('status'=>true,'error'=>'','txt'=>$user['SHORT_NAME']);
    }
    else
    {
        $obRes = array('status'=>false,'error'=>$res->getErrorMessages(),'txt'=>'');
    }
    echo CUtil::PhpToJSObject($obRes);
}

if($request['AJAX_ACTION']=='DELETE_MEMBER' && (int)$request['UID']>0 && $arParams["HAVE_CHANGE_ASSISTANTS"])
{
    $data = Support\TicketMemberTable::getList(array('select'=>array('ID'),'filter'=>array('USER_ID'=>$request['UID'],'TICKET_ID'=>$arParams['ID'])));
    if($member = $data->fetch())
    {
        Support\TicketMemberTable::delete($member['ID']);
        echo CUtil::PhpToJSObject(array('status'=>true,'error'=>''));
    }
}

if($request['AJAX_ACTION']=='SET_MEMBER_LIST' /*&& count($request['UID'])>0*/ && $arParams["HAVE_CHANGE_ASSISTANTS"])
{
    $allList = array();
    $data = Support\TicketMemberTable::getList(array('select'=>array('ID','USER_ID'),'filter'=>array('TICKET_ID'=>$arParams['ID'])));
    while($member = $data->fetch())
    {
        Support\TicketMemberTable::delete($member['ID']);
        $allList[$member['USER_ID']] = $member;
    }

    foreach($request['UID'] as $uid)
    {
        Support\TicketMemberTable::add(array('USER_ID'=>$uid,'TICKET_ID'=>$arParams["ID"]));

        //new, send event
        if(!array_key_exists($uid,$allList))
        {
            Support\Event::sendAddMember($arParams["ID"],$uid);
        }
    }

    echo CUtil::PhpToJSObject(array('status'=>true,'error'=>''));
}

if($request['AJAX_ACTION']=='SET_GROUP' && (int)$request['GROUP_ID']>0 && ($arParams['ALLOW'] || ($USER->GetID()==$arTicket['OWNER_USER_ID'] && $USER->GetID()==$arTicket['GROUP_OWNER_ID'])))
{
    $result = Support\TicketTable::update($arParams["ID"],array('GROUP_ID'=>$request['GROUP_ID']));
    if($result->isSuccess())
    {
        $obRes = array('status'=>true,'error'=>'');
    }
    else
    {
        $obRes = array('status'=>false,'error'=>$res->getErrorMessages());
    }    
    echo CUtil::PhpToJSObject($obRes);
}

if($request['AJAX_ACTION']=='SET_DEAL' && (int)$request['DEAL_ID']>0 && $arParams['ALLOW'])
{
    $result = Support\TicketTable::update($arParams["ID"],array('DEAL_ID'=>$request['DEAL_ID']));
    if($result->isSuccess())
    {
        $obRes = array('status'=>true,'error'=>'');
    }
    else
    {
        $obRes = array('status'=>false,'error'=>$res->getErrorMessages());
    }    
    echo CUtil::PhpToJSObject($obRes);
}


if($request['AJAX_ACTION']=='ADD_FAVORITE' && (int)$request['MESSAGE_ID']>0)
{
    \ALTASIB\Support\FavoriteTable::add(array('TICKET_ID'=>$arParams['ID'],'MESSAGE_ID'=>$request['MESSAGE_ID'],'USER_ID'=>$USER->GetID()));
    echo CUtil::PhpToJSObject(array('result'=>true));
}

if($request['AJAX_ACTION']=='DEL_FAVORITE' && (int)$request['MESSAGE_ID']>0)
{
    $row = \ALTASIB\Support\FavoriteTable::getRow(array('filter'=>array('TICKET_ID'=>$arParams['ID'],'MESSAGE_ID'=>$request['MESSAGE_ID'],'USER_ID'=>$USER->GetID())));
    if($row)
    {
        \ALTASIB\Support\FavoriteTable::delete($row['ID']);
        echo CUtil::PhpToJSObject(array('result'=>true));
    }
    else
        echo CUtil::PhpToJSObject(array('result'=>false));
}
?>