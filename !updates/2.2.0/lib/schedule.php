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

class ScheduleTable extends Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'altasib_support_schedule';
    }

    public static function getMap()
    {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
            ),
            'SLA_ID' => array(
                'data_type' => 'integer',
            ),
            'WEEKDAY' => array(
                'data_type' => 'integer',
            ),
            'TIME_START' => array(
                'data_type' => 'datetime',
            ),
            'TIME_END' => array(
                'data_type' => 'datetime',
            ),

        );
    }
}