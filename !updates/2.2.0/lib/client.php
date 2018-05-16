<?php
/**
 * ALTASIB
 * @site http://www.altasib.ru
 * @email dev@altasib.ru
 * @copyright 2006-2018 ALTASIB
 */

namespace ALTASIB\Support;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

Class ClientTable extends Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'altasib_support_client';
    }

    public static function getMap()
    {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
            ),
            'USER_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_C2CW_ENTITY_USER_ID_FIELD')
            ),
            'USER' => array(
                'data_type' => 'ALTASIB\Support\User',
                'reference' => array('=this.USER_ID' => 'ref.ID')
            ),
            'RESPONSIBLE_USER_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_C2CW_ENTITY_RESPONSIBLE_USER_ID_FIELD')
            ),
            'RESPONSIBLE_USER' => array(
                'data_type' => 'ALTASIB\Support\User',
                'reference' => array('=this.RESPONSIBLE_USER_ID' => 'ref.ID')
            ),
        );
    }

    public static function isResponsible($responsibleUserId, $userId)
    {
        $userId = (int)$userId;
        if ($userId == 0) {
            return false;
        }
        return self::getRow(array(
            'filter' => array('RESPONSIBLE_USER_ID' => $responsibleUserId, 'USER_ID' => $userId),
            'select' => array('ID')
        ));
    }
}