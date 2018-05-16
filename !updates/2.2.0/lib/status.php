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

Class StatusTable extends Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'altasib_support_status';
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
                'title' => Loc::getMessage('ALTASIB_SUPPORT_STATUS_ENTITY_NAME_FIELD')
            ),
            'SORT' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('ALTASIB_SUPPORT_STATUS_ENTITY_SORT_FIELD')
            ),
            'SKIP' => array(
                'data_type' => 'boolean',
                'title' => Loc::getMessage('ALTASIB_SUPPORT_STATUS_ENTITY_SORT_SKIP'),
                'values' => array('N', 'Y')
            ),
        );
    }

    public static function getName($id)
    {
        if ($id > 0) {
            //todo: getlist+select
            $ob = self::getById($id);
            if ($ar = $ob->fetch()) {
                return $ar["NAME"];
            }
        }

        return " - ";
    }
}