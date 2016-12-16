<?
define("STOP_STATISTICS", true);
define("STOP_WEBDAV", true);
define("NOT_CHECK_PERMISSIONS", true);
define('NO_AGENT_CHECK', true);
define("DisableEventsCheck", true);
 
$_SERVER['DOCUMENT_ROOT'] = '';
require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include.php");

if(!CModule::IncludeModule('altasib.support'))
    die();
use ALTASIB\Support;
use Bitrix\Main;
use Bitrix\Main\Type;

$old_db_login = '';
$old_db_pass = '';
$old_db_name = '';
$specialStatisID = 6;
$specialUser = 533;
@set_time_limit(0);
define("SUP_IMPORT",true);
CModule::IncludeModule("support");
//params data
$Status = array();
$obStatus = Support\StatusTable::getList();
while($arStatus = $obStatus->fetch())
    $Status[$arStatus["ID"]] = $arStatus['NAME'];            

$arPriority = Support\Priority::get();
$arCategory = array();
$dataCategory = Support\CategoryTable::getList();
while($ar = $dataCategory->fetch())
{
    $arCategory[$ar["NAME"]] = $ar;
}

//old tickets

$arOldTickets = array();
$arOldTicketsMessages = array();
$userInfo = array();
$filesImp = array();
$i = 0;

