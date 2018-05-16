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

/**
 * Event
 *
 * @package altasib
 * @subpackage support
 * @author MrDeff
 * @copyright 2013 ALTASIB
 */
Loc::loadMessages(__FILE__);

class Event
{
    public static $moduleId = 'altasib.support';

    /**
     * Event::sendToResponsible()
     *
     * @param mixed $TICKET_ID
     * @return
     */
    public static function sendToResponsible($TICKET_ID)
    {
        if ($arEvent = self::getEventDataEx($TICKET_ID)) {
            if (Main\Loader::includeModule("im") && \CIMSettings::GetNotifyAccess($arEvent['TICKET_RESPONSIBLE_USER_ID'],
                    self::$moduleId, 'create', \CIMSettings::CLIENT_SITE)
            ) {
                \CIMNotify::add(array(
                    'FROM_USER_ID' => $arEvent['TICKET_OWNER_USER_ID'],
                    'TO_USER_ID' => $arEvent['TICKET_RESPONSIBLE_USER_ID'],
                    "NOTIFY_TYPE" => IM_NOTIFY_FROM,
                    "NOTIFY_MODULE" => "altasib.support",
                    "NOTIFY_EVENT" => 'support_new',
                    "NOTIFY_ANSWER" => "Y",
                    "NOTIFY_TAG" => "ALTASIB|SUPPORT|TICKET|" . $TICKET_ID,
                    'NOTIFY_MESSAGE' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_NEW_TICKET', array(
                        '#ID#' => $TICKET_ID,
                        '#TITLE#' => $arEvent['TICKET_TITLE'],
                        '#MESSAGE#' => $arEvent['TICKET_MESSAGE'],
                        '#URL#' => $arEvent['URL']
                    )),
                    'NOTIFY_MESSAGE_OUT' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_NEW_TICKET', array(
                        '#ID#' => $TICKET_ID,
                        '#TITLE#' => $arEvent['TICKET_TITLE'],
                        '#MESSAGE#' => $arEvent['TICKET_MESSAGE'],
                        '#URL#' => $arEvent['URL']
                    )),
                ));
            }

            $arEvent['TICKET_MESSAGE'] = self::convertText($arEvent['TICKET_MESSAGE'] . self::getAdditionalFieldTxt($TICKET_ID));

            $userToNotify = self::getAdminToNotify(false, $arEvent['TICKET_RESPONSIBLE_USER_ID']);
            $userToNotify[$arEvent['TICKET_RESPONSIBLE_USER_ID']] = array('EMAIL' => $arEvent['TICKET_RESPONSIBLE_USER_EMAIL']);
            foreach ($userToNotify as $uid => $user) {
                if (self::allowSend($uid, 'create', 'email')) {
                    $arEvent['EMAIL'] = $user['EMAIL'];
                    \CEvent::SendImmediate("ALTASIB_SUPPORT", $arEvent['SITE_ID'], $arEvent);
                }
            }
        }
    }

    /**
     * Event::sendNotifyMessageClient()
     *
     * @param mixed $fields
     * @return
     */
    public static function sendNotifyMessageClient($fields)
    {
        global $USER;
        if ($arEvent = self::getEventDataEx($fields['TICKET_ID'], $fields['MESSAGE_ID'])) {
            $arUser = \CUser::GetByID($fields['CREATED_USER_ID'])->fetch();
            //get list from send
            $listToNotify = self::getAdditionalUserListToNotify($fields['TICKET_ID']);
            if (!in_array($arEvent['TICKET_OWNER_USER_ID'], $listToNotify)) {
                $listToNotify[] = $arEvent['TICKET_OWNER_USER_ID'];
            }

            $listToNotify = array_unique($listToNotify);

            foreach ($listToNotify as $TO_USER_ID) {
                if ($TO_USER_ID == $USER->GetID()) {
                    continue;
                }

                if (Main\Loader::includeModule("im") && \CIMSettings::GetNotifyAccess($TO_USER_ID, self::$moduleId,
                        'message', \CIMSettings::CLIENT_SITE)
                ) {
                    //"NOTIFY_TITLE" => Loc::getMessage('ALTASIB_SUPPORT_EVENT_CHANGE_TICKET',array('#ID#'=>$fields['TICKET_ID'])),
                    \CIMNotify::add(array(
                        'FROM_USER_ID' => $fields['CREATED_USER_ID'],
                        'TO_USER_ID' => $TO_USER_ID,
                        "NOTIFY_TYPE" => IM_NOTIFY_FROM,
                        "NOTIFY_MODULE" => "altasib.support",
                        "NOTIFY_EVENT" => 'support_client_message',
                        'NOTIFY_MESSAGE' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_NEW_MESSAGE_CLIENT', array(
                            '#ID#' => $fields['TICKET_ID'],
                            '#TITLE#' => $arEvent['TICKET_TITLE'],
                            '#MESSAGE#' => $fields['MESSAGE'],
                            '#URL#' => $arEvent['URL']
                        )),
                        'NOTIFY_MESSAGE_OUT' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_NEW_MESSAGE_CLIENT', array(
                            '#ID#' => $fields['TICKET_ID'],
                            '#TITLE#' => $arEvent['TICKET_TITLE'],
                            '#MESSAGE#' => $fields['MESSAGE'],
                            '#URL#' => $arEvent['URL']
                        )),
                    ));
                }
                $arEvent['FILES'] = self::getFileTxtLine($fields['TICKET_ID'], $fields['MESSAGE_ID'],
                    $arEvent['SITE_ID']);

                $arEvent['MESSAGE'] = self::convertText($fields['MESSAGE']);

                if (self::allowSend($TO_USER_ID, 'message', 'email')) {
                    $arForUser = \CUser::GetByID($TO_USER_ID)->fetch();
                    $emailSend = $arForUser['EMAIL'];
                    $arEvent['EMAIL'] = $emailSend;
                    \CEvent::SendImmediate("ALTASIB_SUPPORT_MESSAGE", $arEvent['SITE_ID'], $arEvent);
                }

                if (Main\Loader::includeModule("altasib.supportreport")) {
                    \ALTASIB\SupportReport\SonetNotify::AddMessage(array(
                        'TITLE' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_CHANGE_TICKET',
                            array('#ID#' => $fields['TICKET_ID'])),
                        'URL' => $arEvent['URL'],
                        'MESSAGE' => $arEvent['MESSAGE'],
                        'ID' => $fields['MESSAGE_ID'],
                        'CREATE_USER_ID' => $fields['CREATED_USER_ID']
                    ), $TO_USER_ID);
                }
            }
        }
    }

    /**
     * Event::sendNotifyMessageSupport()
     *
     * @param mixed $fields
     * @return
     */
    public static function sendNotifyMessageSupport($fields)
    {
        global $USER;
        if ((int)$fields['ELAPSED_TIME'] > 0) {
            return;
        }

        if ($arEvent = self::getEventDataEx($fields['TICKET_ID'], $fields['MESSAGE_ID'])) {
            $arUser = \CUser::GetByID($fields['CREATED_USER_ID'])->fetch();
            //get list from send
            $listToNotify = $additionalListToNot = self::getAdditionalUserListToNotifySupport($fields['TICKET_ID']);
            $dataAdmin = self::getAdminToNotify(false, $fields['CREATED_USER_ID']);
            $adminList = array();
            foreach ($dataAdmin as $Admin) {
                $listToNotify[] = $Admin['ID'];
                $adminList[] = $Admin['ID'];
            }

            $listToNotify = array_unique($listToNotify);
            foreach ($listToNotify as $TO_USER_ID) {
                if ($TO_USER_ID == $USER->GetID()) {
                    continue;
                }

                $arEvent['FILES'] = self::getFileTxtLine($fields['TICKET_ID'], $fields['MESSAGE_ID'],
                    $arEvent['SITE_ID']);
                $arEvent['CLOSE_TXT'] = $arEvent['TICKET_IS_CLOSE'] == 'Y' ? Loc::getMessage('ALTASIB_SUPPORT_EVENT_CLOSE') : '';

                if (in_array($TO_USER_ID, $additionalListToNot) || ((in_array($TO_USER_ID,
                                $adminList) && $TO_USER_ID == $arEvent['TICKET_RESPONSIBLE_USER_ID']) || !in_array($TO_USER_ID,
                            $adminList))
                ) {
                    if (Main\Loader::includeModule("im") && \CIMSettings::GetNotifyAccess($TO_USER_ID, self::$moduleId,
                            'message', \CIMSettings::CLIENT_SITE)
                    ) {
                        \CIMNotify::add(array(
                            "TO_USER_ID" => $TO_USER_ID,
                            "FROM_USER_ID" => $fields['CREATED_USER_ID'],
                            "NOTIFY_TYPE" => IM_NOTIFY_FROM,
                            "NOTIFY_MODULE" => "altasib.support",
                            "NOTIFY_EVENT" => 'support_to_support_message',
                            "NOTIFY_ANSWER" => "Y",
                            "NOTIFY_TAG" => "ALTASIB|SUPPORT|TICKET|" . $fields['TICKET_ID'],
                            'NOTIFY_MESSAGE' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_NEW_MESSAGE_SUPPORT', array(
                                '#ID#' => $fields['TICKET_ID'],
                                '#TITLE#' => $arEvent['TICKET_TITLE'],
                                '#MESSAGE#' => $fields['MESSAGE'],
                                '#URL#' => $arEvent['URL'],
                                '#CLOSE_TXT#' => $arEvent['CLOSE_TXT']
                            )),
                            'NOTIFY_MESSAGE_OUT' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_NEW_MESSAGE_SUPPORT_OUT',
                                array(
                                    '#ID#' => $fields['TICKET_ID'],
                                    '#TITLE#' => $arEvent['TICKET_TITLE'],
                                    '#MESSAGE#' => $fields['MESSAGE'],
                                    '#URL#' => $arEvent['URL']
                                )),
                        ));
                    }
                }
                $arEvent['CLOSE_TXT'] = '<p>' . $arEvent['CLOSE_TXT'] . '</p>';

                $arEvent['MESSAGE'] = self::convertText($fields['MESSAGE']);

                if (self::allowSend($TO_USER_ID, 'message', 'email')) {
                    $arForUser = \CUser::GetByID($TO_USER_ID)->fetch();
                    $emailSend = $arForUser['EMAIL'];
                    $arEvent['EMAIL'] = $emailSend;

                    \CEvent::SendImmediate("ALTASIB_SUPPORT_MESSAGE_SUPPORT", $arEvent['SITE_ID'], $arEvent);
                }
            }
        }
    }

    /**
     * Event::changeCaregory()
     *
     * @param mixed $TICKET_ID
     * @return
     */
    function changeCaregory($TICKET_ID)
    {
        if ($arEvent = self::getEventDataEx($TICKET_ID)) {
            TicketMessageTable::add(Array(
                'CREATED_USER_ID' => $arEvent['TICKET_MODIFIED_USER_ID'],
                'TICKET_ID' => $TICKET_ID,
                'MESSAGE' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_CHANGE_CATEGORY_LOG',
                    array('#CATEGORY#' => $arEvent['TICKET_CATEGORY'])),
                'IS_LOG' => 'Y'
            ));

            //get list from send
            $listToNotify = self::getAdditionalUserListToNotify($TICKET_ID);
            if (!in_array($arEvent['TICKET_OWNER_USER_ID'], $listToNotify)) {
                $listToNotify[] = $arEvent['TICKET_OWNER_USER_ID'];
            }

            $listToNotify = array_unique($listToNotify);
            foreach ($listToNotify as $TO_USER_ID) {
                if (Main\Loader::includeModule("im") && \CIMSettings::GetNotifyAccess($TO_USER_ID, self::$moduleId,
                        'edit', \CIMSettings::CLIENT_SITE)
                ) {
                    //"NOTIFY_TITLE" => Loc::getMessage('ALTASIB_SUPPORT_EVENT_CHANGE_TICKET',array('#ID#'=>$TICKET_ID)),
                    \CIMNotify::add(array(
                        'FROM_USER_ID' => $arEvent['TICKET_MODIFIED_USER_ID'],
                        'TO_USER_ID' => $TO_USER_ID,
                        "NOTIFY_TYPE" => IM_NOTIFY_FROM,
                        "NOTIFY_MODULE" => "altasib.support",
                        "NOTIFY_EVENT" => 'support_changeCaregory',
                        'NOTIFY_MESSAGE' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_CHANGE_CATEGORY', array(
                            '#ID#' => $TICKET_ID,
                            '#CATEGORY#' => $arEvent['TICKET_CATEGORY'],
                            '#URL#' => $arEvent['URL']
                        )),
                        'NOTIFY_MESSAGE_OUT' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_CHANGE_CATEGORY', array(
                            '#ID#' => $TICKET_ID,
                            '#CATEGORY#' => $arEvent['TICKET_CATEGORY'],
                            '#URL#' => $arEvent['URL']
                        )),
                    ));
                }

                $arEvent['MESSAGE'] = self::convertText(Loc::getMessage('ALTASIB_SUPPORT_EVENT_CHANGE_CATEGORY_MAIL',
                    array(
                        '#ID#' => $TICKET_ID,
                        '#CATEGORY#' => $arEvent['TICKET_CATEGORY'],
                        '#URL#' => $arEvent['URL']
                    )));

                if (self::allowSend($TO_USER_ID, 'edit', 'email')) {
                    $arForUser = \CUser::GetByID($TO_USER_ID)->fetch();
                    $arEvent['EMAIL'] = $arForUser['EMAIL'];

                    \CEvent::SendImmediate("ALTASIB_SUPPORT_TICKET_CHANGE", $arEvent['SITE_ID'], $arEvent);
                }
            }
        }
    }

    /**
     * Event::changeStatus()
     *
     * @param mixed $TICKET_ID
     * @return
     */
    public static function changeStatus($TICKET_ID)
    {
        global $USER;
        if ($arEvent = self::getEventDataEx($TICKET_ID)) {
            TicketMessageTable::add(Array(
                'CREATED_USER_ID' => $arEvent['TICKET_MODIFIED_USER_ID'],
                'TICKET_ID' => $TICKET_ID,
                'MESSAGE' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_CHANGE_STATUS_LOG',
                    array('#STATUS#' => $arEvent['TICKET_STATUS'])),
                'IS_LOG' => 'Y'
            ));

            //get list from send
            $listToNotify = self::getAdditionalUserListToNotify($TICKET_ID);
            if (!in_array($arEvent['TICKET_OWNER_USER_ID'], $listToNotify)) {
                $listToNotify[] = $arEvent['TICKET_OWNER_USER_ID'];
            }

            $listToNotify = array_unique($listToNotify);
            foreach ($listToNotify as $TO_USER_ID) {

                if ($TO_USER_ID == $USER->GetID()) {
                    continue;
                }

                if (Main\Loader::includeModule("im") && \CIMSettings::GetNotifyAccess($TO_USER_ID, self::$moduleId,
                        'edit', \CIMSettings::CLIENT_SITE)
                ) {
                    //"NOTIFY_TITLE" => Loc::getMessage('ALTASIB_SUPPORT_EVENT_CHANGE_TICKET',array('#ID#'=>$TICKET_ID)),
                    \CIMNotify::add(array(
                        'FROM_USER_ID' => $arEvent['TICKET_MODIFIED_USER_ID'],
                        'TO_USER_ID' => $TO_USER_ID,
                        "NOTIFY_TYPE" => IM_NOTIFY_FROM,
                        "NOTIFY_MODULE" => "altasib.support",
                        "NOTIFY_EVENT" => 'support_changeStatus',
                        'NOTIFY_MESSAGE' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_CHANGE_STATUS', array(
                            '#ID#' => $TICKET_ID,
                            '#STATUS#' => $arEvent['TICKET_STATUS'],
                            '#URL#' => $arEvent['URL']
                        )),
                        'NOTIFY_MESSAGE_OUT' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_CHANGE_STATUS', array(
                            '#ID#' => $TICKET_ID,
                            '#STATUS#' => $arEvent['TICKET_STATUS'],
                            '#URL#' => $arEvent['URL']
                        )),
                    ));
                }
                $arEvent['MESSAGE'] = self::convertText(Loc::getMessage('ALTASIB_SUPPORT_EVENT_CHANGE_STATUS_MAIL',
                    array('#ID#' => $TICKET_ID, '#STATUS#' => $arEvent['TICKET_STATUS'], '#URL#' => $arEvent['URL'])));

                if (self::allowSend($TO_USER_ID, 'edit', 'email')) {
                    $arForUser = \CUser::GetByID($TO_USER_ID)->fetch();
                    $arEvent['EMAIL'] = $arForUser['EMAIL'];

                    \CEvent::SendImmediate("ALTASIB_SUPPORT_TICKET_CHANGE", $arEvent['SITE_ID'], $arEvent);
                }
            }
        }
    }

    /**
     * Event::changePriority()
     *
     * @param mixed $TICKET_ID
     * @return
     */
    function changePriority($TICKET_ID, $last)
    {
        if ($arEvent = self::getEventDataEx($TICKET_ID)) {
            TicketMessageTable::add(Array(
                'CREATED_USER_ID' => $arEvent['TICKET_MODIFIED_USER_ID'],
                'TICKET_ID' => $TICKET_ID,
                'MESSAGE' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_CHANGE_PRIORITY_LOG',
                    array('#PRIORITY#' => $arEvent['TICKET_PRIORITY'])),
                'IS_LOG' => 'Y'
            ));

            //get list from send
            $listToNotify = self::getListNotify($TICKET_ID);//self::getAdditionalUserListToNotify($TICKET_ID);
            if (!in_array($arEvent['TICKET_OWNER_USER_ID'], $listToNotify)) {
                $listToNotify[] = $arEvent['TICKET_OWNER_USER_ID'];
            }

            $up = false;
            if ($last > 0 && $last < $arEvent['TICKET_PRIORITY_ID']) {
                $listToNotify = array_merge($listToNotify, self::getAdminToNotify(false, 0, true));
                $up = true;
            }

            $listToNotify = array_unique($listToNotify);
            foreach ($listToNotify as $TO_USER_ID) {
                if ($TO_USER_ID == $arEvent['TICKET_MODIFIED_USER_ID']) {
                    continue;
                }

                if (Main\Loader::includeModule("im") && \CIMSettings::GetNotifyAccess($TO_USER_ID, self::$moduleId,
                        'edit', \CIMSettings::CLIENT_SITE)
                ) {
                    //"NOTIFY_TITLE" => Loc::getMessage('ALTASIB_SUPPORT_EVENT_CHANGE_TICKET',array('#ID#'=>$TICKET_ID)),
                    $messId = $up ? 'ALTASIB_SUPPORT_EVENT_CHANGE_PRIORITY_UP' : 'ALTASIB_SUPPORT_EVENT_CHANGE_PRIORITY';
                    $messIdmail = $up ? 'ALTASIB_SUPPORT_EVENT_CHANGE_PRIORITY_MAIL_UP' : 'ALTASIB_SUPPORT_EVENT_CHANGE_PRIORITY_MAIL';
                    \CIMNotify::add(array(
                        'FROM_USER_ID' => $arEvent['TICKET_MODIFIED_USER_ID'],
                        'TO_USER_ID' => $TO_USER_ID,
                        "NOTIFY_TYPE" => IM_NOTIFY_FROM,
                        "NOTIFY_MODULE" => "altasib.support",
                        "NOTIFY_EVENT" => 'support_changePriority',
                        'NOTIFY_MESSAGE' => Loc::getMessage($messId, array(
                            '#ID#' => $TICKET_ID,
                            '#PRIORITY#' => $arEvent['TICKET_PRIORITY'],
                            '#URL#' => $arEvent['URL']
                        )),
                        'NOTIFY_MESSAGE_OUT' => Loc::getMessage($messId, array(
                            '#ID#' => $TICKET_ID,
                            '#PRIORITY#' => $arEvent['TICKET_PRIORITY'],
                            '#URL#' => $arEvent['URL']
                        )),
                    ));
                }

                $arEvent['MESSAGE'] = self::convertText(Loc::getMessage($messIdmail, array(
                    '#ID#' => $TICKET_ID,
                    '#PRIORITY#' => $arEvent['TICKET_PRIORITY'],
                    '#URL#' => $arEvent['URL']
                )));

                if (self::allowSend($TO_USER_ID, 'edit', 'email')) {
                    $arForUser = \CUser::GetByID($TO_USER_ID)->fetch();
                    $arEvent['EMAIL'] = $arForUser['EMAIL'];

                    \CEvent::SendImmediate("ALTASIB_SUPPORT_TICKET_CHANGE", $arEvent['SITE_ID'], $arEvent);
                }
            }
        }
    }

    /**
     * Event::changeResponsible()
     *
     * @param mixed $TICKET_ID
     * @return
     */
    function changeResponsible($TICKET_ID)
    {
        if ($arEvent = self::getEventDataEx($TICKET_ID)) {
            TicketMessageTable::add(Array(
                'CREATED_USER_ID' => $arEvent['TICKET_MODIFIED_USER_ID'],
                'TICKET_ID' => $TICKET_ID,
                'MESSAGE' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_CHANGE_RESPONSIBLE_LOG',
                    array('#RESPONSIBLE_TEXT#' => $arEvent['TICKET_RESPONSIBLE_USER_LIST_NAME'])),
                'IS_LOG' => 'Y'
            ));

            $arEvent['MESSAGE'] = self::convertText(Loc::getMessage('ALTASIB_SUPPORT_EVENT_CHANGE_RESPONSIBLE_MAIL',
                array(
                    '#ID#' => $TICKET_ID,
                    '#RESPONSIBLE_TEXT#' => $arEvent['TICKET_RESPONSIBLE_USER_LIST_NAME'],
                    '#URL#' => $arEvent['URL']
                )));
//to support
            if (Main\Loader::includeModule("im") && \CIMSettings::GetNotifyAccess($arEvent['TICKET_RESPONSIBLE_USER_ID'],
                    self::$moduleId, 'resp', \CIMSettings::CLIENT_SITE)
            ) {
                //"NOTIFY_TITLE" => Loc::getMessage('ALTASIB_SUPPORT_EVENT_CHANGE_TICKET',array('#ID#'=>$TICKET_ID)),
                \CIMNotify::add(array(
                    'FROM_USER_ID' => $arEvent['TICKET_MODIFIED_USER_ID'],
                    'TO_USER_ID' => $arEvent['TICKET_RESPONSIBLE_USER_ID'],
                    "NOTIFY_TYPE" => IM_NOTIFY_FROM,
                    "NOTIFY_MODULE" => "altasib.support",
                    "NOTIFY_EVENT" => 'support_changeResponsible',
                    'NOTIFY_MESSAGE' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_CHANGE_RESPONSIBLE_SUPPORT', array(
                        '#ID#' => $TICKET_ID,
                        '#MESSAGE#' => $arEvent['TICKET_MESSAGE'],
                        '#TITLE#' => $arEvent['TICKET_TITLE'],
                        '#URL#' => $arEvent['URL']
                    )),
                    'NOTIFY_MESSAGE_OUT' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_CHANGE_RESPONSIBLE_SUPPORT', array(
                        '#ID#' => $TICKET_ID,
                        '#MESSAGE#' => $arEvent['TICKET_MESSAGE'],
                        '#TITLE#' => $arEvent['TICKET_TITLE'],
                        '#URL#' => $arEvent['URL']
                    )),
                ));
            }
            $arEvent['EMAIL'] = $arEvent['TICKET_RESPONSIBLE_USER_EMAIL'];
            \CEvent::SendImmediate("ALTASIB_SUPPORT_TICKET_CHANGE", $arEvent['SITE_ID'], $arEvent);
