<?php
/**
 * ALTASIB
 * @site http://www.altasib.ru
 * @email dev@altasib.ru
 * @copyright 2006-2018 ALTASIB
 */

namespace ALTASIB\Support;

use Bitrix\Main\Type;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class SlaTable extends Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'altasib_support_sla';
    }

    public static function getMap()
    {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
            ),
            'NAME' => array(
                'data_type' => 'string',
                'required' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_SLA_ENTITY_NAME_FIELD')
            ),
            'DESCRIPTION' => array(
                'data_type' => 'string',
                'title' => Loc::getMessage('ALTASIB_SUPPORT_SLA_ENTITY_DESCRIPTION_FIELD')
            ),

            'SORT' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('ALTASIB_SUPPORT_SLA_ENTITY_SORT_FIELD')
            ),

            'RESPONSE_TIME' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('ALTASIB_SUPPORT_SLA_ENTITY_RESPONSE_TIME_FIELD')
            ),

            'NOTICE_TIME' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('ALTASIB_SUPPORT_SLA_ENTITY_NOTICE_TIME_FIELD')
            ),
            'DATE_CREATE' => array(
                'data_type' => 'datetime',
                'required' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_SLA_ENTITY_DATE_CREATE_FIELD')
            ),

            'CREATED_USER_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_SLA_ENTITY_CREATED_USER_ID_FIELD')
            ),
            'CREATED_USER' => array(
                'data_type' => 'ALTASIB\Support\User',
                'reference' => array('=this.CREATED_USER_ID' => 'ref.ID')
            ),

            'TIMESTAMP' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('ALTASIB_SUPPORT_SLA_ENTITY_TIMESTAMP_FIELD')
            ),

            'MODIFIED_USER_ID' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('ALTASIB_SUPPORT_SLA_ENTITY_MODIFIED_USER_ID_FIELD')
            ),
            'MODIFIED_USER' => array(
                'data_type' => 'ALTASIB\Support\User',
                'reference' => array('=this.MODIFIED_USER_ID' => 'ref.ID')
            ),

            'SLA_GROUP' => array(
                'data_type' => 'ALTASIB\Support\SlaGroupTable',
                'reference' => array('=this.ID' => 'ref.SLA_ID')
            ),
        );
    }

    public static function add(array $data)
    {
        $date = new Type\DateTime(date('Y-m-d H:i:s'), 'Y-m-d H:i:s');
        if (!isset($data['DATE_CREATE'])) {
            $data['DATE_CREATE'] = $date;
        }
        $date = new Type\DateTime(date('Y-m-d H:i:s'), 'Y-m-d H:i:s');
        if (!isset($data['TIMESTAMP'])) {
            $data['TIMESTAMP'] = $date;
        }

        if (!isset($data['CREATED_USER_ID'])) {
            global $USER;
            $data['CREATED_USER_ID'] = $USER->GetID();
        }

        if (!isset($data['MODIFIED_USER_ID'])) {
            global $USER;
            $data['MODIFIED_USER_ID'] = $USER->GetID();
        }
        return parent::add($data);
    }

    public static function update($id, array $data)
    {
        $date = new Type\DateTime(date('Y-m-d H:i:s'), 'Y-m-d H:i:s');
        if (!isset($data['TIMESTAMP'])) {
            $data['TIMESTAMP'] = $date;
        }
        if (!isset($data['MODIFIED_USER_ID'])) {
            global $USER;
            $data['MODIFIED_USER_ID'] = $USER->GetID();
        }
        return parent::update($id, $data);
    }

    public static function delete($id)
    {
        parent::delete($id);
        SlaGroupTable::delete($id);
    }

    public static function getUserSla($USER_ID)
    {
        $userGroups = \CUser::GetUserGroup($USER_ID);

        return SlaTable::getRow(array(
            'filter' => array('SLA_GROUP.GROUP_ID' => $userGroups),
            'select' => array('*', 'GROUP_' => 'SLA_GROUP')
        ));
    }
}