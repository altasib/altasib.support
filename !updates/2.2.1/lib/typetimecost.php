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
use Bitrix\Main\Type;

Loc::loadMessages(__FILE__);

Class TypeTimeCostTable extends Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'altasib_support_type_time_cost';
    }

    public static function getMap()
    {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
            ),

            'DATE_START' => array(
                'data_type' => 'date',
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_ENTITY_TIMESTAMP_FIELD')
            ),

            'DATE_END' => array(
                'data_type' => 'date',
                'title' => Loc::getMessage('ALTASIB_SUPPORT_TICKET_ENTITY_TIMESTAMP_FIELD')
            ),

            'TYPE_TIME_ID' => array(
                'data_type' => 'integer',
                'required' => true,
            ),
            'COST' => array(
                'data_type' => 'integer',
            ),
            'RATE' => array(
                'data_type' => 'integer',
            ),
            'USER_ID' => array(
                'data_type' => 'integer',
            ),
            'IS_SPECIAL' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y'),
            ),
        );
    }

    public static function add($data)
    {
        $filter = array(
            'TYPE_TIME_ID' => $data['TYPE_TIME_ID'],
            '<DATE_START' => $data['DATE_START']->toString(),
            'DATE_END' => false
        );
        if (isset($data['USER_ID']) && $data['USER_ID'] > 0) {
            $filter['USER_ID'] = $data['USER_ID'];
        }

        $ob = self::getList(array(
            'order' => array('DATE_START' => 'DESC'),
            'filter' => $filter
        ));

        while ($timeCost = $ob->fetch()) {
            $endDate = new Type\Date(date('d.m.Y', \AddToTimeStamp(array('DD' => -1),
                MakeTimeStamp($data['DATE_START']->toString(), "DD.MM.YYYY 00:00:00"))), 'd.m.Y');
            self::update($timeCost['ID'], array('DATE_END' => $endDate));
        }
        parent::add($data);
    }

    public static function getCurrentCost($TYPE_ID, $USER_ID)
    {
        $ob = self::getList(array(
            'order' => array('DATE_START' => 'DESC'),
            'filter' => array(
                'TYPE_TIME_ID' => $TYPE_ID,
                'DATE_END' => false,
                'USER_ID' => $USER_ID > 0 ? $USER_ID : false
            )
        ));

        return $ob->fetch();
    }

    public static function isSpecialPrice($TYPE_ID, $USER_ID)
    {
        return self::getRow(array(
            'select' => array('ID'),
            'filter' => array('USER_ID' => $USER_ID, 'TYPE_TIME_ID' => $TYPE_ID)
        ));
    }

    public static function getCost($USER_ID)
    {
        static $result = array();
        $data = TypeTimeTable::getList();
        while ($type = $data->fetch()) {
            $current = TypeTimeCostTable::getCurrentCost($type['ID'], $USER_ID);
            $costType = array(
                'BY_TIME' => array(),
                'CURRENT' => $current['COST'],
            );

            $ob = TypeTimeCostTable::getList(array(
                'order' => array('DATE_START' => 'ASC'),
                'filter' => array('TYPE_TIME_ID' => $type['ID'], 'USER_ID' => $USER_ID > 0 ? $USER_ID : false)
            ));

            while ($cost = $ob->fetch()) {
                $costType['BY_TIME'][\MakeTimeStamp($cost['DATE_START']->toString(),
                    "DD.MM.YYYY 00:00:00")] = array('COST' => $cost['COST'], 'RATE' => $cost['RATE']);

                if (is_object($cost['DATE_END']) && strlen($cost['DATE_END']->toString()) > 0) {
                    if ($cost['DATE_START']->toString() != $cost['DATE_END']->toString()) {
                        $costType['BY_TIME'][\MakeTimeStamp($cost['DATE_END']->toString(),
                            "DD.MM.YYYY 00:00:00")] = array('COST' => $cost['COST'], 'RATE' => $cost['RATE']);
                    }
                } else {
                    $costType['CURRENT'] = $cost;
                    $costType['CURRENT']['ts'] = \MakeTimeStamp($cost['DATE_START']->toString());
                }
            }
            $result[$type['ID']] = $costType;
            if ($type['DEFAULT'] == 'Y') {
                $result[0] = $costType;
            }
        }
        return $result;
    }
}