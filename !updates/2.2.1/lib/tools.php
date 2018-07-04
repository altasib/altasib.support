<?php
/**
 * ALTASIB
 * @site http://www.altasib.ru
 * @email dev@altasib.ru
 * @copyright 2006-2018 ALTASIB
 */

namespace ALTASIB\Support;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Tools
{
    public static $PULL_TYPE_CUSTOMER = 'C';
    public static $PULL_TYPE_SUPPORT_TEAM = 'E';
    public static $PULL_TYPE_SUPPORT_TEAM_ADMIN = 'W';

    public static function getUserRole()
    {
        global $APPLICATION;
        return $APPLICATION->GetUserRight("altasib.support");
    }

    function getModuleGroup()
    {
        global $APPLICATION;
        $result = array();
        $groupId = array();

        $db = $APPLICATION->GetGroupRightList(Array("MODULE_ID" => "altasib.support", "G_ACCESS" => 'C'));
        while ($arRole = $db->Fetch()) {
            $groupId[] = $arRole["GROUP_ID"];
        }

        $db = $APPLICATION->GetGroupRightList(Array("MODULE_ID" => "altasib.support", "G_ACCESS" => 'E'));
        while ($arRole = $db->Fetch()) {
            $groupId[] = $arRole["GROUP_ID"];
        }

        $db = $APPLICATION->GetGroupRightList(Array("MODULE_ID" => "altasib.support", "G_ACCESS" => 'W'));
        while ($arRole = $db->Fetch()) {
            $groupId[] = $arRole["GROUP_ID"];
        }

        $data = \CGroup::GetList(($by = "c_sort"), ($order = "desc"), array('ID' => implode(' | ', $groupId)));
        while ($res = $data->Fetch()) {
            $result[] = $res;
        }

        return $result;
    }

    public static function getSupportTeam()
    {
        global $APPLICATION;
        $arResult = Array(/*"0"=>"-"*/);
        $arFilter["ACTIVE"] = "Y";
        $arFilter["GROUPS_ID"] = Array(1);
        if ($arRole = $APPLICATION->GetGroupRightList(Array(
            "MODULE_ID" => "altasib.support",
            "G_ACCESS" => 'E'
        ))->Fetch()
        ) {
            $arFilter["GROUPS_ID"][] = $arRole["GROUP_ID"];
        }

        if ($arRole = $APPLICATION->GetGroupRightList(Array(
            "MODULE_ID" => "altasib.support",
            "G_ACCESS" => 'W'
        ))->Fetch()
        ) {
            $arFilter["GROUPS_ID"][] = $arRole["GROUP_ID"];
        }

        $obUser = \CUser::GetList(($by = "id"), ($order = "asc"), $arFilter);
        while ($arUser = $obUser->Fetch()) {
            $arResult[$arUser["ID"]] = /*"[".$arUser["LOGIN"]."] ".*/
                $arUser["NAME"] . " " . $arUser["LAST_NAME"];
        }
        return $arResult;
    }

    public static function getSupportAdminTeam()
    {
        global $APPLICATION;
        $arResult = Array(/*"0"=>"-"*/);
        $arFilter["ACTIVE"] = "Y";
        $arFilter["GROUPS_ID"] = Array(1);

        //if($arRole = $APPLICATION->GetGroupRightList(Array("MODULE_ID" => "altasib.support","G_ACCESS"=>'W'))->Fetch())
        //$arFilter["GROUPS_ID"][] = $arRole["GROUP_ID"];

        $obUser = \CUser::GetList(($by = "id"), ($order = "asc"), $arFilter);
        while ($arUser = $obUser->Fetch()) {
            $arResult[$arUser["ID"]] = $arUser;
        }
        return $arResult;
    }

    public static function getSupportGroup()
    {
        global $APPLICATION;
        $groups = Array(1);
        if ($arRole = $APPLICATION->GetGroupRightList(Array(
            "MODULE_ID" => "altasib.support",
            "G_ACCESS" => 'E'
        ))->Fetch()
        ) {
            $groups[] = $arRole["GROUP_ID"];
        }

        if ($arRole = $APPLICATION->GetGroupRightList(Array(
            "MODULE_ID" => "altasib.support",
            "G_ACCESS" => 'W'
        ))->Fetch()
        ) {
            $groups[] = $arRole["GROUP_ID"];
        }

        return $groups;
    }

