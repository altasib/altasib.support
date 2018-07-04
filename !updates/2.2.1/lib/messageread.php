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

Class MessageReadTable extends Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'altasib_support_ticket_message_read';
    }

    public static function getMap()
    {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
            ),

            'TICKET_ID' => array(
                'data_type' => 'integer',
                'required' => true,
            ),

            'TICKET' => array(
                'data_type' => 'ALTASIB\Support\Ticket',
                'reference' => array('=this.TICKET_ID' => 'ref.ID')
            ),

            'LAST_MESSAGE_ID' => array(
                'data_type' => 'integer',
                'required' => true,
            ),
            'USER_ID' => array(
                'data_type' => 'integer',
                'required' => true,
            ),

            'USER' => array(
                'data_type' => 'ALTASIB\Support\User',
                'reference' => array('=this.USER_ID' => 'ref.ID'),
                'required' => true,
            ),

            'READ_DATE' => array(
                'data_type' => 'datetime',
                'required' => true,
            ),
        );
    }

    public static function add($data)
    {
        if (!isset($data['READ_DATE'])) {
            $data['READ_DATE'] = new Type\DateTime(date('Y-m-d H:i:s'), 'Y-m-d H:i:s');
        }
        if (!isset($data['USER_ID'])) {
            global $USER;
            $data['USER_ID'] = $USER->GetID();
        }

        return parent::add($data);
    }

    public static function getLastUnreadId($ticketId)
    {
        global $USER;

        if ($data = self::getRow(array(
            'filter' => array('USER_ID' => $USER->GetID(), 'TICKET_ID' => $ticketId),
            'select' => array('LAST_MESSAGE_ID')
        ))
        ) {
            return $data['LAST_MESSAGE_ID'];
        }
        return 0;
    }

    public static function setLastUnreadId($fields)
    {
        if ($data = self::getRow(array(
            'filter' => array(
                'USER_ID' => $fields['USER_ID'],
                'TICKET_ID' => $fields['TICKET_ID']
            ),
            'select' => array('ID')
        ))
        ) {
            if ($fields['LAST_MESSAGE_ID'] > $data['ID']) {
                self::update($data['ID'], $fields);
            }
        } else {
            self::add($fields);
        }
    }
}