$DB->Disconnect();
if(($DB->Connect('localhost', $old_db_name, $old_db_login, $old_db_pass)))
{
    $obTickets = CTicket::GetList($by = "s_id",$order = "asc",Array(),$if,$a = "N");
    while($arTicket = $obTickets->Fetch())
    {
        $statusId = 0;
        if(strlen($arTicket['STATUS_NAME'])>0)
        {
            $statusId = array_search($arTicket['STATUS_NAME'],$Status);
        }
        if((int)$statusId==0)
            $statusId = $specialStatisID;
            
        $PRIORITY_ID = '';
        if(strlen($arTicket["CRITICALITY_NAME"])>0)
        {
            foreach($arPriority as $k => $priority)
            {
                if(substr($priority,0,4) == substr($arTicket["CRITICALITY_NAME"],0,4))
                {
                    $PRIORITY_ID = $k;
                break;
                }
            }
        }
            
        $categoryId = 1;
        if(strlen($arTicket['CATEGORY_NAME'])>0)
        {
            if(array_key_exists($arTicket['CATEGORY_NAME'],$arCategory))
                $categoryId = $arCategory[$arTicket['CATEGORY_NAME']]['ID'];                    
        }
    
        $arOldTickets[$arTicket['ID']] = Array(
                'ID'=>$arTicket['ID'],
                "TITLE"=>$arTicket["TITLE"],
                "OWNER_USER_ID"=>$arTicket["OWNER_USER_ID"],
                "CREATED_USER_ID"=>$arTicket["CREATED_USER_ID"],
                "MODIFIED_USER_ID"=>$arTicket["MODIFIED_USER_ID"],
                "CATEGORY_ID"=>$categoryId,
                "PRIORITY_ID"=>$PRIORITY_ID>0 ? $PRIORITY_ID : "",
                "RESPONSIBLE_USER_ID"=>$arTicket["RESPONSIBLE_USER_ID"],
                "DATE_CLOSE"=>new Type\DateTime($arTicket["DATE_CLOSE"]),
                "IS_CLOSE"=>strlen($arTicket["DATE_CLOSE"])>0 ? "Y" : "N",
                "STATUS_ID"=>$statusId,
                'FILES'=>array(),
        );
        if(!in_array($arTicket["OWNER_USER_ID"],$userInfo))
            $userInfo[$arTicket["OWNER_USER_ID"]] = CUser::GetList(($by="id"), ($order="asc"), array("ID_EQUAL_EXACT"=>$arTicket["OWNER_USER_ID"]), array("FIELDS"=>array("ID","LOGIN","EMAIL")))->Fetch();
        
        if(!in_array($arTicket["CREATED_USER_ID"],$userInfo))
            $userInfo[$arTicket["CREATED_USER_ID"]] = CUser::GetList(($by="id"), ($order="asc"), array("ID_EQUAL_EXACT"=>$arTicket["CREATED_USER_ID"]), array("FIELDS"=>array("ID","LOGIN","EMAIL")))->Fetch();
            
        if(!in_array($arTicket["MODIFIED_USER_ID"],$userInfo))
            $userInfo[$arTicket["MODIFIED_USER_ID"]] = CUser::GetList(($by="id"), ($order="asc"), array("ID_EQUAL_EXACT"=>$arTicket["MODIFIED_USER_ID"]), array("FIELDS"=>array("ID","LOGIN","EMAIL")))->Fetch();
        
        if(!in_array($arTicket["RESPONSIBLE_USER_ID"],$userInfo))
            $userInfo[$arTicket["RESPONSIBLE_USER_ID"]] = CUser::GetList(($by="id"), ($order="asc"), array("ID_EQUAL_EXACT"=>$arTicket["RESPONSIBLE_USER_ID"]), array("FIELDS"=>array("ID","LOGIN","EMAIL")))->Fetch();
        
        $skipMess = 0;
        $by = "s_id";
        $order = "asc";
        $obTicketMess = CTicket::GetMessageList($by,$order, array("TICKET_ID" => $arTicket["ID"], "TICKET_ID_EXACT_MATCH" => "Y"), $c, $a = "N");
        while($arTicketMess = $obTicketMess->Fetch())
        {
            $skipMess = $arTicketMess['ID'];
            $arOldTickets[$arTicket['ID']]['MESSAGE'] = $arTicketMess['MESSAGE'];
            $arOldTickets[$arTicket['ID']]['MESSAGE'] = str_replace(
                array('<B>','</B>','<QUOTE>','</QUOTE>','<CODE>','</CODE>','<I>','</I>','<U>','</U>'),
                array('[B]','[/B]','[QUOTE]','[/QUOTE]','[CODE]','[/CODE]','[I]','[/I]','[U]','[/U]'),
            $arTicketMess['MESSAGE']
            );
            $arOldTickets[$arTicket['ID']]['LAST_MESSAGE_DATE'] = new Type\DateTime($arTicketMess['DATE_CREATE']);
            $rsFiles = CTicket::GetFileList($v1="s_id", $v2="asc", array("TICKET_ID" => $arTicket["ID"],'MESSAGE_ID'=>$arTicketMess['ID']));
            {
                    while ($arFile = $rsFiles->Fetch())
                    {
                        $arOldTickets[$arTicket['ID']]['FILES'][] = $arFile['ID'];
                        $filesImp[$arFile['ID']] = CFile::GetByID($arFile['ID'])->Fetch();
                        $filesImp[$arFile['ID']] = array_merge($filesImp[$arFile['ID']],CFile::MakeFileArray($arFile['ID']));
                        unset($filesImp[$arFile['ID']]['ID']);
                        unset($filesImp[$arFile['ID']]['TIMESTAMP_X']);
                    }
            }                
            break;                    
        }                            
    
        $by = "s_id";
        $order = "asc";
        $obTicketMess = CTicket::GetMessageList($by,$order, array("TICKET_ID" => $arTicket["ID"], "TICKET_ID_EXACT_MATCH" => "Y"/*,'IS_LOG'=>'N'*/,'IS_HIDDEN'=>'N'), $c, $a = "N");
        while($arTicketMess = $obTicketMess->Fetch())
        {
            if($arTicketMess["ID"]==$skipMess)
                continue;
    
                $arTicketMessage = Array(
                    'ID'=>$arTicketMess['ID'],
                    'DATE_CREATE'=>new Type\DateTime($arTicketMess['DATE_CREATE']),
                    "CREATED_USER_ID"=>$arTicketMess["CREATED_USER_ID"],
                    "MODIFIED_USER_ID"=>$arTicketMess["MODIFIED_USER_ID"],
                    "TICKET_ID"=>$TICKET_ID,
                    "MESSAGE"=>$arTicketMess["MESSAGE"],
                    'FILES'=>array(),
                );
                
                if(!in_array($arTicketMessage["CREATED_USER_ID"],$userInfo))
                    $userInfo[$arTicketMessage["CREATED_USER_ID"]] = CUser::GetList(($by="id"), ($order="asc"), array("ID_EQUAL_EXACT"=>$arTicketMessage["CREATED_USER_ID"]), array("FIELDS"=>array("ID","LOGIN","EMAIL")))->Fetch();
                    
                if(!in_array($arTicketMessage["MODIFIED_USER_ID"],$userInfo))
                    $userInfo[$arTicketMessage["MODIFIED_USER_ID"]] = CUser::GetList(($by="id"), ($order="asc"), array("ID_EQUAL_EXACT"=>$arTicketMessage["MODIFIED_USER_ID"]), array("FIELDS"=>array("ID","LOGIN","EMAIL")))->Fetch();
                
                if($arTicketMess['IS_LOG'] == 'Y')
                {
                    $arTicketMessage['MESSAGE'] = str_replace(
                        array('<li>'),
                        array('[*]'),
                    $arTicketMessage['MESSAGE']
                    );                         
                    $arTicketMessage['MESSAGE'] = '[LIST]'.$arTicketMessage['MESSAGE'].'[/LIST]';   
                }
                else
                {
                    $arTicketMessage['MESSAGE'] = str_replace(
                        array('<B>','</B>','<QUOTE>','</QUOTE>','<CODE>','</CODE>','<I>','</I>','<U>','</U>'),
                        array('[B]','[/B]','[QUOTE]','[/QUOTE]','[CODE]','[/CODE]','[I]','[/I]','[U]','[/U]'),
                    $arTicketMessage['MESSAGE']
                    );
                }
                $rsFiles = CTicket::GetFileList($v1="s_id", $v2="asc", array("TICKET_ID" => $arTicket["ID"],'MESSAGE_ID'=>$arTicketMess['ID']));
                {
                        while ($arFile = $rsFiles->Fetch())
                        {
                            $arTicketMessage['FILES'][] = $arFile['ID'];
                            $filesImp[$arFile['ID']] = CFile::GetByID($arFile['ID'])->Fetch();
                            $filesImp[$arFile['ID']] = array_merge($filesImp[$arFile['ID']],CFile::MakeFileArray($arFile['ID']));
                            unset($filesImp[$arFile['ID']]['ID']);
                            unset($filesImp[$arFile['ID']]['TIMESTAMP_X']);
                        }
                }                        
                //p($arTicketMessage);
                $arOldTicketsMessages[$arTicket["ID"]][] = $arTicketMessage;
        }                
            $i++;
    }
}
$DB->Disconnect();
$DB->Connect($DBHost, $DBName, $DBLogin, $DBPassword);