    function getCustomerList()
    {
        global $APPLICATION;
        $arResult = Array();
        $arFilter["ACTIVE"] = "Y";

        if ($arRole = $APPLICATION->GetGroupRightList(Array(
            "MODULE_ID" => "altasib.support",
            "G_ACCESS" => 'C'
        ))->Fetch()
        ) {
            $arFilter["GROUPS_ID"][] = $arRole["GROUP_ID"];
        }

        $obUser = \CUser::GetList(($by = "id"), ($order = "asc"), $arFilter);
        while ($arUser = $obUser->Fetch()) {
            $arResult[$arUser["ID"]] = "[" . $arUser["LOGIN"] . "] " . $arUser["NAME"] . " " . $arUser["LAST_NAME"];
        }
        return $arResult;
    }

    public static function IsSupportTeam($USER_ID)
    {
        global $APPLICATION;
        static $USER_ROLES = array();
        $USER_ID = (int)$USER_ID;
        if ($USER_ID == 0) {
            return false;
        }
        if (isset($USER_ROLES[$USER_ID])) {
            $arRoles = $USER_ROLES[$USER_ID];
        } else {
            $arGroups = \CUser::GetUserGroup($USER_ID);
            $arRoles = $APPLICATION->GetUserRoles("altasib.support", $arGroups);
            $USER_ROLES[$USER_ID] = $arRoles;
        }
        if (in_array("W", $arRoles) || in_array("E", $arRoles)) {
            return true;
        } else {
            return false;
        }
    }

    function getAllowTags()
    {
        return array(
            "HTML" => "N",
            "ANCHOR" => \COption::GetOptionString("altasib.support", 'FORM_ALLOW_ANCHOR', 'Y'),
            "BIU" => \COption::GetOptionString("altasib.support", 'FORM_ALLOW_BIU', 'Y'),
            "IMG" => \COption::GetOptionString("altasib.support", 'FORM_ALLOW_IMG', 'Y'),
            "QUOTE" => \COption::GetOptionString("altasib.support", 'FORM_ALLOW_QUOTE', 'Y'),
            "CODE" => \COption::GetOptionString("altasib.support", 'FORM_ALLOW_CODE', 'N'),
            "FONT" => \COption::GetOptionString("altasib.support", 'FORM_ALLOW_FONT', 'N'),
            "LIST" => \COption::GetOptionString("altasib.support", 'FORM_ALLOW_LIST', 'N'),
            "TABLE" => \COption::GetOptionString("altasib.support", 'FORM_ALLOW_TABLE', 'N'),
            "VIDEO" => \COption::GetOptionString("altasib.support", 'FORM_ALLOW_VIDEO', 'N'),
            "NL2BR" => \COption::GetOptionString("altasib.support", 'FORM_ALLOW_NL2BR', 'Y'),
            "SMILES" => "N",
            "CUT_ANCHOR" => "N",
            "ALIGN" => "N"
        );
    }