//to client
            //get list from send
            $listToNotify = self::getAdditionalUserListToNotify($TICKET_ID);
            if (!in_array($arEvent['TICKET_OWNER_USER_ID'], $listToNotify)) {
                $listToNotify[] = $arEvent['TICKET_OWNER_USER_ID'];
            }

            $listToNotify = array_unique($listToNotify);
            foreach ($listToNotify as $TO_USER_ID) {
                if (Main\Loader::includeModule("im")) {
                    //"NOTIFY_TITLE" => Loc::getMessage('ALTASIB_SUPPORT_EVENT_CHANGE_TICKET',array('#ID#'=>$TICKET_ID)),
                    \CIMNotify::add(array(
                        'FROM_USER_ID' => $arEvent['TICKET_MODIFIED_USER_ID'],
                        'TO_USER_ID' => $TO_USER_ID,
                        "NOTIFY_TYPE" => IM_NOTIFY_FROM,
                        "NOTIFY_MODULE" => "altasib.support",
                        "NOTIFY_EVENT" => 'support_changeResponsible',
                        'NOTIFY_MESSAGE' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_CHANGE_RESPONSIBLE', array(
                            '#ID#' => $TICKET_ID,
                            '#RESPONSIBLE_TEXT#' => $arEvent['TICKET_RESPONSIBLE_USER_LIST_NAME'],
                            '#URL#' => $arEvent['URL']
                        )),
                        'NOTIFY_MESSAGE_OUT' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_CHANGE_RESPONSIBLE', array(
                            '#ID#' => $TICKET_ID,
                            '#RESPONSIBLE_TEXT#' => $arEvent['TICKET_RESPONSIBLE_USER_LIST_NAME'],
                            '#URL#' => $arEvent['URL']
                        )),
                    ));

                }
                if (self::allowSend($TO_USER_ID, 'resp', 'email')) {
                    $arForUser = \CUser::GetByID($TO_USER_ID)->fetch();
                    $arEvent['EMAIL'] = $arForUser['EMAIL'];

                    \CEvent::SendImmediate("ALTASIB_SUPPORT_TICKET_CHANGE", $arEvent['SITE_ID'], $arEvent);
                }
            }
        }
    }

    /**
     * Event::getAdditionalUserListToNotify()
     *
     * @param mixed $TICKET_ID
     * @return
     */
    function getAdditionalUserListToNotify($TICKET_ID)
    {
        $result = array();
        $obData = TicketTable::getList(array(
            'filter' => array('ID' => $TICKET_ID),
            'select' => array('OWNER_USER_ID')
        ));
        if ($data = $obData->fetch()) {
            $c2cw = C2CWTable::GetList(
                array(
                    'filter' => array(
                        array('USER_ID' => $data['OWNER_USER_ID']),
                        array('WORKER_USER_ID' => $data['OWNER_USER_ID']),
                        'LOGIC' => 'OR',
                    ),
                )
            );

            while ($dataUser = $c2cw->fetch()) {
                $result[] = $dataUser['USER_ID'];
                $result[] = $dataUser['WORKER_USER_ID'];
            }
        }
        return array_unique($result);
    }

    /**
     * Event::getAdditionalUserListToNotifySupport()
     *
     * @param mixed $TICKET_ID
     * @return
     */
    function getAdditionalUserListToNotifySupport($TICKET_ID)
    {
        $result = array();
        $obData = TicketTable::getList(array(
            'filter' => array('ID' => $TICKET_ID),
            'select' => array('OWNER_USER_ID', 'RESPONSIBLE_USER_ID')
        ));
        if ($data = $obData->fetch()) {
            $result[] = $data['RESPONSIBLE_USER_ID'];

            $listClientNotify = self::getAdditionalUserListToNotify($TICKET_ID);
            $filter = array('LOGIC' => 'OR');
            foreach ($listClientNotify as $USER_ID) {
                $filter[] = array('CLIENT_USER_ID' => $USER_ID);
            }

            if (count($filter) > 1) {
                $c2cw = WtCTable::GetList(
                    array(
                        'filter' => $filter,
                    )
                );

                while ($dataUser = $c2cw->fetch()) {
                    $result[] = $dataUser['USER_ID'];
                }
            }
        }

        $dataMember = TicketMemberTable::getList(array(
            'select' => array('USER_ID'),
            'filter' => array('TICKET_ID' => $TICKET_ID)
        ))->fetchAll();
        foreach ($dataMember as $member) {
            $result[] = $member['USER_ID'];
        }

        return array_unique($result);
    }

    /**
     * Event::getEventDataEx()
     *
     * @param mixed $TICKET_ID
     * @param mixed $MESSAGE_ID
     * @return
     */
    function getEventDataEx($TICKET_ID, $MESSAGE_ID = 0)
    {
        $obData = TicketTable::getList(array(
                'filter' => array('ID' => $TICKET_ID),
                'select' => array(
                    '*',
                    'CATEGORY_NAME' => 'CATEGORY.NAME',
                    'STATUS_NAME' => 'STATUS.NAME',
                    'SLA_NAME' => 'SLA.NAME',

                    'OWNER_USER_NAME' => 'OWNER_USER.NAME',
                    'OWNER_USER_LOGIN' => 'OWNER_USER.LOGIN',
                    'OWNER_USER_EMAIL' => 'OWNER_USER.EMAIL',
                    'OWNER_USER_SHORT_NAME' => 'OWNER_USER.SHORT_NAME',
                    'OWNER_USER_LIST_NAME' => 'OWNER_USER.LIST_NAME',

                    'CREATED_USER_NAME' => 'CREATED_USER.NAME',
                    'CREATED_USER_LOGIN' => 'CREATED_USER.LOGIN',
                    'CREATED_USER_EMAIL' => 'CREATED_USER.EMAIL',
                    'CREATED_USER_SHORT_NAME' => 'CREATED_USER.SHORT_NAME',
                    'CREATED_USER_LIST_NAME' => 'CREATED_USER.LIST_NAME',

                    'MODIFIED_USER_NAME' => 'MODIFIED_USER.NAME',
                    'MODIFIED_USER_LOGIN' => 'MODIFIED_USER.LOGIN',
                    'MODIFIED_USER_EMAIL' => 'MODIFIED_USER.EMAIL',
                    'MODIFIED_USER_SHORT_NAME' => 'MODIFIED_USER.SHORT_NAME',
                    'MODIFIED_USER_LIST_NAME' => 'MODIFIED_USER.LIST_NAME',

                    'RESPONSIBLE_USER_NAME' => 'RESPONSIBLE_USER.NAME',
                    'RESPONSIBLE_USER_LOGIN' => 'RESPONSIBLE_USER.LOGIN',
                    'RESPONSIBLE_USER_EMAIL' => 'RESPONSIBLE_USER.EMAIL',
                    'RESPONSIBLE_USER_SHORT_NAME' => 'RESPONSIBLE_USER.SHORT_NAME',
                    'RESPONSIBLE_USER_LIST_NAME' => 'RESPONSIBLE_USER.LIST_NAME'
                )
            )
        );
        if ($data = $obData->fetch()) {
            $arEvent = array(
                'TICKET_ID' => $TICKET_ID,
                'TICKET_TITLE' => $data['TITLE'],
                'SITE_ID' => $data['SITE_ID'],
                'TICKET_MESSAGE' => $data['MESSAGE'],
                'TICKET_DATE_CREATE' => $data['DATE_CREATE']->toString(),
                'TICKET_CATEGORY' => $data['CATEGORY_NAME'],
                'TICKET_STATUS' => $data['STATUS_NAME'],
                'TICKET_PRIORITY_ID' => $data['PRIORITY_ID'],
                'TICKET_PRIORITY' => $data['PRIORITY_ID'] > 0 ? Priority::getName($data['PRIORITY_ID']) : '',
                'TICKET_SLA' => $data['SLA_NAME'],
                'TICKET_IS_CLOSE' => $data['IS_CLOSE'],

                'TICKET_OWNER_USER_ID' => $data['OWNER_USER_ID'],
                'TICKET_OWNER_USER_NAME' => $data['OWNER_USER_NAME'],
                'TICKET_OWNER_USER_LOGIN' => $data['OWNER_USER_LOGIN'],
                'TICKET_OWNER_USER_EMAIL' => $data['OWNER_USER_EMAIL'],
                'TICKET_OWNER_USER_SHORT_NAME' => $data['OWNER_USER_SHORT_NAME'],
                'TICKET_OWNER_USER_LIST_NAME' => $data['OWNER_USER_LIST_NAME'],

                'TICKET_CREATED_USER_ID' => $data['CREATED_USER_ID'],
                'TICKET_CREATED_USER_NAME' => $data['CREATED_USER_NAME'],
                'TICKET_CREATED_USER_LOGIN' => $data['CREATED_USER_LOGIN'],
                'TICKET_CREATED_USER_EMAIL' => $data['CREATED_USER_EMAIL'],
                'TICKET_CREATED_USER_SHORT_NAME' => $data['CREATED_USER_SHORT_NAME'],
                'TICKET_CREATED_USER_LIST_NAME' => $data['CREATED_USER_LIST_NAME'],

                'TICKET_MODIFIED_USER_ID' => $data['MODIFIED_USER_ID'],
                'TICKET_MODIFIED_USER_NAME' => $data['MODIFIED_USER_NAME'],
                'TICKET_MODIFIED_USER_LOGIN' => $data['MODIFIED_USER_LOGIN'],
                'TICKET_MODIFIED_USER_EMAIL' => $data['MODIFIED_USER_EMAIL'],
                'TICKET_MODIFIED_USER_SHORT_NAME' => $data['MODIFIED_USER_SHORT_NAME'],
                'TICKET_MODIFIED_USER_LIST_NAME' => $data['MODIFIED_USER_LIST_NAME'],

                'TICKET_RESPONSIBLE_USER_ID' => $data['RESPONSIBLE_USER_ID'],
                'TICKET_RESPONSIBLE_USER_NAME' => $data['RESPONSIBLE_USER_NAME'],
                'TICKET_RESPONSIBLE_USER_LOGIN' => $data['RESPONSIBLE_USER_LOGIN'],
                'TICKET_RESPONSIBLE_USER_EMAIL' => $data['RESPONSIBLE_USER_EMAIL'],
                'TICKET_RESPONSIBLE_USER_SHORT_NAME' => $data['RESPONSIBLE_USER_SHORT_NAME'],
                'TICKET_RESPONSIBLE_USER_LIST_NAME' => $data['RESPONSIBLE_USER_LIST_NAME'],

                'TICKET_FILES' => self::getFileTxtLine($TICKET_ID, 0, $data['SITE_ID']),

                'SUPPORT_EMAIL' => \COption::GetOptionString('altasib.support', 'SUPPORT_MAIL'),
                'URL' => self::getURL($data['ID'], $data['SITE_ID']),
            );

            if ($MESSAGE_ID > 0) {
                $obDataMessage = TicketMessageTable::getList(array(
                    'filter' => array(
                        'TICKET_ID' => $TICKET_ID,
                        'ID' => $MESSAGE_ID
                    ),
                    'select' => array(
                        'CREATED_USER_NAME' => 'CREATED_USER.NAME',
                        'CREATED_USER_LOGIN' => 'CREATED_USER.LOGIN',
                        'CREATED_USER_EMAIL' => 'CREATED_USER.EMAIL',
                        'CREATED_USER_SHORT_NAME' => 'CREATED_USER.SHORT_NAME',
                        'CREATED_USER_LIST_NAME' => 'CREATED_USER.LIST_NAME',
                        'ELAPSED_TIME',
                    )
                ));
                if ($message = $obDataMessage->fetch()) {
                    $arEvent = array_merge($arEvent, $message);
                }
            }
            return $arEvent;
        } else {
            return false;
        }
    }

    /**
     * Event::getURL()
     *
     * @param mixed $TICKET_ID
     * @param mixed $SITE_ID
     * @return
     */
    function getURL($TICKET_ID, $SITE_ID)
    {
        $url = str_replace('#ID#', $TICKET_ID,
            \COption::GetOptionString('altasib.support', 'path_detail', '', $SITE_ID));
        $http = \CMain::IsHTTPS() ? "https://" : "http://";
        return $http . $_SERVER['SERVER_NAME'] . $url;
    }

    /**
     * Event::getAdminToNotify()
     *
     * @param bool $emailOnly
     * @param integer $skipId
     * @return
     */
    function getAdminToNotify($emailOnly = false, $skipId = 0, $idOnly = false)
    {
        global $APPLICATION;
        $user = array();
        $email = array();
        $arFilter["ACTIVE"] = "Y";
        $arFilter["GROUPS_ID"] = Array(1);
        if ($arRole = $APPLICATION->GetGroupRightList(Array(
            "MODULE_ID" => "altasib.support",
            "G_ACCESS" => 'W'
        ))->Fetch()
        ) {
            $arFilter["GROUPS_ID"][] = $arRole["GROUP_ID"];
        }

        $obUser = \CUser::GetList(($by = "id"), ($order = "asc"), $arFilter);
        while ($arUser = $obUser->Fetch()) {
            if ($skipId > 0 && $skipId == $arUser['ID']) {
                continue;
            }

            $user[$arUser['ID']] = $arUser;
            $email[] = $arUser['EMAIL'];
        }
        if ($idOnly) {
            return array_keys($user);
        }

        if ($emailOnly) {
            return array_unique($email);
        } else {
            return $user;
        }
    }

    /**
     * Event::expiredSend()
     *
     * @return
     */
    public static function expiredSend()
    {
        $ts = \AddToTimeStamp(array('HH' => -1));
        $data = TicketTable::getList(array(
            'filter' => array(
                'IS_CLOSE' => 'N',
                'LAST_MESSAGE_BY_SUPPORT' => 'N',
                array(
                    'LOGIC' => 'OR',
                    '<LAST_MESSAGE_DATE' => new \Bitrix\Main\Type\DateTime(date('Y-m-d H:i:s', $ts), 'Y-m-d H:i:s'),
                    array(
                        'LAST_MESSAGE_DATE' => null,
                        '<DATE_CREATE' => new \Bitrix\Main\Type\DateTime(date('Y-m-d H:i:s', $ts), 'Y-m-d H:i:s'),
                    )
                ),
                'IS_NOTIFY_SEND' => 'N'
            ),
            'select' => array(
                'ID',
                'SITE_ID',
                'DATE_CREATE',
                'LAST_MESSAGE_USER_ID',
                'LAST_MESSAGE_DATE',
                'SLA_NOTICE_TIME' => 'SLA.NOTICE_TIME',
                'SLA_RESPONSE_TIME' => 'SLA.RESPONSE_TIME'
            ),
        ));
        while ($ticket = $data->fetch()) {
            //todo: to sql
            if (!$ticket['LAST_MESSAGE_DATE']) {
                $ticket['LAST_MESSAGE_DATE'] = $ticket['DATE_CREATE'];
            }
            $ts1 = \AddToTimeStamp(array('HH' => $ticket['SLA_NOTICE_TIME']),
                \MakeTimeStamp($ticket['LAST_MESSAGE_DATE']->toString()));
            $date = new \Bitrix\Main\Type\Date();
            $ts2 = $date->getTimestamp();
            if ($ts1 < $ts2) {
                //get list from send
                $listToNotify = self::getAdditionalUserListToNotifySupport($ticket['ID']);
                $dataAdmin = self::getAdminToNotify(false);
                foreach ($dataAdmin as $Admin) {
                    $listToNotify[] = $Admin['ID'];
                }

                $listToNotify = array_unique($listToNotify);
                $arEvent = self::getEventDataEx($ticket['ID']);
                $dataMessage = TicketMessageTable::getList(array(
                    'order' => array('ID' => 'DESC'),
                    'filter' => array('TICKET_ID' => $ticket['ID'], 'IS_HIDDEN' => 'N', 'IS_LOG' => 'N'),
                    'limit' => 1,
                    'select' => array('MESSAGE')
                ))->fetch();
                $arEvent['LAST_MESSAGE'] = $dataMessage['MESSAGE'];
                if (!$arEvent['LAST_MESSAGE']) {
                    $arEvent['LAST_MESSAGE'] = $arEvent['MESSAGE'];
                }


                $arEvent['EXPIRATION_DATE'] = date('d.m.Y H:i:s',
                    \AddToTimeStamp(array('HH' => $ticket['SLA_RESPONSE_TIME']),
                        \MakeTimeStamp($ticket['LAST_MESSAGE_DATE']->toString())));
                $remTs = \MakeTimeStamp($arEvent['EXPIRATION_DATE'], "DD.MM.YYYY HH:MI:SS") - $ts2;
                $arEvent['REMAINED_TIME'] = $remTs > 0 ? date('H', $remTs) : 0;

                foreach ($listToNotify as $TO_USER_ID) {
                    if (Main\Loader::includeModule("im")) {
                        //"NOTIFY_TITLE" => Loc::getMessage('ALTASIB_SUPPORT_EVENT_EXPIRE_TITLE_SUPPORT',array('#ID#'=>$ticket['ID'])),
                        \CIMNotify::add(array(
                            'FROM_USER_ID' => $arEvent['CREATED_USER_ID'],
                            'TO_USER_ID' => $TO_USER_ID,
                            "NOTIFY_TYPE" => IM_NOTIFY_FROM,
                            "NOTIFY_MODULE" => "altasib.support",
                            "NOTIFY_EVENT" => 'support_expiredSend',
                            'NOTIFY_MESSAGE' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_EXPIRE_SUPPORT', array(
                                '#ID#' => $ticket['ID'],
                                '#TITLE#' => $arEvent['TICKET_TITLE'],
                                '#MESSAGE#' => $arEvent['LAST_MESSAGE'],
                                '#URL#' => $arEvent['URL']
                            )),
                            'NOTIFY_MESSAGE_OUT' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_EXPIRE_SUPPORT', array(
                                '#ID#' => $ticket['ID'],
                                '#TITLE#' => $arEvent['TICKET_TITLE'],
                                '#MESSAGE#' => $arEvent['LAST_MESSAGE'],
                                '#URL#' => $arEvent['URL']
                            )),
                        ));
                    }

                    $arEvent['MESSAGE'] = self::convertText($arEvent['LAST_MESSAGE']);

                    $forUser = \Bitrix\Main\UserTable::getList(array(
                        'filter' => array('ID' => $TO_USER_ID),
                        'select' => array('EMAIL')
                    ))->fetch();
                    $arEvent['EMAIL'] = $forUser['EMAIL'];
                    \CEvent::SendImmediate("ALTASIB_SUPPORT_EXPIRE_NOTIFY", $ticket['SITE_ID'], $arEvent);
                }
                TicketTable::update($ticket['ID'], array('IS_NOTIFY_SEND' => 'Y'));
            }
        }
        return 'ALTASIB\Support\Event::expiredSend();';
    }

    public static function sendAddMember($TICKET_ID, $USER_ID)
    {
        $data = self::getEventDataEx($TICKET_ID);
        if (Main\Loader::includeModule("im")) {
            //"NOTIFY_TITLE" => Loc::getMessage('ALTASIB_SUPPORT_EVENT_ADD_MEMBER_TITLE',array('#ID#'=>$TICKET_ID)),
            \CIMNotify::add(array(
                'FROM_USER_ID' => $data['TICKET_RESPONSIBLE_USER_ID'],
                'TO_USER_ID' => $USER_ID,
                "NOTIFY_TYPE" => IM_NOTIFY_FROM,
                "NOTIFY_MODULE" => "altasib.support",
                "NOTIFY_EVENT" => 'support_add_member',
                'NOTIFY_MESSAGE' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_ADD_MEMBER', array(
                    '#ID#' => $TICKET_ID,
                    '#TITLE#' => $data['TICKET_TITLE'],
                    '#MESSAGE#' => $data['TICKET_MESSAGE'],
                    '#URL#' => $data['URL']
                )),
                'NOTIFY_MESSAGE_OUT' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_ADD_MEMBER', array(
                    '#ID#' => $TICKET_ID,
                    '#TITLE#' => $data['TICKET_TITLE'],
                    '#MESSAGE#' => $data['TICKET_MESSAGE'],
                    '#URL#' => $data['URL']
                )),
            ));

        }
    }

    /**
     * Event::getFileTxtLine()
     *
     * @param mixed $TICKET_ID
     * @param mixed $MESSAGE_ID
     * @param mixed $SITE_ID
     * @return
     */
    private static function getFileTxtLine($TICKET_ID, $MESSAGE_ID, $SITE_ID)
    {
        $fileTxt = '';
        $dataFile = FileTable::getList(array(
            'filter' => array(
                'TICKET_ID' => $TICKET_ID,
                'MESSAGE_ID' => $MESSAGE_ID
            )
        ));
        while ($file = $dataFile->fetch()) {
            $fileUrl = str_replace(array('#ID#', '#FILE_HASH#'), array($TICKET_ID, $file['HASH']),
                \COption::GetOptionString('altasib.support', 'path_file', '', $SITE_ID));
            $http = \CMain::IsHTTPS() ? "https://" : "http://";
            $fileUrl = $http . $_SERVER['SERVER_NAME'] . $fileUrl;

            if ($fileArr = \CFile::GetFileArray($file['FILE_ID'])) {
                $fileTxt .= '<a href="' . $fileUrl . '">' . $fileArr['ORIGINAL_NAME'] . '</a><br>';
            }
        }
        if (strlen($fileTxt) > 0) {
            $fileTxt = Loc::getMessage('ALTASIB_SUPPORT_EVENT_FILES') . $fileTxt;
        }
        return $fileTxt;
    }

    /**
     * Event::getAdditionalFieldTxt()
     *
     * @param mixed $TICKET_ID
     * @return
     */
    private static function getAdditionalFieldTxt($TICKET_ID)
    {
        $txt = '';
        $USER_FIELDS = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("ALTASIB_SUPPORT", $TICKET_ID, LANGUAGE_ID);
        foreach ($USER_FIELDS as $k => $v) {
            if (strlen($v['VALUE']) > 0) {
                $txt .= '<b>' . $v['EDIT_FORM_LABEL'] . '</b>: ' . $v['VALUE'] . '<br>';
            }
        }
        if (strlen($txt) > 0) {
            $txt = '<p>' . $txt . '</p>';
            $txt = Loc::getMessage('ALTASIB_SUPPORT_EVENT_UF') . $txt;
        }
        return $txt;
    }

    /**
     * Event::changeCaregory()
     *
     * @param mixed $TICKET_ID
     * @return
     */
    public static function setDeferred($TICKET_ID)
    {
        if ($arEvent = self::getEventDataEx($TICKET_ID)) {
            TicketMessageTable::add(Array(
                'CREATED_USER_ID' => $arEvent['TICKET_MODIFIED_USER_ID'],
                'TICKET_ID' => $TICKET_ID,
                'MESSAGE' => Loc::getMessage('ALTASIB_SUPPORT_EVENT_SET_DEFERRED_LOG', array()),
                'IS_LOG' => 'Y'
            ));
        }
    }

    private function convertText($text)
    {
        $CCTP = new \CTextParser();
        $CCTP->MaxStringLen = 200;
        $CCTP->type = 'rss';
        $CCTP->allow = Tools::getAllowTags();
        return $CCTP->convertText($text);
    }

    private function allowSend($USER_ID, $type, $clientId)
    {
        $result = true;
        if (Main\Loader::includeModule("im") && !\CIMSettings::GetNotifyAccess($USER_ID, self::$moduleId, $type,
                $clientId)
        ) {
            $result = false;
        }

        return $result;
    }

    private function getListNotify($TICKET_ID)
    {
        $adm = array();
        //$adm = self::getAdminToNotify(false,0,true);
        $e1 = self::getAdditionalUserListToNotify($TICKET_ID);
        $e2 = self::getAdditionalUserListToNotifySupport($TICKET_ID);
        $list = array_merge($adm, $e1, $e2);
        return array_unique($list);
    }
}