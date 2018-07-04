<?php
/**
 * ALTASIB
 * @site http://www.altasib.ru
 * @email dev@altasib.ru
 * @copyright 2006-2018 ALTASIB
 */

namespace ALTASIB\Support;

use Bitrix\Main\Entity;
use Bitrix\Main\Type;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class TicketTable extends Entity\DataManager
{
    const LAMP_RED = 'red';
    const LAMP_GREEN = 'green';
    const LAMP_GRAY = 'gray';
    const LAMP_BROWN = 'brown';

    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getUfId()
    {
        return 'ALTASIB_SUPPORT';
    }

    public static function getTableName()
    {
        return 'altasib_support_ticket';
    }

    public static function getMap()
    {
        global $DB;
        $map = array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_ENTITY_ID_FIELD')
            ),
            'SITE_ID' => array(
                'data_type' => 'string',
                'required' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_ENTITY_SITE_ID_FIELD')
            ),
            'DATE_CREATE' => array(
                'data_type' => 'datetime',
                'required' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_ENTITY_DATE_CREATE_FIELD')
            ),

            'DATE_CREATE_X' => array(
                'data_type' => 'datetime',
                'expression' => array(
                    $DB->DatetimeToDateFunction('%s'),
                    'DATE_CREATE'
                )
            ),

            'TIMESTAMP' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_ENTITY_TIMESTAMP_FIELD')
            ),

            'TIMESTAMP_X' => array(
                'data_type' => 'datetime',
                'expression' => array(
                    $DB->DatetimeToDateFunction('%s'),
                    'TIMESTAMP'
                )
            ),

            'LAST_MESSAGE_DATE' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_ENTITY_LAST_MESSAGE_DATE_FIELD')
            ),

            'LAST_MESSAGE_USER_ID' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_ENTITY_LAST_MESSAGE_USER_ID_FIELD')
            ),
            'LAST_MESSAGE_USER' => array(
                'data_type' => 'ALTASIB\Support\User',
                'reference' => array('=this.LAST_MESSAGE_USER_ID' => 'ref.ID')
            ),

            'LAST_MESSAGE_BY_SUPPORT' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y')
            ),

            'IS_CLOSE' => array(
                'data_type' => 'boolean',
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_ENTITY_IS_CLOSE_FIELD'),
                'values' => array('N', 'Y')
            ),

            'DATE_CLOSE' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_ENTITY_DATE_CLOSE_FIELD')
            ),

            'DATE_CLOSE_X' => array(
                'data_type' => 'datetime',
                'expression' => array(
                    $DB->DatetimeToDateFunction('%s'),
                    'DATE_CLOSE'
                )
            ),

            'OWNER_USER_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_ENTITY_OWNER_USER_ID_FIELD')
            ),
            'OWNER_USER' => array(
                'data_type' => 'ALTASIB\Support\User',
                'reference' => array('=this.OWNER_USER_ID' => 'ref.ID')
            ),

            'CREATED_USER_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_ENTITY_CREATED_USER_ID_FIELD')
            ),
            'CREATED_USER' => array(
                'data_type' => 'ALTASIB\Support\User',
                'reference' => array('=this.CREATED_USER_ID' => 'ref.ID')
            ),

            'MODIFIED_USER_ID' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_ENTITY_MODIFIED_USER_ID_FIELD')
            ),
            'MODIFIED_USER' => array(
                'data_type' => 'ALTASIB\Support\User',
                'reference' => array('=this.MODIFIED_USER_ID' => 'ref.ID')
            ),

            'RESPONSIBLE_USER_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_ENTITY_RESPONSIBLE_USER_ID_FIELD')
            ),
            'RESPONSIBLE_USER' => array(
                'data_type' => 'ALTASIB\Support\User',
                'reference' => array('=this.RESPONSIBLE_USER_ID' => 'ref.ID')
            ),

            'CATEGORY_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_ENTITY_CATEGORY_ID_FIELD')
            ),

            'CATEGORY' => array(
                'data_type' => 'ALTASIB\Support\CategoryTable',
                'reference' => array('=this.CATEGORY_ID' => 'ref.ID')
            ),

            'PRIORITY_ID' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_ENTITY_PRIORITY_ID_FIELD')
            ),

            'STATUS_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_ENTITY_STATUS_ID_FIELD')
            ),

            'STATUS' => array(
                'data_type' => 'ALTASIB\Support\StatusTable',
                'reference' => array('=this.STATUS_ID' => 'ref.ID')
            ),

            'SLA_ID' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_ENTITY_SLA_ID_FIELD')
            ),

            'SLA' => array(
                'data_type' => 'ALTASIB\Support\SlaTable',
                'reference' => array('=this.SLA_ID' => 'ref.ID')
            ),

            'TITLE' => array(
                'data_type' => 'string',
                'required' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_ENTITY_TITLE_FIELD')
            ),

            'IP' => array(
                'data_type' => 'string',
                'required' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_ENTITY_IP_FIELD')
            ),

            'MESSAGE' => array(
                'data_type' => 'string',
                'required' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_ENTITY_MESSAGE_FIELD')
            ),
            'MESSAGE_CNT' => array(
                'data_type' => 'integer',
                'expression' => array(
                    '(SELECT COUNT(altasib_support_ticket_message.ID) from altasib_support_ticket_message WHERE altasib_support_ticket_message.TICKET_ID=%s)',
                    'ID'
                ),
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_ENTITY_MESSAGE_CNT_FIELD')
            ),
            'MESSAGES' => array(
                'data_type' => 'ALTASIB\Support\TicketMessageTable',
                'reference' => array('=this.ID' => 'ref.TICKET_ID')
            ),

            'GROUP_ID' => array(
                'data_type' => 'integer',
            ),

            'GROUP' => array(
                'data_type' => '\Bitrix\Socialnetwork\WorkgroupTable',
                'reference' => array('=this.GROUP_ID' => 'ref.ID')
            ),
            'TASK_ID' => array(
                'data_type' => 'integer',
            ),

            'DEAL_ID' => array(
                'data_type' => 'integer',
            ),

            'IS_OVERDUE' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y')
            ),
            'IS_NOTIFY_SEND' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y')
            ),
            'IS_DEFERRED' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y')
            ),

            'SUM_ELAPSED_TIME' => array(
                'data_type' => 'integer',
                'expression' => array(
                    '(SELECT SUM(altasib_support_ticket_message.ELAPSED_TIME) from altasib_support_ticket_message WHERE altasib_support_ticket_message.TICKET_ID=%s)',
                    'ID'
                ),
            ),

            'COMMENT' => array(
                'data_type' => 'string',
            ),
        );
        return $map;
    }

    public static function isClose($ID)
    {
        $ob = self::getList(array('filter' => array('ID' => $ID), 'select' => array('IS_CLOSE')));
        if ($data = $ob->fetch()) {
            return ($data['IS_CLOSE'] == 'Y');
        }

        return false;
    }

    public static function isDeferred($ID)
    {
        $ob = self::getList(array('filter' => array('ID' => $ID), 'select' => array('IS_DEFERRED')));
        if ($data = $ob->fetch()) {
            return ($data['IS_DEFERRED'] == 'Y');
        }

        return false;
    }

    public static function add(array $data)
    {
        global $USER;
        $date = new Type\DateTime(date('Y-m-d H:i:s'), 'Y-m-d H:i:s');
        if (!isset($data['DATE_CREATE'])) {
            $data['DATE_CREATE'] = $date;
        }
        if (!isset($data['CREATED_USER_ID'])) {
            global $USER;
            $data['CREATED_USER_ID'] = $USER->GetID();
        }

        if (!isset($data['OWNER_USER_ID'])) {
            global $USER;
            $data['OWNER_USER_ID'] = $USER->GetID();
        }

        if (!isset($data['CATEGORY_ID'])) {
            $category = CategoryTable::getList(array(
                'filter' => array('USE_DEFAULT' => 'Y'),
                'select' => array('ID')
            ));
            while ($arCategory = $category->fetch()) {
                $data['CATEGORY_ID'] = $arCategory['ID'];
            }
        }

        //check personal resp..
        $dataClient = ClientTable::getList(array(
            'filter' => array(
                "USER_ID" => $data['OWNER_USER_ID'],
                '>RESPONSIBLE_USER_ID' => 0
            ),
            'select' => array('RESPONSIBLE_USER_ID')
        ));
        if ($arDataClient = $dataClient->fetch()) {
            $data['RESPONSIBLE_USER_ID'] = $arDataClient['RESPONSIBLE_USER_ID'];
        }

        if (!isset($data['RESPONSIBLE_USER_ID']) && $data['CATEGORY_ID'] > 0) {
            $category = CategoryTable::getList(array(
                'filter' => array('ID' => $data['CATEGORY_ID']),
                'select' => array('RESPONSIBLE_USER_ID')
            ));
            if ($arCategory = $category->fetch()) {
                $data['RESPONSIBLE_USER_ID'] = $arCategory['RESPONSIBLE_USER_ID'];
            }
        }

        if (!isset($data['SITE_ID'])) {
            $data['SITE_ID'] = SITE_ID;
        }

        if (!isset($data['IP'])) {
            $server = \Bitrix\Main\Context::getCurrent()->getServer();
            $data['IP'] = $server['REMOTE_ADDR'];
        }

        if (!isset($data['STATUS_ID'])) {
            $data['STATUS_ID'] = \Bitrix\Main\Config\Option::get('altasib.support', 'DEFAULT_STATUS', 1);
        }

        if (!isset($data['SLA_ID'])) {
            $userGroups = \CUser::GetUserGroup($data['OWNER_USER_ID']);
            $dataSlaGroup = SlaTable::getList(array(
                'filter' => array('SLA_GROUP.GROUP_ID' => $userGroups),
                'select' => array('GROUP_' => 'SLA_GROUP')
            ));
            if ($sla = $dataSlaGroup->fetch()) {
                $data['SLA_ID'] = $sla['GROUP_SLA_ID'];
            }
        }

        $arFiles = array();
        if (array_key_exists('FILES', $data)) {
            if (is_array($data['FILES']) && count($data['FILES']) > 0) {
                $arFiles = $data['FILES'];
            }

            unset($data['FILES']);
        }
        $result = parent::add($data);

        if ($result->isSuccess()) {
            if (count($arFiles) > 0) {
                foreach ($arFiles as $FILE_ID) {
                    FileTable::add(array(
                        'MESSAGE_ID' => 0,
                        'TICKET_ID' => $result->getId(),
                        'FILE_ID' => $FILE_ID
                    ));
                }
            }

            //send events
            if (!defined('SUP_IMPORT')) {
                Event::sendToResponsible($result->getId());
            }
            //from owners
        }

        return $result;
    }

    public static function close($ID, $MESSAGE = '', $changeStatus = true)
    {
        global $USER;
        if (self::isClose($ID)) {
            self::update($ID, array(
                'IS_CLOSE' => 'N',
                'MODIFIED_USER_ID' => $USER->GetID(),
                'TIMESTAMP' => new Type\DateTime(),
                'STATUS_ID' => \Bitrix\Main\Config\Option::get('altasib.support', 'RE_STATUS', 1)
            ));
            Event::changeStatus($ID);
        } else {
            if ($changeStatus) {
                self::update($ID, array(
                    'IS_CLOSE' => 'Y',
                    'MODIFIED_USER_ID' => $USER->GetID(),
                    'TIMESTAMP' => new Type\DateTime(),
                    'STATUS_ID' => \Bitrix\Main\Config\Option::get('altasib.support', 'FINAL_STATUS', 1)
                ));
                Event::changeStatus($ID);
            } else {
                self::update($ID, array('IS_CLOSE' => 'Y'));
            }

            if ($MESSAGE) {
                TicketMessageTable::add(array(
                    "TICKET_ID" => $ID,
                    "MESSAGE" => $MESSAGE,
                    'IS_HIDDEN' => 'N',
                ));
            }
        }
    }

    public static function getRight($ID)
    {
        global $USER;

        $result = array(
            'HAVE_CHANGE_STATUS' => false,
            'HAVE_CHANGE_RESPONSIBLE' => false,
            'HAVE_CHANGE_CATEGORY' => false,
            'HAVE_CHANGE_PRIORITY' => false,
            'HAVE_ANSWER' => false
        );
        $Role = Tools::getUserRole();
        if ($Role == 'D') {
            return $result;
        }

        if ($Role == "W") {
            $result["HAVE_CHANGE_STATUS"] = true;
            $result["HAVE_CHANGE_RESPONSIBLE"] = true;
            $result["HAVE_CHANGE_CATEGORY"] = true;
            $result["HAVE_CHANGE_PRIORITY"] = true;
            $result["HAVE_ANSWER"] = true;
            return $result;
        } else {

            $obTicket = self::getList(array(
                'filter' => array('ID' => $ID),
                'select' => array("ID", 'CATEGORY_ID', "OWNER_USER_ID", "RESPONSIBLE_USER_ID")
            ));
            if ($arTicket = $obTicket->fetch()) {
            } else {
                return false;
            }

            if ($Role == "E") {
                if ($arTicket["RESPONSIBLE_USER_ID"] != $USER->GetID()) {
                    $wtc = WtCTable::getList(
                        array(
                            'filter' => array('USER_ID' => $USER->GetID(), 'CATEGORY_ID' => $arTicket['CATEGORY_ID'])
                        )
                    );
                    if ($arClient = $wtc->fetch()) {
                        if ($arClient['R_VIEW'] != 'Y') {
                            return false;
                        }
                        $result["HAVE_CHANGE_STATUS"] = $arClient['R_CHANGE_S'] == 'Y' ? true : false;
                        $result["HAVE_CHANGE_RESPONSIBLE"] = $arClient['R_CHANGE_R'] == 'Y' ? true : false;
                        $result["HAVE_CHANGE_CATEGORY"] = $arClient['R_CHANGE_C'] == 'Y' ? true : false;
                        $result["HAVE_CHANGE_PRIORITY"] = $arClient['R_CHANGE_P'] == 'Y' ? true : false;
                        $result["HAVE_ANSWER"] = $arClient['R_ANSWER'] == 'Y' ? true : false;
                    } else {
                        return false;
                    }
                } else {
                    $result["HAVE_CHANGE_STATUS"] = true;
                    $result["HAVE_CHANGE_RESPONSIBLE"] = true;
                    $result["HAVE_CHANGE_CATEGORY"] = true;
                    $result["HAVE_CHANGE_PRIORITY"] = true;
                    $result["HAVE_ANSWER"] = true;
                    return $result;
                }
            } else {
                //check is client worker
                $c2cw = C2CWTable::getList(
                    array(
                        'filter' => array('WORKER_USER_ID' => $USER->GetID())
                    )
                );

                $isWorker = false;
                while ($ClientWorker = $c2cw->fetch()) {
                    $arParams['isWorker'] = true;
                    if ($ClientWorker['CATEGORY_ID'] != $arTicket["CATEGORY_ID"]) {
                        continue;
                    }

                    if ($ClientWorker['R_VIEW'] != 'Y') {
                        return false;
                    } else {
                        if ($ClientWorker['R_ANSWER'] == 'Y') {
                            $result["HAVE_ANSWER"] = true;
                            return $result;
                        }
                    }
                }
                if (!$isWorker) {
                    if ($arTicket['OWNER_USER_ID'] != $USER->GetID()) {
                        $c2cw = C2CWTable::getList(
                            array(
                                'filter' => array(
                                    'USER_ID' => $USER->GetID(),
                                    'WORKER_USER_ID' => $arTicket['OWNER_USER_ID']
                                )
                            )
                        );
                        if (!$c2cw->fetch()) {
                            return false;
                        } else {
                            $result["HAVE_ANSWER"] = true;
                            return $result;
                        }
                    } else {
                        $result["HAVE_ANSWER"] = true;
                        return $result;
                    }
                }
            }
        }
        return false;
    }

    public static function autoClose()
    {
        $dayLeft = \Bitrix\Main\Config\Option::get('altasib.support', 'AUTO_CLOSE', 7);
        if ($dayLeft > 0) {
            $ts = \AddToTimeStamp(array('DD' => -$dayLeft));
            $data = self::getList(array(
                'select' => array('ID', 'LAST_MESSAGE_USER_ID'),
                'filter' => array(
                    'IS_CLOSE' => 'N',
                    'IS_DEFERRED' => 'N',
                    '<LAST_MESSAGE_DATE' => new Type\DateTime(date('Y-m-d H:i:s', $ts), 'Y-m-d H:i:s'),
                    'CATEGORY.NOT_CLOSE' => 'N'
                )
            ));

            while ($ticket = $data->fetch()) {
                if (Tools::IsSupportTeam($ticket['LAST_MESSAGE_USER_ID'])) {
                    self::close($ticket['ID'], false, false);
                }
            }
        }

        return "ALTASIB\\Support\\TicketTable::autoClose();";
    }

    public static function checkOverdue()
    {
        $ts = \AddToTimeStamp(array('HH' => -1));
        $data = self::getList(array(
            'filter' => array(
                'IS_CLOSE' => 'N',
                'IS_DEFERRED' => 'N',
                'LAST_MESSAGE_BY_SUPPORT' => 'N',
                'STATUS.SKIP' => 'N',
                array(
                    'LOGIC' => 'OR',
                    '<LAST_MESSAGE_DATE' => new \Bitrix\Main\Type\DateTime(date('Y-m-d H:i:s', $ts), 'Y-m-d H:i:s'),
                    array(
                        'LAST_MESSAGE_DATE' => null,
                        '<DATE_CREATE' => new \Bitrix\Main\Type\DateTime(date('Y-m-d H:i:s', $ts), 'Y-m-d H:i:s'),
                    )
                ),
            ),
            'select' => array(
                'ID',
                'DATE_CREATE',
                'LAST_MESSAGE_USER_ID',
                'LAST_MESSAGE_DATE',
                'SLA_RESPONSE_TIME' => 'SLA.RESPONSE_TIME'
            ),
        ));
        while ($ticket = $data->fetch()) {
            if (!$ticket['LAST_MESSAGE_DATE']) {
                $ticket['LAST_MESSAGE_DATE'] = $ticket['DATE_CREATE'];
            }

            //todo: to sql
            $ts1 = \AddToTimeStamp(array('HH' => $ticket['SLA_RESPONSE_TIME']),
                \MakeTimeStamp($ticket['LAST_MESSAGE_DATE']->toString()));
            $date = new Type\Date();
            $ts2 = $date->getTimestamp();
            if ($ts1 < $ts2) {
                self::update($ticket['ID'], array('IS_OVERDUE' => 'Y'));
            }
        }
        return 'ALTASIB\Support\TicketTable::checkOverdue();';
    }


    /**
     * TicketTable::getTicketRight()
     *
     * @param mixed $ownerId or responsibleId
     * @param mixed $categoryId
     * @return void
     */
    public static function getTicketRight($ticketId, $ownerId, $responsibleId, $categoryId)
    {
        global $USER;
        $right = array();
        $Role = User::getRole();
        if ($Role == 'W') {
            $right = array(
                'VIEW' => true,
                'ANSWER' => true,
                'CHANGE_STATUS' => true,
                'CHANGE_RESPONSIBLE' => true,
                'CHANGE_ASSISTANTS' => true,
                'CHANGE_CATEGORY' => true,
                'CHANGE_PRIORITY' => true,
            );
        }

        if ($Role == 'C') {
            $right = array(
                'VIEW' => false,
                'CREATE' => false,
                'ANSWER' => false,
                'CHANGE_PRIORITY' => false
            );
            $isWorker = User::isWorker($USER->GetID());

            $c2cw = \ALTASIB\Support\C2CWTable::getList(
                array(
                    'filter' => array(
                        'WORKER_USER_ID' => $USER->GetID(),
                        'CATEGORY_ID' => $categoryId,
                        'R_VIEW' => 'Y'
                    )
                )
            );

            $isWorker = false;
            while ($cw = $c2cw->fetch()) {
                $isWorker = true;
                $right['VIEW'] = true;
                if ($cw['R_ANSWER'] == 'Y') {
                    $right["ANSWER"] = true;
                }
            }

            if (!$isWorker) {
                if ($ownerId != $USER->GetID()) {
                    $c2cw = \ALTASIB\Support\C2CWTable::getList(
                        array('filter' => array('USER_ID' => $USER->GetID(), 'WORKER_USER_ID' => $ownerId))
                    );
                    if ($c2cw->fetch()) {
                        $right['VIEW'] = true;
                        $right["ANSWER"] = true;
                    }
                } else {
                    $right['VIEW'] = true;
                    $right["ANSWER"] = true;
                }
            }
            if ($right['VIEW']) {
                $right['CHANGE_PRIORITY'] = true;
            }
        }

        if ($Role == 'E') {
            $right = array(
                'VIEW' => false,
                'CREATE' => false,
                'ANSWER' => false,
                'CHANGE_PRIORITY' => false,
                'CHANGE_STATUS' => false,
                'CHANGE_ASSISTANTS' => false
            );
            if ($responsibleId == $USER->GetID()) {
                $right['VIEW'] = true;
                $right['ANSWER'] = true;
                $right['CHANGE_STATUS'] = true;
                $right['CHANGE_ASSISTANTS'] = true;
            } else {
                $wtc = \ALTASIB\Support\WtCTable::getList(
                    array(
                        'filter' => array(
                            'USER_ID' => $USER->GetID(),
                            'CATEGORY_ID' => $categoryId,
                            'CLIENT_USER_ID' => $ownerId,
                            'R_VIEW' => 'Y'
                        )
                    )
                );
                if ($client = $wtc->fetch()) {
                    $right["CHANGE_STATUS"] = $client['R_CHANGE_S'] == 'Y' ? true : false;
                    $right["CHANGE_RESPONSIBLE"] = $client['R_CHANGE_R'] == 'Y' ? true : false;
                    $right["CHANGE_ASSISTANTS"] = $client['R_CHANGE_A'] == 'Y' ? true : false;
                    $right["CHANGE_CATEGORY"] = $client['R_CHANGE_C'] == 'Y' ? true : false;
                    $right["CHANGE_PRIORITY"] = $client['R_CHANGE_P'] == 'Y' ? true : false;
                    $right["ANSWER"] = $client['R_ANSWER'] == 'Y' ? true : false;
                } else {
                    if (\ALTASIB\Support\TicketMemberTable::getList(array(
                        'select' => array('ID'),
                        'filter' => array('USER_ID' => $USER->GetID(), 'TICKET_ID' => $ticketId)
                    ))->fetch()
                    ) {
                        $right['VIEW'] = true;
                        $right['ANSWER'] = true;
                    }
                }
            }
        }
        return $right;
    }

    public static function getUserRoleFilter($additional)
    {
        global $USER;
        $Role = User::getRole();
        $roleFilter = array();
        $filter = array();
        $hasCreate = false;
        if ($additional) {
            $roleFilter = $filter = $additional;
        }
        if ($Role == "C") {
            $c2cw = C2CWTable::getList(
                array(
                    'filter' => array('WORKER_USER_ID' => $USER->GetID())
                )
            );

            $filterCategory = array();
            $worker = false;
            while ($ClientWorker = $c2cw->fetch()) {
                $worker = true;
                if ($ClientWorker['R_VIEW'] == 'Y') {
                    $filterCategory[$ClientWorker['USER_ID']][] = $ClientWorker['CATEGORY_ID'];
                }
                if ($ClientWorker['R_CREATE'] == 'Y') {
                    $hasCreate = true;
                }
            }

            if ($worker) {
                if (empty($filterCategory)) {
                    $filter['OWNER_USER_ID'] = $USER->GetID();
                } else {
                    $filter['OWNER_USER_ID'] = $USER->GetID();
                    $wFilter = array('LOGIC' => 'OR');
                    foreach ($filterCategory as $UID => $category) {
                        $wFilter[] = array(
                            array('CATEGORY_ID' => $category),
                            array('OWNER_USER_ID' => $UID),
                        );
                    }
                    $roleFilter[] = $wFilter;
                }
            } else {
                $c2cw = C2CWTable::getList(
                    array(
                        'filter' => array('USER_ID' => $USER->GetID())
                    )
                );
                while ($arClientWorker = $c2cw->fetch()) {
                    $filter["OWNER_USER_ID"][] = $arClientWorker['WORKER_USER_ID'];
                }
                if (is_array($filter["OWNER_USER_ID"])) {
                    $filter["OWNER_USER_ID"][] = $USER->GetID();
                } else {
                    $filter["OWNER_USER_ID"] = $USER->GetID();
                }

                if (is_array($filter["OWNER_USER_ID"])) {
                    $filter["OWNER_USER_ID"] = array_unique($filter["OWNER_USER_ID"]);
                }

                $hasCreate = true;
            }
        }
        if ($Role == "E") {
            // check client 2 worker
            $wtc = WtCTable::getList(
                array(
                    'filter' => array('USER_ID' => $USER->GetID())
                )
            );

            $filterCategory = array();
            while ($Client = $wtc->fetch()) {
                if ($Client['R_VIEW'] == 'Y') {
                    $filterCategory[$Client['CLIENT_USER_ID']][] = $Client['CATEGORY_ID'];
                }
            }

            if (empty($filterCategory)) {
                $filter["RESPONSIBLE_USER_ID"] = $USER->GetID();
            } else {
                $filter["RESPONSIBLE_USER_ID"] = $USER->GetID();
                $pFilter = array('LOGIC' => 'OR');
                foreach ($filterCategory as $UID => $category) {
                    $pFilter[] = array(
                        array('CATEGORY_ID' => $category),
                        array('OWNER_USER_ID' => $UID),
                    );
                }
                $roleFilter[] = $pFilter;
            }
        }
        return array('roleFilter' => $roleFilter, 'filter' => $filter, 'HAS_CREATE' => $hasCreate);
    }

}