    public static function ShowLHE($ID, $CONTENT, $INPUT)
    {
        Main\Loader::includeModule("fileman");
        $Editor = new \CHTMLEditor;
        $controlsMap = array();
        $allowTags = self::getAllowTags();
        $formId = '';
        foreach ($allowTags as $key => $val) {
            if ($key == 'BIU' && $val == 'Y') {
                $controlsMap[] = array('id' => 'Bold', 'compact' => true, 'sort' => 80);
                $controlsMap[] = array('id' => 'Bold', 'compact' => true, 'sort' => 80);
                $controlsMap[] = array('id' => 'Italic', 'compact' => true, 'sort' => 90);
                $controlsMap[] = array('id' => 'Underline', 'compact' => true, 'sort' => 100);
                $controlsMap[] = array('id' => 'Strikeout', 'compact' => true, 'sort' => 110);
                $controlsMap[] = array('id' => 'RemoveFormat', 'compact' => true, 'sort' => 120);
                $controlsMap[] = array('id' => 'AlignList', 'compact' => false, 'sort' => 125);
            }

            if ($key == 'FONT' && $val == 'Y') {
                $controlsMap[] = array('id' => 'Color', 'compact' => true, 'sort' => 130);
                $controlsMap[] = array('id' => 'FontSelector', 'compact' => false, 'sort' => 135);
                $controlsMap[] = array('id' => 'FontSize', 'compact' => false, 'sort' => 140);
                $controlsMap[] = array('separator' => true, 'compact' => false, 'sort' => 145);
            }

            if ($key == 'LIST' && $val == 'Y') {
                $controlsMap[] = array('id' => 'OrderedList', 'compact' => true, 'sort' => 150);
                $controlsMap[] = array('id' => 'UnorderedList', 'compact' => true, 'sort' => 160);
                $controlsMap[] = array('separator' => true, 'compact' => false, 'sort' => 200);
            }

            if ($key == 'ANCHOR' && $val == 'Y') {
                $controlsMap[] = array(
                    'id' => 'InsertLink',
                    'compact' => true,
                    'sort' => 210,
                    'wrap' => 'bx-b-link-' . $formId
                );
            }
            if ($key == 'IMG' && $val == 'Y') {
                $controlsMap[] = array('id' => 'InsertImage', 'compact' => false, 'sort' => 220);
            }
            if ($key == 'VIDEO' && $val == 'Y') {
                $controlsMap[] = array(
                    'id' => 'InsertVideo',
                    'compact' => true,
                    'sort' => 230,
                    'wrap' => 'bx-b-video-' . $formId
                );
            }
            if ($key == 'TABLE' && $val == 'Y') {
                $controlsMap[] = array('id' => 'InsertTable', 'compact' => false, 'sort' => 250);
            }
            if ($key == 'CODE' && $val == 'Y') {
                $controlsMap[] = array('id' => 'Code', 'compact' => true, 'sort' => 260);
            }
            if ($key == 'QUOTE' && $val == 'Y') {
                $controlsMap[] = array(
                    'id' => 'Quote',
                    'compact' => true,
                    'sort' => 270,
                    'wrap' => 'bx-b-quote-' . $formId
                );
            }

            $controlsMap[] = array('id' => 'Fullscreen', 'compact' => false, 'sort' => 310);

            $controlsMap[] = array('id' => 'BbCode', 'compact' => true, 'sort' => 340);
        }
        $controlsMap[] = array('id' => 'quickAnswer', 'compact' => false, 'sort' => 340);
        $Editor->Show(array(
            'name' => $INPUT,
            'inputName' => $INPUT,
            'id' => $ID,
            'siteId' => SITE_ID,
            'width' => '100%',
            'height' => 210,
            'content' => htmlspecialcharsback($CONTENT),
            'bAllowPhp' => false,
            'limitPhpAccess' => false,
            'showTaskbars' => false,
            'showNodeNavi' => false,
            'askBeforeUnloadPage' => false,
            //'arSmiles' => $arParams["SMILES"]["VALUE"],
            'bbCode' => true,
            'autoResize' => true,
            'autoResizeOffset' => 40,
            'saveOnBlur' => true,
            'iframeCss' => 'body{font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; font-size: 13px;}',
            'minBodyWidth' => 350,
            'normalBodyWidth' => 555,
            //'ctrlEnterHandler' => 'sendSupportMessage',
            'controlsMap' => $controlsMap,
        ));
    }

    function taskPlannerProcess($TICKET_ID, $remove = false)
    {
        if (Main\Loader::includeModule("tasks")) {
            $ticketData = TicketTable::getList(array(
                'filter' => array('ID' => $TICKET_ID),
                'select' => array('TASK_ID', 'RESPONSIBLE_USER_ID')
            ))->fetch();
            if ($ticketData['TASK_ID'] > 0) {
                self::taskPlanner($ticketData['TASK_ID'], $ticketData['RESPONSIBLE_USER_ID'], $remove);
            }
        }
    }