/*Array(
    "OWNER_USER_ID"=>$arTicket["OWNER_USER_ID"],
    "CREATED_USER_ID"=>$arTicket["CREATED_USER_ID"],
    "MODIFIED_USER_ID"=>$arTicket["MODIFIED_USER_ID"],
    "RESPONSIBLE_USER_ID"=>$arTicket["RESPONSIBLE_USER_ID"],
);
*/
    
$userExist = array();
$userReplace = array();
foreach($arOldTickets as $tid=>$add)
{
    if(!in_array($add['OWNER_USER_ID'],$userExist))
    {
        if($user = CUser::GetList(($by="id"), ($order="asc"), array("LOGIN_EQUAL_EXACT"=>$userInfo[$add['OWNER_USER_ID']]['LOGIN']), array("FIELDS"=>array("ID","LOGIN","EMAIL")))->Fetch())
        {
            if($user['ID']!=$add['OWNER_USER_ID'])
                $userReplace[$add['OWNER_USER_ID']] = $user['ID'];
        }
        else
        {
            $userReplace[$add['OWNER_USER_ID']] = $specialUser;
        }
        $userExist[] = $add['OWNER_USER_ID'];        
    }
    
    if(!in_array($add['CREATED_USER_ID'],$userExist))
    {
        if($user = CUser::GetList(($by="id"), ($order="asc"), array("LOGIN_EQUAL_EXACT"=>$userInfo[$add['CREATED_USER_ID']]['LOGIN']), array("FIELDS"=>array("ID","LOGIN","EMAIL")))->Fetch())
        {
            if($user['ID']!=$add['CREATED_USER_ID'])
                $userReplace[$add['CREATED_USER_ID']] = $user['ID'];
        }
        else
        {
            $userReplace[$add['CREATED_USER_ID']] = $specialUser;
        }
        $userExist[] = $add['CREATED_USER_ID'];
    }
    
    if(!in_array($add['MODIFIED_USER_ID'],$userExist))
    {
        if($user = CUser::GetList(($by="id"), ($order="asc"), array("LOGIN_EQUAL_EXACT"=>$userInfo[$add['MODIFIED_USER_ID']]['LOGIN']), array("FIELDS"=>array("ID","LOGIN","EMAIL")))->Fetch())
        {
            if($user['ID']!=$add['MODIFIED_USER_ID'])
                $userReplace[$add['MODIFIED_USER_ID']] = $user['ID'];
        }
        else
        {
            $userReplace[$add['MODIFIED_USER_ID']] = $specialUser;
        }
        $userExist[] = $add['MODIFIED_USER_ID'];        
    }
    
    if(!in_array($add['RESPONSIBLE_USER_ID'],$userExist))
    {
        if($user = CUser::GetList(($by="id"), ($order="asc"), array("LOGIN_EQUAL_EXACT"=>$userInfo[$add['RESPONSIBLE_USER_ID']]['LOGIN']), array("FIELDS"=>array("ID","LOGIN","EMAIL")))->Fetch())
        {
            if($user['ID']!=$add['RESPONSIBLE_USER_ID'])
                $userReplace[$add['RESPONSIBLE_USER_ID']] = $user['ID'];
        }
        else
        {
            $userReplace[$add['RESPONSIBLE_USER_ID']] = $specialUser;
        }
        $userExist[] = $add['RESPONSIBLE_USER_ID'];        
    }            

    if(array_key_exists($add['OWNER_USER_ID'],$userReplace))
        $add['OWNER_USER_ID'] = $userReplace[$add['OWNER_USER_ID']];
    if(array_key_exists($add['CREATED_USER_ID'],$userReplace))
        $add['CREATED_USER_ID'] = $userReplace[$add['CREATED_USER_ID']];
    if(array_key_exists($add['MODIFIED_USER_ID'],$userReplace))
        $add['MODIFIED_USER_ID'] = $userReplace[$add['MODIFIED_USER_ID']];
    if(array_key_exists($add['RESPONSIBLE_USER_ID'],$userReplace))
        $add['RESPONSIBLE_USER_ID'] = $userReplace[$add['RESPONSIBLE_USER_ID']];                    
    
    $add['IP'] = '127.0.0.1';
    if(count($add['FILES'])>0)
    {
        $filesToAdd = array();
        foreach($add['FILES'] as $fid)
        {
            $filesToAdd[] = CFile::SaveFile($filesImp[$fid]);
        }
        $add['FILES'] = $filesToAdd;
    }
    
    $result = Support\TicketTable::add($add);
    $TICKET_ID = $result->getId();
    if($result->isSuccess())
    {
        foreach($arOldTicketsMessages[$tid] as $mess)
        {
            if(!in_array($mess['CREATED_USER_ID'],$userExist))
            {
                if($user = CUser::GetList(($by="id"), ($order="asc"), array("LOGIN_EQUAL_EXACT"=>$userInfo[$mess['CREATED_USER_ID']]['LOGIN']), array("FIELDS"=>array("ID","LOGIN","EMAIL")))->Fetch())
                {
                    if($user['ID']!=$mess['CREATED_USER_ID'])
                        $userReplace[$mess['CREATED_USER_ID']] = $user['ID'];
                }
                else
                {
                    $userReplace[$mess['CREATED_USER_ID']] = $specialUser;
                }
                $userExist[] = $mess['CREATED_USER_ID'];
            }
            
            if(!in_array($mess['MODIFIED_USER_ID'],$userExist))
            {
                if($user = CUser::GetList(($by="id"), ($order="asc"), array("LOGIN_EQUAL_EXACT"=>$userInfo[$mess['MODIFIED_USER_ID']]['LOGIN']), array("FIELDS"=>array("ID","LOGIN","EMAIL")))->Fetch())
                {
                    if($user['ID']!=$mess['MODIFIED_USER_ID'])
                        $userReplace[$mess['MODIFIED_USER_ID']] = $user['ID'];
                }
                else
                {
                    $userReplace[$mess['MODIFIED_USER_ID']] = $specialUser;
                }
                $userExist[] = $mess['MODIFIED_USER_ID'];        
            }            
            if(array_key_exists($mess['CREATED_USER_ID'],$userReplace))
                $mess['CREATED_USER_ID'] = $userReplace[$mess['CREATED_USER_ID']];
            if(array_key_exists($mess['MODIFIED_USER_ID'],$userReplace))
                $mess['MODIFIED_USER_ID'] = $userReplace[$mess['MODIFIED_USER_ID']];
            
            $mess['TICKET_ID'] = $TICKET_ID;
            
            if(count($mess['FILES'])>0)
            {
                $filesToAdd = array();
                foreach($mess['FILES'] as $fid)
                {
                    $filesToAdd[] = CFile::SaveFile($filesImp[$fid]);
                }
                $mess['FILES'] = $filesToAdd;
            }            
            Support\TicketMessageTable::add($mess);
        }
    }
    else
    {
        echo "<pre>";print_r($add);echo "</pre>";
        var_dump($result->getErrorMessages());
        die();
    }
}
?>