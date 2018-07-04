<?php
/**
 * ALTASIB
 * @site http://www.altasib.ru
 * @email dev@altasib.ru
 * @copyright 2006-2018 ALTASIB
 */

namespace ALTASIB\Support;

use Bitrix\Main;
use Bitrix\Main\Type;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

Class TicketMessageTable extends Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'altasib_support_ticket_message';
    }

    public static function getMap()
    {
        $map = array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
            ),

            'TIMESTAMP' => array(
                'data_type' => 'datetime',
            ),

            'DATE_CREATE' => array(
                'data_type' => 'datetime',
                'required' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_MESSAGE_ENTITY_DATE_CREATE_FIELD')
            ),

            'TICKET_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_MESSAGE_ENTITY_TICKET_ID_FIELD')
            ),

            'TICKET' => array(
                'data_type' => 'Ticket',
                'reference' => array('=this.TICKET_ID' => 'ref.ID')
            ),

            'IS_HIDDEN' => array(
                'data_type' => 'boolean',
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_MESSAGE_ENTITY_IS_HIDDEN_FIELD'),
                'values' => array('N', 'Y')
            ),

            'IS_LOG' => array(
                'data_type' => 'boolean',
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_MESSAGE_ENTITY_IS_LOG_FIELD'),
                'values' => array('N', 'Y')
            ),

            'MESSAGE' => array(
                'data_type' => 'string',
                'required' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_MESSAGE_ENTITY_MESSAGE_FIELD')
            ),

            'CREATED_USER_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_C2CW_ENTITY_CREATED_USER_ID_FIELD')
            ),
            'CREATED_USER' => array(
                'data_type' => 'ALTASIB\Support\User',
                'reference' => array('=this.CREATED_USER_ID' => 'ref.ID')
            ),

            'MODIFIED_USER_ID' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('ALTASIB_SUPPORT_C2CW_ENTITY_MODIFIED_USER_ID_FIELD')
            ),
            'MODIFIED_USER' => array(
                'data_type' => 'ALTASIB\Support\User',
                'reference' => array('=this.MODIFIED_USER_ID' => 'ref.ID')
            ),

            'ELAPSED_TIME' => array(
                'data_type' => 'integer',
            ),
        );

        $map['READ'] = array(
            'data_type' => 'ALTASIB\SupportReport\MessageReadTable',
            'reference' => array('=this.ID' => 'ref.ID')
        );
        return $map;
    }

    public static function add($data)
    {
        global $USER;
        if (!isset($data['DATE_CREATE'])) {
            $data['DATE_CREATE'] = new Type\DateTime(date('Y-m-d H:i:s'), 'Y-m-d H:i:s');
        }

        if (!isset($data['CREATED_USER_ID'])) {
            $data['CREATED_USER_ID'] = $USER->GetID();
        }

        $IsSupportTeam = Tools::IsSupportTeam($data['CREATED_USER_ID']);

        $arFiles = array();
        if (array_key_exists('FILES', $data)) {
            if (is_array($data['FILES']) && count($data['FILES']) > 0) {
                $arFiles = $data['FILES'];
            }

            unset($data['FILES']);
        }

        $close = false;
        if (\is_set($data, 'CLOSE')) {
            $close = ($data['CLOSE'] == 'Y');
            unset($data['CLOSE']);
        }

        if (\is_set($data, 'IS_DEFERRED')) {
            $is_deferred = ($data['IS_DEFERRED'] == 'Y');
            unset($data['IS_DEFERRED']);
        }

        if (\is_set($data, 'CLOSE_EX')) {
            if ($data['CLOSE_EX'] == 'Y') {
                TicketTable::close($data['TICKET_ID'], false, false);
                return new Entity\Result();
            }
            unset($data['CLOSE_EX']);
        }

        if ($close && strlen($data['MESSAGE']) == 0) {
            TicketTable::close($data['TICKET_ID'], '');
            return new Entity\Result();
        }

        if (\is_set($data, 'NOT_CHANGE')) {
            $notChange = ($data['NOT_CHANGE'] == 'Y');
            unset($data['NOT_CHANGE']);
        }

        $result = parent::add($data);

        if ($result->isSuccess()) {
            $data['MESSAGE_ID'] = $result->getId();
            if ($close) {
                $data['IS_CLOSE'] = $close ? 'Y' : 'N';
                TicketTable::close($data['TICKET_ID'], '');
            }

            if (count($arFiles) > 0) {
                foreach ($arFiles as $FILE_ID) {
                    FileTable::add(array(
                        'MESSAGE_ID' => $result->getId(),
                        'TICKET_ID' => $data['TICKET_ID'],
                        'FILE_ID' => $FILE_ID
                    ));
                }
            }

            if (!defined('SUP_IMPORT')) {
                if ($data['IS_LOG'] != 'Y') {

                    $ticketUpdateFields = array(
                        'MODIFIED_USER_ID' => $USER->GetID(),
                        'LAST_MESSAGE_USER_ID' => $data['CREATED_USER_ID'],
                        'LAST_MESSAGE_DATE' => new Type\DateTime(),
                        'LAST_MESSAGE_BY_SUPPORT' => $IsSupportTeam ? 'Y' : 'N',
                        'TIMESTAMP' => new Type\DateTime(),
                        'IS_DEFERRED' => $is_deferred ? 'Y' : 'N',
                    );

                    if ($IsSupportTeam && $notChange) {
                        $ticketUpdateFields['LAST_MESSAGE_BY_SUPPORT'] = 'N';
                    }

                    $setFirstReplyStatus = \Bitrix\Main\Config\Option::get('altasib.support', 'SET_STATUS', 0);
                    if ($setFirstReplyStatus > 0) {
                        $countQuery = new \Bitrix\Main\Entity\Query(self::getEntity());
                        $countQuery
                            ->registerRuntimeField("CNT", array(
                                    "data_type" => "integer",
                                    "expression" => array("COUNT(1)")
                                )
                            )
                            ->setSelect(array("CNT"))
                            ->setFilter(array('TICKET_ID' => $data['TICKET_ID']));
                        $totalCount = $countQuery->setLimit(null)->setOffset(null)->exec()->fetch();
                        $totalCount = intval($totalCount['CNT']);
                        if ($totalCount == 1) {
                            $ticketUpdateFields['STATUS_ID'] = $setFirstReplyStatus;
                        }
                    }
                    if ($IsSupportTeam || $is_deferred) {
                        $ticketUpdateFields['IS_OVERDUE'] = 'N';
                    }

                    if ($data['IS_HIDDEN'] != 'Y') {
                        TicketTable::update($data['TICKET_ID'], $ticketUpdateFields);
                        Event::sendNotifyMessageClient($data);
                    }

                    Event::sendNotifyMessageSupport($data);

                    if ($is_deferred) {
                        Event::setDeferred($data['TICKET_ID']);
                    }
                }
            }
        }
        return $result;
    }

    public static function update($id, $data)
    {
        if (!isset($data['MODIFIED_USER_ID'])) {
            global $USER;
            $data['MODIFIED_USER_ID'] = $USER->GetID();
        }

        $arFiles = array();
        if (array_key_exists('FILES', $data)) {
            if (is_array($data['FILES']) && count($data['FILES']) > 0) {
                $arFiles = $data['FILES'];
            }

            unset($data['FILES']);
        }

        $result = parent::update($id, $data);

        if ($result->isSuccess()) {
            if (count($arFiles) > 0) {
                foreach ($arFiles as $FILE_ID) {
                    FileTable::add(array(
                        'MESSAGE_ID' => $id,
                        'TICKET_ID' => $data['TICKET_ID'],
                        'FILE_ID' => $FILE_ID
                    ));
                }
            }
        }
        return $result;
    }

    public static function delete($id)
    {
        $data = FileTable::getList(array('filter' => array('MESSAGE_ID' => $id), 'select' => array('FILE_ID')));
        while ($file = $data->fetch()) {
            \CFile::Delete($file['FILE_ID']);
        }
        parent::delete($id);
    }
}