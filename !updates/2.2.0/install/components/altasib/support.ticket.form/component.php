<?php
/**
 * ALTASIB
 * @site http://www.altasib.ru
 * @email dev@altasib.ru
 * @copyright 2006-2018 ALTASIB
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use ALTASIB\Support;
use Bitrix\Main;

if(!Main\Loader::includeModule("altasib.support"))
{
        ShowError("ALTASIB_SUPPORT_MODULE_NOT_INSTALL");
        return;
}
$arParams["ID"] = (int)$arParams["ID"];

if(!($arParams['Right'] instanceof ALTASIB\Support\Rights))
    $arParams['Right'] = new ALTASIB\Support\Rights($USER->GetID(),$arParams['ID']);

$arParams["ROLE"] = $Role = $arParams['Right']->getRole();
if($arParams['Right']->getRole() == 'D')
    $APPLICATION->AuthForm('');

$arParams['ALLOW'] = ($arParams["ROLE"]>= 'W');
$arParams['IS_SUPPORT_TEAM'] = $arParams['Right']->isSupportTeam();
$arParams["REPLY_ON"] = COption::GetOptionString("altasib.support","REPLY_ON","Y");
        
$arResult = $this->__parent->arResult;

$arParams["USER_FIELDS"] = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("ALTASIB_SUPPORT",0,LANGUAGE_ID);

$arParams["PULL_TAG"] = 'ALTASIB_SUPPORT_'.$arParams["ID"];
$arParams["PULL_TAG_SUPPORT"] = 'ALTASIB_SUPPORT_'.$arParams["ID"].'_SUPPORT';
$arParams["PULL_TAG_SUPPORT_ADMIN"] = 'ALTASIB_SUPPORT_'.$arParams["ID"].'_SUPPORT_ADMIN';
$arParams['SHOW_FULL_FORM'] = ($arParams['SHOW_FULL_FORM']=='Y');
    
$request = Main\Context::getCurrent()->getRequest();
$ajax = false;
if(isset($request['SUPPORT_AJAX']) && $request['SUPPORT_AJAX']=='Y')
{
    $ajax = true;
    $APPLICATION->RestartBuffer();
    CUtil::JSPostUnescape();
}
    //edit check
    if ($request->isPost() && check_bitrix_sessid() && isset($request['edit_support_message_id']) && $request['edit_support_message_id']>0 && isset($request['check_support_message_edit']))
    {
        $arParams['edit_support_message_id'] = (int)$request['edit_support_message_id'];
        $message = Support\TicketMessageTable::getRowById($arParams['edit_support_message_id']);
        CTimeZone::Disable();
        if($arParams['ALLOW'] || ($message['CREATED_USER_ID']==$USER->GetID() && (time()<AddToTimeStamp(array('MI'=>5),MakeTimeStamp($message['DATE_CREATE'], "DD.MM.YYYY HH:MI:SS")))))
        {
            echo CUtil::PhpToJSObject(array('result'=>true)); 
        }
        else
            echo CUtil::PhpToJSObject(array('result'=>false));
            
        die();
    }
        //edit
        if ($request->isPost() && check_bitrix_sessid() && isset($request['edit_support_message_id']) && $request['edit_support_message_id']>0)
        {
            $editProcess = isset($request['ACTION']);
            if($editProcess)
                $FILES = $_POST["FILES"];
                
            $arParams['edit_support_message_id'] = (int)$request['edit_support_message_id'];
            $message = Support\TicketMessageTable::getRowById($arParams['edit_support_message_id']);
            
            CTimeZone::Disable();
            if(!$arParams['ALLOW'] && time()>AddToTimeStamp(array('MI'=>5),MakeTimeStamp($message['DATE_CREATE'], "DD.MM.YYYY HH:MI:SS")))
                return;
            CTimeZone::Enable();
            
            if(!$arParams['ALLOW'] && $message['CREATED_USER_ID']!=$USER->GetID() && !$arParams['IS_SUPPORT_TEAM'])
                return;
            else
            {
                if(!$editProcess)
                    $arResult['MESSAGE'] = $message;
                
                $files = Support\FileTable::getList(array('filter'=>array('MESSAGE_ID'=>$arParams['edit_support_message_id'])));
                while($arFile = $files->fetch())
                {
                    $arResult['FILES_EDIT_VALUE'][] = $arFile['FILE_ID'];
                    if($editProcess)
                    {
                        $keyDelFile = array_search($arFile['FILE_ID'],$FILES);
                        if($keyDelFile !== false)
                            unset($FILES[$keyDelFile]);
                            
                        if(!CFile::GetFileArray($arFile['FILE_ID']))
                            Support\FileTable::delete($arFile['ID']);
                    }
                }
                
                if($editProcess)
                {
                    $dataUpdate = array(
                        'TICKET_ID' => (int)$request["TICKET_ID"],
                        'MESSAGE' => $_POST['MESSAGE_e'],
                        'FILES' => $FILES,
                    );
                    $updRes = Support\TicketMessageTable::update($arParams['edit_support_message_id'],$dataUpdate);
                    if($updRes->isSuccess())
                    {
                            ?>
                            <script bxrunfirst="true">
                            top.BX.WindowManager.Get().Close();
                            top.BX.showWait();
                            top.BX.reload('<?=CUtil::JSEscape(str_replace('#ID#',$request["TICKET_ID"],$arParams['URL_DETAIL']));?>', true);
                            </script>
                            <?   
                            die();                    
                    }
                    else
                    {
                        $arResult["ERRORS"] = $updRes->getErrorMessages();
                    }
                }
            }
        } 

                // process POST
        if ($request->isPost() && check_bitrix_sessid() && (!empty($request['SEND_MESSAGE']) || !empty($request["t_submit"]) || !empty($request["t_submit_go"])) && ($arParams["HAVE_ANSWER"] || $arParams['ID']==0 && $arParams["HAVE_CREATE"]))
        {
                $TICKET_ID = (int)$request["TICKET_ID"];
                if($TICKET_ID==0)
                {
                    //UF
                    $arUserFields = Array();
                    foreach ($arParams["USER_FIELDS"] as $FIELD_NAME => $arPostField)
                    {
                    
                        if($arPostField["EDIT_IN_LIST"]=="Y")
                        {
                            if($arPostField["USER_TYPE"]["BASE_TYPE"]=="file")
                            {
                                $arUserFields[$arPostField["FIELD_NAME"]] = $_FILES[$arPostField["FIELD_NAME"]];
                                $arUserFields[$arPostField["FIELD_NAME"]]["del"] = $_POST[$arPostField["FIELD_NAME"]."_del"];
                                $arUserFields[$arPostField["FIELD_NAME"]]["old_id"] = $old_id;
                            }
                            else
                                $arUserFields[$arPostField["FIELD_NAME"]] = $_POST[$arPostField["FIELD_NAME"]];
                        }
                    }              
                    
                    $arTicket = Array(
                        'TITLE' =>$request["TITLE"],
                        'CATEGORY_ID' =>(int)$request["CATEGORY_ID"],
                        'PRIORITY_ID' =>(int)$request["PRIORITY_ID"],
                        'MESSAGE' =>$_POST['MESSAGE'],
                        'FILES'=>$request["FILES"],
                    );
                    
                    if($arParams['ALLOW'] && (int)$request['PARRENT_MESSAGE_ID']>0)
                    {
                        $parentMessage = Support\TicketMessageTable::getRow(array(
                                    'filter'=>array('ID'=>$request['PARRENT_MESSAGE_ID']),
                                    'select'=>array('TICKET_OWNER_USER_ID'=>'TICKET.OWNER_USER_ID')
                                )); 
                        
                        $arTicket['OWNER_USER_ID'] = $parentMessage['TICKET_OWNER_USER_ID'];
                    }
                    else
                    {
                        if($arParams['ALLOW'] && IsModuleInstalled('intranet') && is_array($request['OWNER_ID']) && count($request['OWNER_ID'])>0)
                            $arTicket['OWNER_USER_ID'] = array_shift($request['OWNER_ID']);
                        elseif(($arParams['ALLOW'] || Support\ClientTable::getList(array('filter'=>array('RESPONSIBLE_USER_ID'=>$USER->GetID())))) && (int)$request['OWNER_ID']>0)
                            $arTicket['OWNER_USER_ID'] = (int)$request['OWNER_ID'];
                    }
                    
                    if(IsModuleInstalled('intranet'))
                    {
                        //group
                        if($arParams['GROUP_ID']>0)
                            $arTicket['GROUP_ID'] = $arParams['GROUP_ID']; 
                        else
                        {
                            if($request['GROUP_ID']>0)
                            {
                                if($arParams['ROLE'] == 'C')
                                {
                                    $group = CSocNetUserToGroup::GetList(
                                    	array("GROUP_NAME" => "ASC"),
                                        array(
                                    			"USER_ID" => $USER->GetID(),
                                    			"GROUP_ACTIVE" => "Y",
                                                "<=ROLE" => SONET_ROLES_MODERATOR
                                    		),
                                    	false,
                                    	false,
                                    	array("ID")
                                    );
                                    if($group->Fetch())
                                    {
                                        $arTicket['GROUP_ID'] = $request['GROUP_ID'];
                                    }                                    
                                }
                                if($arParams['ROLE'] == 'W')
                                    $arTicket['GROUP_ID'] = $request['GROUP_ID'];
                            }
                        }
                    }
                    
                    $arTicket = array_merge($arTicket,$arUserFields);
                    if($arParams['isWorker'] && !in_array($request["CATEGORY_ID"],$arParams['HAVE_CREATE_TO_CAREGORY']))
                        $arResult["ERRORS"][] = GetMessage('ALTASIB_SUPPORT_ERROR_CATEGORY');
                    
                    if(empty($arResult["ERRORS"]))
                    {
                        if ($arParams["USE_CAPTCHA"] == "Y" && $arParams["ID"] <= 0)
                        {
                                if (!$APPLICATION->CaptchaCheckCode($_REQUEST["captcha_word"], $_REQUEST["captcha_sid"]))
                                {
                                        $arResult["ERRORS"] .= GetMessage("I_RECEPTION_FORM_WRONG_CAPTCHA");
                                }
                        }

                        $result = Support\TicketTable::add($arTicket);
                        $TICKET_ID = $result->getId();
                        if(!$result->isSuccess())
                        {
                                $arResult = $arTicket;
                                if($arParams['ALLOW'] && (int)$request['PARRENT_MESSAGE_ID']>0)
                                {
                                    $arResult['PARRENT_MESSAGE_ID'] = $request['PARRENT_MESSAGE_ID'];
                                }
                                $arResult["ERRORS"] = $result->getErrorMessages();
                        }
                        else
                        {
                            //if create task
                            if (Main\Loader::includeModule("tasks") && $arParams['USE_TASK'])
                            {
                                $ticketData = Support\TicketTable::getList(array('filter'=>array('ID'=>$TICKET_ID),'select'=>array('RESPONSIBLE_USER_ID')))->fetch();
                                if($ticketData['RESPONSIBLE_USER_ID']>0)
                                {
                                    $arFields = Array(
                                        "TITLE" => GetMessage('ALTASIB_SUPPORT_TASK_PLAN',array('#ID#'=>$TICKET_ID,'#NAME#'=>$request["TITLE"])),        
                                        "DESCRIPTION" => $request["MESSAGE"].GetMessage('ALTASIB_SUPPORT_TASK_PLAN_DETAIL',array('#URL#'=>str_replace("#ID#",$TICKET_ID,$arParams["URL_DETAIL"]))),        
                                        "CREATED_BY" => $ticketData['RESPONSIBLE_USER_ID'],
                                        "RESPONSIBLE_ID" => $ticketData['RESPONSIBLE_USER_ID'],        
                                        "GROUP_ID" => 54,
                                    );
                                        
                                    $obTask = new CTasks;    
                                    $taskID = $obTask->Add($arFields);
                                    Support\Tools::taskPlanner($taskID,$ticketData['RESPONSIBLE_USER_ID']);
                                    Support\TicketTable::update($TICKET_ID,array('TASK_ID'=>$taskID));
                                }    
                            }
                            $_SESSION["TICKET_OK"] = true;
                            if($arTicket['GROUP_ID']==0)
                                LocalRedirect(str_replace("#ID#",$TICKET_ID,$arParams["URL_DETAIL"]));
                            else
                                LocalRedirect(str_replace(array("#ID#",'#TICKET_ID#','#group_id#'),array($TICKET_ID,$TICKET_ID,$arTicket['GROUP_ID']),COption::GetOptionString('altasib.support','path_group_detail')));
                        }
                    }
                    else
                        $arResult = $arTicket;
                }
                else
                {
                        if($request["OPEN"]=="Y")
                        {
                            if($GLOBALS["USER"]->IsAuthorized() && CModule::IncludeModule("pull") && CPullOptions::GetNginxStatus())
                            {
                                $pullParams = array(
                                    'TICKET_ID' => $TICKET_ID,
                                );                                
                               	CPullWatch::AddToStack($arParams["PULL_TAG"],
                                		Array(
                                			'module_id' => 'altasib.support',
                                			'command' => 'open',
                                			'params' => $pullParams
                                		)
                                	);
                            	CPullWatch::AddToStack($arParams["PULL_TAG_SUPPORT"],
                            		Array(
                            			'module_id' => 'altasib.support',
                            			'command' => 'open',
                            			'params' => $pullParams
                            		)
                            	);
                            	CPullWatch::AddToStack($arParams["PULL_TAG_SUPPORT_ADMIN"],
                            		Array(
                            			'module_id' => 'altasib.support',
                            			'command' => 'open',
                            			'params' => $pullParams
                            		)
                            	);                                                                
                            }                            
                            Support\TicketTable::close($TICKET_ID);
                            if(!empty($request["t_submit_go"]))
                                LocalRedirect($arParams["TICKET_LIST_URL"]);
                            else
                                LocalRedirect(str_replace("#ID#",$TICKET_ID,$arParams["URL_DETAIL"]));
                        }
                        
                        $arTicketMessage = Array(
                                "TICKET_ID"=>$TICKET_ID,
                                "MESSAGE"=>$_POST["MESSAGE"],
                                'IS_HIDDEN'=>'N',
                                "FILES"=>$request["FILES"],
                                'CLOSE' => $request["CLOSE"],
                                'IS_DEFERRED' => $request["IS_DEFERRED"]
                        );
                        
                        //preg_match_all('/\[IMG(.+)](.+)\[\/IMG]/i',$arTicketMessage['MESSAGE'],$paste_img);
                        preg_match_all('/\[img((.+)|)](.+)\[\/img]/i',$arTicketMessage['MESSAGE'],$paste_img);
                        if(count($paste_img[3])>0)
                        {
                            foreach($paste_img[3] as $k=>$img)
                            {
                                if(strstr($img,'data:image'))
                                {
                                    $arTicketMessage['MESSAGE'] = str_replace($paste_img[0][$k],'&nbsp;',$arTicketMessage['MESSAGE']);
                                    $type = 'png';
                                    if(strstr($img,'data:image/jpg;base64'))
                                        $type = 'jpg';
                                    if(strstr($img,'data:image/gif;base64'))
                                        $type = 'gif';

                                    $pImg = str_replace(' ', '+',str_replace('data:image/'.$type.';base64,', '', $img));
                                    $arTicketMessage['FILES'][] = CFile::SaveFile(array(
                                        'name' => uniqid().'.'.$type,
                                        'type'=>'image/jpeg',
                                        'content' => base64_decode($pImg),
                                        'MODULE_ID' => 'altasib.support'
                                    ),'altasib.support/base64',true);
                                }
                            }
                        }
                        
                        $close_ex = false;
                        if($arParams['IS_SUPPORT_TEAM'])
                        {
                            $arTicketMessage['NOT_CHANGE'] = $request['NOT_CHANGE']=='Y' ? 'Y' : 'N';
                            $arTicketMessage['IS_HIDDEN'] = $request['IS_HIDDEN']=='Y' ? 'Y' : 'N';
                            
                            if($request['CLOSE_EX']=='Y')
                            {
                                $arTicketMessage['CLOSE_EX'] = $request['CLOSE_EX'];
                                $close_ex = true;
                            }
                        }

                        if(strlen($arTicketMessage['MESSAGE']) == 0 && $request["CLOSE"]=='Y')
                            $close_ex = true;
                        
                        $result = Support\TicketMessageTable::add($arTicketMessage);
                        if(!$close_ex)
                            $TICKET_MESSAGE_ID = $result->getId();

                        if(!$result->isSuccess())
                        {
                                $arResult["ERRORS"] = $result->getErrorMessages();
                                if($ajax)
                                {
                                    echo CUtil::PhpToJSObject(array('status'=>false,'error'=>implode('<br />',$arResult["ERRORS"])));
                                    die();
                                }                                
                        }
                        else
                        {
                            if(!$close_ex)
                            {       
                                $pull = true;
                                if (!isset($request['AJAX_CALL']) && $GLOBALS["USER"]->IsAuthorized() && CModule::IncludeModule("pull") && CPullOptions::GetNginxStatus())
                                {
                                    $pullParams = array(
                                        'ID'=>$TICKET_MESSAGE_ID,
                                        'TICKET_ID' => $TICKET_ID,
                                        'IS_HIDDEN' => $arTicketMessage['IS_HIDDEN']
                                        );
                                    if(!function_exists('getMessageSupport'))
                                        include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/altasib/support.ticket.detail/templates/.default/message_template.php');
                                    
                                    $dataTicket = Support\TicketTable::getRow(array(
                                        'filter'=>array('ID'=>$TICKET_ID),
                                        'select'=>array('*')
                                    ));
                                    $dataTicketMessage = Support\TicketMessageTable::getRow(array(
                                        'filter'=>array('ID'=>$TICKET_MESSAGE_ID),
                                        'select'=>array('*','CREATED_USER_NAME'=>'CREATED_USER.NAME','CREATED_USER_LAST_NAME'=>'CREATED_USER.LAST_NAME','CREATED_USER_SHORT_NAME'=>'CREATED_USER.SHORT_NAME')
                                    ));
                                    
                                    if($arTicketMessage['IS_HIDDEN']=='N')
                                    {
                                        $dataTicketMessage['pull_type'] = Support\Tools::$PULL_TYPE_CUSTOMER;
                                        $pullParams['MESSAGE'] = getMessageSupport($dataTicketMessage,$arParams,$dataTicket);
                                    	CPullWatch::AddToStack($arParams["PULL_TAG"],
                                    		Array(
                                    			'module_id' => 'altasib.support',
                                    			'command' => 'message',
                                    			'params' => $pullParams
                                    		)
                                    	);
                                    }
                                    
                                    $dataTicketMessage['pull_type'] = Support\Tools::$PULL_TYPE_SUPPORT_TEAM;
                                    $pullParams['MESSAGE'] = getMessageSupport($dataTicketMessage,$arParams,$dataTicket);                                    
                                	CPullWatch::AddToStack($arParams["PULL_TAG_SUPPORT"],
                                		Array(
                                			'module_id' => 'altasib.support',
                                			'command' => 'message',
                                			'params' => $pullParams
                                		)
                                	);
                                    
                                    $dataTicketMessage['pull_type'] = Support\Tools::$PULL_TYPE_SUPPORT_TEAM_ADMIN;
                                    $pullParams['MESSAGE'] = getMessageSupport($dataTicketMessage,$arParams,$dataTicket);                                    
                                	CPullWatch::AddToStack($arParams["PULL_TAG_SUPPORT_ADMIN"],
                                		Array(
                                			'module_id' => 'altasib.support',
                                			'command' => 'message',
                                			'params' => $pullParams
                                		)
                                	);                                    
                                }
                                else
                                    $pull = false;                            
                                
                                Support\Tools::taskPlannerProcess($TICKET_ID,Support\Tools::IsSupportTeam($USER->GetID()));
                            }
                            
                            if(($request["CLOSE"]=="Y" || $close_ex) && $GLOBALS["USER"]->IsAuthorized() && CModule::IncludeModule("pull") && CPullOptions::GetNginxStatus())
                            {
                                $pullParams = array(
                                    'TICKET_ID' => $TICKET_ID,
                                );                                
                               	CPullWatch::AddToStack($arParams["PULL_TAG"],
                                		Array(
                                			'module_id' => 'altasib.support',
                                			'command' => 'close',
                                			'params' => $pullParams
                                		)
                                	);
                            	CPullWatch::AddToStack($arParams["PULL_TAG_SUPPORT"],
                            		Array(
                            			'module_id' => 'altasib.support',
                            			'command' => 'close',
                            			'params' => $pullParams
                            		)
                            	);
                                
                            	CPullWatch::AddToStack($arParams["PULL_TAG_SUPPORT_ADMIN"],
                            		Array(
                            			'module_id' => 'altasib.support',
                            			'command' => 'close',
                            			'params' => $pullParams
                            		)
                            	);                                
                                                                
                            }
                            if($ajax)
                            {
                                $obRes = array('status'=>true,'error'=>'','messageId'=>$TICKET_MESSAGE_ID);
                                if(!empty($request["t_submit_go"]))
                                {
                                    $obRes['redirect'] = true;
                                    $obRes['redirect_url'] = $arParams["TICKET_LIST_URL"];
                                }
                                elseif($request["CLOSE"]=="Y" || $close_ex || !$pull)
                                {
                                    $obRes['redirect'] = true;
                                    $obRes['redirect_url'] = str_replace("#ID#",$TICKET_ID,$arParams["URL_DETAIL"]);
                                    if(!$pull)
                                    {
                                        $_SESSION["TICKET_MESSAGE_OK"] = true;
                                    }                                    
                                }
                                
                                echo CUtil::PhpToJSObject($obRes);
                                die();
                            }
                            else
                            {
                                $_SESSION["TICKET_MESSAGE_OK"] = true;
                                if(!empty($request["t_submit_go"]))
                                    LocalRedirect($arParams["TICKET_LIST_URL"]);
                                else
                                    LocalRedirect(str_replace("#ID#",$TICKET_ID,$arParams["URL_DETAIL"]));
                            }
                        }
                }
        }

        if($arParams["ID"]>0)
            $arResult["IS_CLOSE"] = Support\TicketTable::isClose($arParams['ID']);

        // prepare captcha
        if ($arParams["USE_CAPTCHA"] == "Y" && $arParams["ID"] == 0)
        {
                $arResult["CAPTCHA_CODE"] = htmlspecialchars($APPLICATION->CaptchaGetCode());
        }
        if($arParams["ID"]==0)
        {
                $arResult["CATEGORY"] = Array();
                $obCategory = Support\CategoryTable::getList();
                while($arCategory = $obCategory->fetch())
                {
                    if($arParams['isWorker'] && !in_array($arCategory['ID'],$arParams['HAVE_CREATE_TO_CAREGORY']))
                        continue;
                        
                    $arResult["CATEGORY"][$arCategory["ID"]] = $arCategory;
                }
                
                $arResult["PRIORITY"] = Support\Priority::get();
                $arResult['SLA'] = Support\SlaTable::getUserSla($USER->GetID());
                
                if($arParams['ALLOW'] && (int)$request['PARRENT_MESSAGE']>0 && empty($arResult['MESSAGE']))
                {
                    $parentMessage = Support\TicketMessageTable::getRow(array(
                                'filter'=>array('ID'=>$request['PARRENT_MESSAGE']),
                                'select'=>array('*','TICKET_CATEGORY_ID'=>'TICKET.CATEGORY_ID')
                            )); 
                    $arResult['PARRENT_MESSAGE_ID'] = $request['PARRENT_MESSAGE'];
                    $arResult['MESSAGE'] = $parentMessage['MESSAGE'];
                    $arResult['CATEGORY_ID'] =$parentMessage['TICKET_CATEGORY_ID'];
                    
                }
                $arParams['CUSTOMER_LIST'] = array();
                if($arParams['ALLOW'])
                {
                    $arParams['CUSTOMER_LIST'] = Support\Tools::getCustomerList();
                }
                else
                {
                    $arParams['CUSTOMER_LIST'] = array();
                    $clientData = Support\ClientTable::getList(array('filter'=>array('RESPONSIBLE_USER_ID'=>$USER->GetID())));
                    while($client = $clientData->Fetch())
                    {
                        $userData = \Bitrix\Main\UserTable::getRow(array('filter'=>array('ID'=>$client['USER_ID'])));
                        $arParams['CUSTOMER_LIST'][$userData["ID"]] = "[".$userData["LOGIN"]."] ".$userData["NAME"]." ".$userData["LAST_NAME"];
                    }
                }
        }
        
        if($arParams['IS_SUPPORT_TEAM'])
        {
            $arParams['QuickResponse'] = Support\QuickResponseTable::getList()->fetchAll();
        }
        
        $arParams['SHOW_GROUP_SELECTOR'] = false;
        if(IsModuleInstalled('intranet'))
            $arParams['SHOW_GROUP_SELECTOR'] = true;

        $this->IncludeComponentTemplate();
        
CJSCore::Init(array('fx'));        
?>