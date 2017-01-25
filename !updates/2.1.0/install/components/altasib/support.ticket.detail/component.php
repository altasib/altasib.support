<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Evgenió Pedan                    #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2016 ALTASIB             #
#################################################
?>
<?
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
    $arParams['Right'] = new ALTASIB\Support\Rights($USER->getId(),$arParams['ID']);

$arParams["ROLE"] = $Role = $arParams['Right']->getRole();
if($arParams['Right']->getRole() == 'D')
    $APPLICATION->AuthForm();

$arParams['IS_SUPPORT_TEAM'] = $arParams['Right']->isSupportTeam();
$arParams['ALLOW'] = ($arParams["ROLE"]>= 'W');
            
$arParams['SHOW_GROUP_SELECTOR'] = false;
if(IsModuleInstalled('intranet'))
    $arParams['SHOW_GROUP_SELECTOR'] = true;
    
$arParams['SHOW_DEAL_SELECTOR'] = false;    
if(IsModuleInstalled('crm') && $arParams["ROLE"]=='W')
    $arParams['SHOW_DEAL_SELECTOR'] = true;
    
$arResult = $this->__parent->arResult;

$arParams["PULL_TAG"] = 'ALTASIB_SUPPORT_'.$arParams["ID"];
$arParams["PULL_TAG_SUPPORT"] = 'ALTASIB_SUPPORT_'.$arParams["ID"].'_SUPPORT';
$arParams["PULL_TAG_SUPPORT_ADMIN"] = 'ALTASIB_SUPPORT_'.$arParams["ID"].'_SUPPORT_ADMIN';