    function taskPlanner($TASK_ID, $USER_ID, $remove = false)
    {
        global $CACHE_MANAGER;
        if (Main\Loader::includeModule("tasks")) {
            $list = self::taskGetCurrentList($USER_ID);
            if (!is_array($list)) {
                $list = array();
            }
            if (!$remove) {
                $list[] = $TASK_ID;
                array_unique($list);
            } else {
                if (($key = array_search($TASK_ID, $list)) !== false) {
                    unset($list[$key]);
                }
            }
            \CUserOptions::SetOption('tasks', \CTaskPlannerMaintance::PLANNER_OPTION_CURRENT_TASKS, $list, false,
                $USER_ID);
            $CACHE_MANAGER->ClearByTag('tasks_user_' . $USER_ID);
        }
    }

    public static function taskGetCurrentList($USER_ID)
    {
        if (Main\Loader::includeModule("tasks")) {
            $list = \CUserOptions::GetOption('tasks', \CTaskPlannerMaintance::PLANNER_OPTION_CURRENT_TASKS, null,
                $USER_ID);
            if ($list === null) {
                if (Main\Loader::includeModule('timeman')) {
                    $TMUSER = \CTimeManUser::instance();
                    $arInfo = $TMUSER->GetCurrentInfo();
                    if (is_array($arInfo['TASKS'])) {
                        $list = $arInfo['TASKS'];
                    }
                } else {
                    $list = array();
                }

                if ($list !== null) {
                    \CTaskPlannerMaintance::setCurrentTasksList($list);
                }
            }

            if (!is_array($list)) {
                $list = array();
            }

            return $list;
        } else {
            return array();
        }
    }

    public static function sortImage($a, $b)
    {
        if ($a['CONTENT_TYPE'] == $b['CONTENT_TYPE']) {
            return 0;
        }
        return (\CFile::IsImage($a['SRC'])) ? -1 : 1;
    }

    public static function getPullSchema()
    {
        return Array(
            'MODULE_ID' => "altasib.support",
            'USE' => Array("PUBLIC_SECTION")
        );
    }

    /*socnet menu*/
    function __AddSocNetFeature(&$arSocNetFeaturesSettings)
    {
        $arSocNetFeaturesSettings["support"] = array(
            "allowed" => array(SONET_ENTITY_USER, SONET_ENTITY_GROUP),
            "operations" => array(
                "write" => array(
                    SONET_ENTITY_USER => SONET_RELATIONS_TYPE_NONE,
                    SONET_ENTITY_GROUP => SONET_ROLES_MODERATOR
                ),
                "view" => array(SONET_ENTITY_USER => SONET_RELATIONS_TYPE_ALL, SONET_ENTITY_GROUP => SONET_ROLES_USER),
            ),
            "minoperation" => "view",
            'title' => Loc::getMessage('ALTASIB_SUPPORT_MODULE_NAME')
        );
    }

    function __AddSocNetMenu(&$arResult)
    {
        $arResult["CanView"]["support"] = array_key_exists('support', $arResult['ActiveFeatures']);;
        $arResult["Urls"]["support"] = \CComponentEngine::makePathFromTemplate(SITE_DIR . "workgroups/group/#group_id#/support/",
            array("group_id" => $arResult["Group"]["ID"]));
        $arResult["Title"]["support"] = Loc::getMessage('ALTASIB_SUPPORT_MODULE_NAME');
    }

    function __OnParseSocNetComponentPath(&$arUrlTemplates, &$arCustomPagesPath)
    {
        $arUrlTemplates["support"] = "group/#group_id#/support/";
        $arCustomPagesPath["support"] = "/bitrix/components/altasib/support/";

        $arUrlTemplates["support_detail"] = "group/#group_id#/support/ticket/#TICKET_ID#/";
        $arCustomPagesPath["support_detail"] = "/bitrix/components/altasib/support/";
    }

    function __OnInitSocNetComponentVariables(&$arVariableAliases, &$arCustomPagesPath)
    {

    }

    function prepareComponentParams(&$arParams, &$arResult)
    {
        global $USER, $APPLICATION;
        $Role = $APPLICATION->GetUserRight("altasib.support");
        $arResult["IS_OWNER"] = false;
        if ($Role == "E" || $Role == "W") {
            $arParams["HAVE_CHANGE_STATUS"] = false;
            $arParams["HAVE_CHANGE_RESPONSIBLE"] = false;
            $arParams["HAVE_CHANGE_ASSISTANTS"] = false;
            $arParams["HAVE_CHANGE_CATEGORY"] = false;
            $arParams["HAVE_CHANGE_PRIORITY"] = false;
        }
        $arParams["HAVE_ANSWER"] = false;
        $arParams["HAVE_CREATE"] = false;

        if ($arParams["ID"] > 0) {
            $arFilter = Array(
                "ID" => $arParams["ID"]
            );

            $obTicket = TicketTable::getList(array(
                'filter' => array('ID' => $arParams["ID"]),
                'select' => array("ID", 'CATEGORY_ID', "OWNER_USER_ID", "RESPONSIBLE_USER_ID")
            ));
            if ($arTicket = $obTicket->fetch()) {
                $arResult['OWNER_USER_ID'] = $arTicket["OWNER_USER_ID"];
                $arResult['RESPONSIBLE_USER_ID'] = $arTicket["RESPONSIBLE_USER_ID"];
                $arResult['CATEGORY_ID'] = $arTicket["CATEGORY_ID"];
                unset($arTicket);
            } else {
                ShowError(GetMessage('ALTASIB_SUPPORT_CMP_TICKET_NOT_FOUND'));
                return false;
            }

            if ($Role == "W") {
                $arParams["HAVE_CHANGE_STATUS"] = true;
                $arParams["HAVE_CHANGE_RESPONSIBLE"] = true;
                $arParams["HAVE_CHANGE_ASSISTANTS"] = true;
                $arParams["HAVE_CHANGE_CATEGORY"] = true;
                $arParams["HAVE_CHANGE_PRIORITY"] = true;
                $arParams["HAVE_ANSWER"] = true;
            } elseif ($Role == "E") {
                if ($arResult['RESPONSIBLE_USER_ID'] != $USER->GetID()) {
                    $wtc = WtCTable::getList(
                        array(
                            'filter' => array(
                                'USER_ID' => $USER->GetID(),
                                'CATEGORY_ID' => $arResult['CATEGORY_ID'],
                                'CLIENT_USER_ID' => $arResult['OWNER_USER_ID']
                            )
                        )
                    );
                    if ($arClient = $wtc->fetch()) {
                        if ($arClient['R_VIEW'] != 'Y') {
                            ShowError(GetMessage('ALTASIB_SUPPORT_CMP_TICKET_NOT_FOUND'));
                            return false;
                        }
                        $arParams["HAVE_CHANGE_STATUS"] = $arClient['R_CHANGE_S'] == 'Y' ? true : false;
                        $arParams["HAVE_CHANGE_RESPONSIBLE"] = $arClient['R_CHANGE_R'] == 'Y' ? true : false;
                        $arParams["HAVE_CHANGE_ASSISTANTS"] = $arClient['R_CHANGE_A'] == 'Y' ? true : false;
                        $arParams["HAVE_CHANGE_CATEGORY"] = $arClient['R_CHANGE_C'] == 'Y' ? true : false;
                        $arParams["HAVE_CHANGE_PRIORITY"] = $arClient['R_CHANGE_P'] == 'Y' ? true : false;
                        $arParams["HAVE_ANSWER"] = $arClient['R_ANSWER'] == 'Y' ? true : false;
                    } else {
                        if (!TicketMemberTable::getList(array(
                            'select' => array('ID'),
                            'filter' => array('USER_ID' => $USER->GetID(), 'TICKET_ID' => $arParams['ID'])
                        ))->fetch()
                        ) {
                            ShowError(GetMessage('ALTASIB_SUPPORT_CMP_TICKET_NOT_FOUND'));
                            return false;
                        } else {
                            $arParams["HAVE_ANSWER"] = true;
                        }
                    }
                } else {
                    $arParams["HAVE_ANSWER"] = true;
                    $arParams["HAVE_CHANGE_STATUS"] = true;
                    $arParams["HAVE_CHANGE_RESPONSIBLE"] = true;
                    $arParams["HAVE_CHANGE_ASSISTANTS"] = true;
                }
            } else {

                //check is clien worker
                $c2cw = C2CWTable::getList(
                    array(
                        'filter' => array('WORKER_USER_ID' => $USER->GetID())
                    )
                );

                $arParams['isWorker'] = false;
                while ($ClientWorker = $c2cw->fetch()) {
                    $arParams['isWorker'] = true;
                    if ($ClientWorker['CATEGORY_ID'] != $arResult['CATEGORY_ID']) {
                        continue;
                    }

                    if ($ClientWorker['R_VIEW'] != 'Y') {
                        ShowError(GetMessage('ALTASIB_SUPPORT_CMP_TICKET_NOT_FOUND'));
                        return false;
                    } else {
                        if ($ClientWorker['R_ANSWER'] == 'Y') {
                            $arParams["HAVE_ANSWER"] = true;
                        }
                    }
                }
                if (!$arParams['isWorker']) {
                    if ($arResult['OWNER_USER_ID'] != $USER->GetID()) {
                        $c2cw = C2CWTable::getList(
                            array(
                                'filter' => array(
                                    'USER_ID' => $USER->GetID(),
                                    'WORKER_USER_ID' => $arResult['OWNER_USER_ID']
                                )
                            )
                        );
                        if (!$c2cw->fetch()) {
                            ShowError(GetMessage('ALTASIB_SUPPORT_CMP_TICKET_NOT_FOUND'));
                            return false;
                        } else {
                            $arParams["HAVE_ANSWER"] = true;
                        }
                    } else {
                        $arResult["IS_OWNER"] = true;
                        $arParams["HAVE_ANSWER"] = true;
                    }
                }
            }
        } else {
            $arParams['isWorker'] = false;
            $c2cw = C2CWTable::getList(
                array(
                    'filter' => array('WORKER_USER_ID' => $USER->GetID())
                )
            );
            while ($arClienWorker = $c2cw->fetch()) {
                $arParams['isWorker'] = true;
                if ($arClienWorker['R_CREATE'] == 'Y') {
                    $arParams['HAVE_CREATE_TO_CAREGORY'][] = $arClienWorker['CATEGORY_ID'];
                    $arParams["HAVE_CREATE"] = true;
                }
            }

            if (!$arParams['isWorker']) {
                $arParams["HAVE_CREATE"] = true;
            } elseif (empty($arParams['HAVE_CREATE_TO_CAREGORY'])) {
                ShowError(GetMessage('ALTASIB_SUPPORT_CMP_CREATE_ERROR'));
                return false;
            }

        }

        return true;
    }