$arParams['PART_LOAD'] = false;
$arParams['MESSAGE_LIMIT'] = 5;
$arParams['HIGHLIGHT_MESSAGE_ID'] = 0;
$arParams["CREATE_BY_MESSAGE_URL"] = htmlspecialchars(CComponentEngine::MakePathFromTemplate($arParams["URL_DETAIL"], Array("ID" => "0","TICKET_ID"=>0,"CODE"=>0))).'?PARRENT_MESSAGE=';
if($arParams["ID"]>0)
{
    if($arParams['Right']->isSupportTeam() && CModule::IncludeModule('extranet') && CExtranet::IsExtranetSite())
    {
		$rsCurrentUser = CUser::GetById($USER->GetId());
		if (
			($arCurrentUser = $rsCurrentUser->Fetch())
			&& !empty($arCurrentUser["UF_DEPARTMENT"])
			&& is_array($arCurrentUser["UF_DEPARTMENT"])
			&& intval($arCurrentUser["UF_DEPARTMENT"][0]) > 0
		)
		{
			$arRedirectSite = CSocNetLogComponent::GetSiteByDepartmentId($arCurrentUser["UF_DEPARTMENT"]);
			if ($arRedirectSite["LID"] != SITE_ID)
				if($arParams['Right']->isSupportTeam())
                    LocalRedirect(\ALTASIB\Support\Event::getURL($arParams['ID'],$arRedirectSite["LID"]));
		}
    }
    
    $arParams["USER_FIELDS"] = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("ALTASIB_SUPPORT",$arParams["ID"],LANGUAGE_ID);
    $arParams["USER_FIELDS_SHOW"] = false; 
    
        $select = array('*',
            'CATEGORY_NAME'=>'CATEGORY.NAME',
            'STATUS_NAME'=>'STATUS.NAME',
            'OWNER_USER_SHORT_NAME'=>'OWNER_USER.SHORT_NAME',
            'OWNER_USER_NAME'=>'OWNER_USER.NAME',
            'OWNER_USER_LAST_NAME'=>'OWNER_USER.LAST_NAME',
            'OWNER_USER_LOGIN'=>'OWNER_USER.LOGIN',
            'RESPONSIBLE_USER_SHORT_NAME'=>'RESPONSIBLE_USER.SHORT_NAME',
            'SLA_NAME'=>'SLA.NAME',
            'SLA_DESCRIPTION'=>'SLA.DESCRIPTION',
            'SLA_RESPONSE_TIME'=>'SLA.RESPONSE_TIME',
            'SUM_ELAPSED_TIME',
            //'GROUP_NAME' => 'GROUP.NAME',
            //'GROUP_OWNER_ID' => 'GROUP.OWNER_ID',
        );
        if(IsModuleInstalled('intranet') && Main\Loader::includeModule("socialnetwork"))
        {
            $select['GROUP_NAME'] = 'GROUP.NAME';
            $select['GROUP_OWNER_ID'] = 'GROUP.OWNER_ID';
        }    
        foreach($arParams["USER_FIELDS"] as $k=>$v)
        {
            $select[] = $k;
            if(strlen($v['VALUE'])>0)
                $arParams["USER_FIELDS_SHOW"] = true;
        }
        $obTicket = Support\TicketTable::getList(array('filter'=>array('ID'=>$arParams["ID"]),'select' => $select));
        if(!$ticket = $arTicket = $obTicket->Fetch())
        {
            $arParams["ID"] = 0;
            $APPLICATION->AuthForm(GetMessage('ALTASIB_SUPPORT_CMP_TICKET_NOT_FOUND'));
        return false;
        }
        else
        {
            $request = Main\Context::getCurrent()->getRequest();
            $arParams['PART_LOAD'] = ($request['PART_LOAD']=='Y');
            if($request->isPost() && !check_bitrix_sessid() && $request['AJAX_CALL']=='Y')
            {
                $APPLICATION->RestartBuffer();
                echo CUtil::PhpToJSObject(array('exp'=>true,'sessid'=>bitrix_sessid()));
                die();
            }
            if ($request->isPost() && check_bitrix_sessid() && $request['AJAX_CALL']=='Y' && $arParams['PART_LOAD'])
            {
                $APPLICATION->RestartBuffer();
                if(!function_exists('getMessageSupport'))
                    include_once($_SERVER['DOCUMENT_ROOT'].'/local/components/altasib/support.ticket.detail/templates/.default/message_template.php');
            }
            elseif($request->isPost() && check_bitrix_sessid() && $request['AJAX_CALL']=='Y')
            {
            	$APPLICATION->RestartBuffer();
                require_once("ajax.php");
                die();                
            }
            if (!isset($request['AJAX_CALL']) && $GLOBALS["USER"]->IsAuthorized() && CModule::IncludeModule("pull") && CPullOptions::GetNginxStatus())
            {
                if(!$arParams['Right']->isSupportTeam())
                    CPullWatch::Add($GLOBALS["USER"]->GetId(), $arParams["PULL_TAG"]);
                else
                {
                    if($arParams['ROLE']=='W')
                        CPullWatch::Add($GLOBALS["USER"]->GetId(), $arParams["PULL_TAG_SUPPORT_ADMIN"]);
                    else
                        CPullWatch::Add($GLOBALS["USER"]->GetId(), $arParams["PULL_TAG_SUPPORT"]);
                }
                $userShowParams = array_merge(Support\UserTable::getRow(array('filter'=>array('ID'=>$GLOBALS["USER"]->GetId()),'select'=>array('ID','LOGIN','SHORT_NAME'))),array('TICKET_ID'=>$arParams["ID"]));                     
            	CPullWatch::AddToStack($arParams["PULL_TAG_SUPPORT"],
            		Array(
            			'module_id' => 'altasib.support',
            			'command' => 'showview',
            			'params' => $userShowParams
            		)
            	);
                
            	CPullWatch::AddToStack($arParams["PULL_TAG_SUPPORT_ADMIN"],
            		Array(
            			'module_id' => 'altasib.support',
            			'command' => 'showview',
            			'params' => $userShowParams
            		)
            	);                
            ?>
            <script>
            	BX.ready(function(){BX.PULL.extendWatch('<?=CUtil::JSEscape($arParams["PULL_TAG"])?>');});
            </script>
            <?
            }            
            if(!IsModuleInstalled('intranet'))
            {
                $arParams['SUPPORT_TEAM'] = Support\Tools::getSupportTeam();
            }
            //files
            $arTicket["FILES"] = array();
            $dataFile = Support\FileTable::getList(array('filter'=>array('TICKET_ID'=>$arParams['ID'],'MESSAGE_ID'=>0)));
            while($arFile = $dataFile->fetch())
            {
                $arrFile = CFile::GetFileArray($arFile['FILE_ID']);
                $arrFile['URL'] = str_replace(array('#ID#','#FILE_HASH#'),array($arParams['ID'],$arFile['HASH']),$arParams['URL_GET_FILE']);
                $arrFile['FORMAT_FILE_SIZE'] = CFile::FormatSize(intval($arrFile['FILE_SIZE']), 0);
                if(CFile::IsImage($arrFile['SRC']))
                    $arTicket["FILES_IMAGE"][] = $arrFile;
                else
                    $arTicket["FILES"][] = $arrFile;
            }             

            $arResult["MESSAGES"] = Array();
            $CCTP = new CTextParser();
            $CCTP->maxStringLen = 50;
            $CCTP->allow = Support\Tools::getAllowTags();
                 
            $page = $request['page']>0 ? $request['page'] : 1;
            $selectMessage = array('*',
                    'CREATED_USER_NAME'=>'CREATED_USER.NAME',
                    'CREATED_USER_LAST_NAME'=>'CREATED_USER.LAST_NAME',
                    'CREATED_USER_SHORT_NAME'=>'CREATED_USER.SHORT_NAME',
            );
            $paramsTM = array(
                'order'=>array('DATE_CREATE'=>'DESC','IS_LOG'=>'DESC'),
                'filter'=>array('TICKET_ID'=>$arParams['ID']),
                'select'=>$selectMessage,
                //'limit' => $arParams['MESSAGE_LIMIT'],
                //'offset' => ($page-1) * $arParams['MESSAGE_LIMIT'],
            );
            $showAll = false;
            if(isset($request['message']) && (int)$request['message']>0)
            {
                $showAll = true;
                $arParams['HIGHLIGHT_MESSAGE_ID'] = (int)$request['message'];
            }
            
            if(isset($request['LOAD_FULL']) && $request['LOAD_FULL']=='Y')
                $showAll = true;
                
            if(!$showAll && !$arParams['PART_LOAD'])
            {
                $lastId = \ALTASIB\Support\MessageReadTable::getLastUnreadId($arParams['ID']);
                $paramsTM['filter']['>=ID'] = $lastId;
                $showAll = true;
            }
                
            if(!$showAll)
            {
                $paramsTM['limit'] = $arParams['MESSAGE_LIMIT'];
                $paramsTM['offset'] = ($page-1) * $arParams['MESSAGE_LIMIT'];
            }
            
            if(!$arParams['IS_SUPPORT_TEAM'])
            {
                $paramsTM['filter']['IS_HIDDEN'] = 'N'; 
            }
            $result = Support\TicketMessageTable::getList($paramsTM);
            $result = new CDBResult($result);
            if(!$showAll) 
                $result->NavStart($arParams['MESSAGE_LIMIT']);

            while($arMessage = $result->Fetch())
            {
                $arResult["MESSAGES"][] = $arMessage;                 
            }
            $arResult["MESSAGES"] = array_reverse($arResult["MESSAGES"]);
            
            /**
             * favorite
             */
             
            $arResult['FAVORITE_MESSAGES'] = array();
            $obFav = \ALTASIB\Support\FavoriteTable::getList(array('filter'=>array('TICKET_ID'=>$arParams['ID'],'USER_ID'=>$USER->GetID())));
            while($favorite = $obFav->fetch())
            {
                $arResult['FAVORITE_MESSAGES'][] = array_merge(array('FAVORITE'=>'Y'),\ALTASIB\Support\TicketMessageTable::getRow(array('filter'=>array('ID'=>$favorite['MESSAGE_ID']),'select'=>$selectMessage)));
            }

            $addCnt = 0;
            if(!$arParams['PART_LOAD'])
            {
                $newLastId = $arResult["MESSAGES"][count($arResult["MESSAGES"])-$arParams['MESSAGE_LIMIT']]['ID'];
                if($newLastId>$lastId && ($arResult["MESSAGES"][count($arResult["MESSAGES"])-$arParams['MESSAGE_LIMIT']]['CREATED_USER_ID']!=$USER->GetId() || $lastId==0) )
                {
                    \ALTASIB\Support\MessageReadTable::setLastUnreadId(array(
                        'TICKET_ID' =>$arParams['ID'],
                        'LAST_MESSAGE_ID'=>$newLastId,
                        'USER_ID'=>$USER->GetId(),
                        'READ_DATE' => new Bitrix\Main\Type\DateTime(date('Y-m-d H:i:s'),'Y-m-d H:i:s')
                    ));
                }
                unset($paramsTM['filter']['>=ID']);
                if($newLastId>0)
                    $paramsTM['filter']['<ID'] = $newLastId;
                
                if($lastId==0)
                    $addCnt -= count($arResult["MESSAGES"]);
                else
                    $addCnt = count($arResult["MESSAGES"]);

                $showAll = false;
            }
            if(!$showAll)
            {
                $countQuery = new Bitrix\Main\Entity\Query(Support\TicketMessageTable::getEntity());
                $countQuery
                    ->registerRuntimeField("CNT", array(
                        "data_type" => "integer",
                        "expression" => array("COUNT(1)")
                        )
                    )
                    ->setSelect(array("CNT"))
                    ->setFilter($paramsTM['filter']);        
                $totalCnt = $countQuery->setLimit(null)->setOffset(null)->exec()->fetch();
                $totalCount = intval($totalCnt['CNT'])+$addCnt;
                if($totalCount <=0)
                    $totalCount = intval($totalCnt['CNT']);
                $arResult['MESSAGE_CNT'] = $totalCount;
                    
                $totalPage = ceil($totalCount/$arParams['MESSAGE_LIMIT']);
                $result->NavRecordCount = $totalCount;
                $result->NavPageCount = $totalPage;
                $result->NavPageNomer = $page;
                $arResult["NAV_PARAMS"] = $result->GetNavParams();
                $arResult["NAV_NUM"] = $result->NavNum;
                $result->bShowAll = false;
                $arResult["NAV_OBJECT"] = $result;
                $arResult['leftCnt'] = $totalCount-($page*$arParams['MESSAGE_LIMIT']);
            }
            else
                $arResult['MESSAGE_CNT'] = 1;
        }
        
        if($arParams['PART_LOAD'])
        {
            $mlist = '';
			$lastDate = "";
            foreach($arResult["MESSAGES"] as $arMessage)
            {
                $mlist.=getMessageSupport($arMessage,$arParams,$arTicket,$lastDate);
				$lastDate = $arMessage['DATE_CREATE'];
            }
            $loadResult = array(
                'status' => true,
                'html' => $mlist,
                'end' => ($totalPage==$page),
                'totalCount' => $totalCount,
                'leftCnt' => $totalCount-($page*$arParams['MESSAGE_LIMIT'])
            );
            if($showAll)
                $loadResult['end'] = $showAll;
            echo CUtil::PhpToJSObject($loadResult);
            die();
        }
        $arTicket['MESSAGE'] = $CCTP->convertText($arTicket["MESSAGE"]);
        $arTicket["PRIORITY_NAME"] = $arTicket['PRIORITY_ID'] >0 ? Support\Priority::getName($arTicket['PRIORITY_ID']) : '';
        $arResult["TICKET_INFO"] = $arTicket;

        $dataMember = Support\TicketMemberTable::getList(array('select'=>array('*','USER_SHORT_NAME'=>'USER.SHORT_NAME'),'filter'=>array('TICKET_ID'=>$arParams['ID'])));
        while($member = $dataMember->fetch())
            $arResult['MEMBERS'][$member['USER_ID']] = $member;
         
        if($arParams['SHOW_DEAL_SELECTOR'] && $USER->IsAdmin() && CModule::IncludeModule('crm'))
        {
            $arResult['CRM']['CONTACT'] = array();
            $arResult['CRM']['COMPANY'] = array();
            $arResult['CRM']['DEAL_LIST'] = array();
            $obContact = CCrmContact::GetList(array(),array('UF_SUPPORT_USER_ID'=>$arTicket['OWNER_USER_ID']));
            if($arResult['CRM']['CONTACT'] = $obContact->fetch())
            {
                $arResult['CRM']['CONTACT']['DETAIL_URL'] = CComponentEngine::MakePathFromTemplate(
                    COption::GetOptionString('crm','path_to_contact_show'),
                    array(
                    'contact_id' => $arResult['CRM']['CONTACT']['ID']
                    )
                );
                if($arResult['CRM']['CONTACT']['COMPANY_ID']>0)
                {
                    $obCompany = CCrmCompany::GetList(array(),array('ID'=>$arResult['CRM']['CONTACT']['COMPANY_ID']));
                    if($arResult['CRM']['COMPANY'] = $obCompany->Fetch())
                    {
                        $arResult['CRM']['COMPANY']['DETAIL_URL'] = CComponentEngine::MakePathFromTemplate(
                            COption::GetOptionString('crm','path_to_company_show'),
                            array(
                            'company_id' => $arResult['CRM']['COMPANY']['ID']
                            )
                        );
                        
                        $obDealList = CCrmDeal::GetList(array(),array('COMPANY_ID'=>$arResult['CRM']['COMPANY']['ID']));
                        while($deal = $obDealList->Fetch())
                        {
                            $arResult['CRM']['DEAL_LIST'][$deal['ID']] = $deal;
                        }
                    }
                }
            }
        } 
            
		$this->IncludeComponentTemplate();
        
        $APPLICATION->SetTitle(GetMessage('ALTASIB_SUPPORT_TICKET_TITLE',array('#TICKET_ID#'=>$arParams['ID'])).' - '.$ticket['TITLE']);
}
$APPLICATION->AddHeadScript('/bitrix/js/main/utils.js');
CJSCore::Init(array('access', 'window','jquery','viewer'));
?>