    public static function getClientWorkers($CLIENT_ID)
    {
        $result = array();
        $c2cw = C2CWTable::getList(
            array(
                'filter' => array('USER_ID' => $CLIENT_ID),
                'data_doubling' => false,
            )
        );
        while ($clientWorker = $c2cw->fetch()) {
            $result[] = (int)$clientWorker['WORKER_USER_ID'];
        }
        $result = array_unique($result);
        return $result;
    }

    public static function getNotifySchema()
    {
        global $USER;
        $result = array(
            "altasib.support" => array(
                "NAME" => GetMessage('ALTASIB_SUPPORT_TOOLS_NOTIFY_MODULE'),
                "NOTIFY" => Array(
                    "create" => Array(
                        "NAME" => Loc::getMessage('ALTASIB_SUPPORT_TOOLS_NOTIFY_CREATE_TICKET'),
                    ),
                    "edit" => Array(
                        "NAME" => Loc::getMessage('ALTASIB_SUPPORT_TOOLS_NOTIFY_EDIT'),
                    ),
                    "message" => Array(
                        "NAME" => Loc::getMessage('ALTASIB_SUPPORT_TOOLS_NOTIFY_ADD_MESSAGE'),
                    ),
                    "resp" => Array(
                        "NAME" => Loc::getMessage('ALTASIB_SUPPORT_TOOLS_NOTIFY_RESP'),
                    ),
                ),
            ),
        );
        if (!is_object($USER)) {
            $USER = new \CUser;
        }
        if (!self::IsSupportTeam($USER->GetID())) {
            unset($result['altasib.support']['NOTIFY']['notify']);
        }

        return $result;
    }

    public static function OnAnswerNotifyCallBack($module, $tag, $text, $arNotify)
    {
        global $USER;
        if ($module == "altasib.support") {
            $dataTag = explode("|", $tag);

            if ($dataTag['2'] == 'TICKET') {
                $ticketId = (int)$dataTag['3'];
                if ($ticketId > 0) {
                    $message = array(
                        "TICKET_ID" => $ticketId,
                        "MESSAGE" => $text,
                        'IS_HIDDEN' => 'N',
                    );
                    $result = TicketMessageTable::add($message);
                    $ticketMessageId = $result->getId();
                    if ($result->isSuccess()) {
                        if (Main\Loader::includeModule("pull") && \CPullOptions::GetNginxStatus()) {
                            $arParams['ID'] = $ticketId;
                            $arParams['Right'] = new Rights($USER->GetID(), $arParams['ID']);
                            $arParams["ROLE"] = $Role = $arParams['Right']->getRole();
                            if ($arParams["ROLE"] == 'D') {
                                return '';
                            }

                            $arParams['IS_SUPPORT_TEAM'] = $arParams['Right']->isSupportTeam();
                            $pullParams = array(
                                'ID' => $ticketMessageId,
                                'TICKET_ID' => $ticketId,
                            );

                            if (!function_exists('getMessageSupport')) {
                                include_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/altasib/support.ticket.detail/templates/.default/message_template.php');
                            }

                            $dataTicket = TicketTable::getRow(array(
                                'filter' => array('ID' => $ticketId),
                                'select' => array('*')
                            ));

                            $dataTicketMessage = TicketMessageTable::getRow(array(
                                'filter' => array('ID' => $ticketMessageId),
                                'select' => array(
                                    '*',
                                    'TYPE_TIME_NAME' => 'TYPE_TIME.NAME',
                                    'CREATED_USER_NAME' => 'CREATED_USER.NAME',
                                    'CREATED_USER_LAST_NAME' => 'CREATED_USER.LAST_NAME',
                                    'CREATED_USER_SHORT_NAME' => 'CREATED_USER.SHORT_NAME'
                                )
                            ));

                            $dataTicketMessage['pull_type'] = self::$PULL_TYPE_CUSTOMER;

                            $pullParams['MESSAGE'] = \getMessageSupport($dataTicketMessage, $arParams, $dataTicket);
                            \CPullWatch::AddToStack('ALTASIB_SUPPORT_' . $ticketId,
                                Array(
                                    'module_id' => 'altasib.support',
                                    'command' => 'message',
                                    'params' => $pullParams
                                )
                            );

                            $dataTicketMessage['pull_type'] = self::$PULL_TYPE_SUPPORT_TEAM;
                            $pullParams['MESSAGE'] = \getMessageSupport($dataTicketMessage, $arParams, $dataTicket);
                            \CPullWatch::AddToStack('ALTASIB_SUPPORT_' . $ticketId . '_SUPPORT',
                                Array(
                                    'module_id' => 'altasib.support',
                                    'command' => 'message',
                                    'params' => $pullParams
                                )
                            );

                            $dataTicketMessage['pull_type'] = self::$PULL_TYPE_SUPPORT_TEAM_ADMIN;
                            $pullParams['MESSAGE'] = \getMessageSupport($dataTicketMessage, $arParams, $dataTicket);
                            \CPullWatch::AddToStack('ALTASIB_SUPPORT_' . $ticketId . '_SUPPORT_ADMIN',
                                Array(
                                    'module_id' => 'altasib.support',
                                    'command' => 'message',
                                    'params' => $pullParams
                                )
                            );
                        }
                        return Loc::getMessage('ALTASIB_SUPPORT_TOOLS_NOTIFY_ANSWER');
                    }
                }
            }
        }
    }
}

?>