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

class CategoryTable extends Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'altasib_support_category';
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
                'title' => Loc::getMessage('ALTASIB_SUPPORT_CATEGORY_ENTITY_NAME_FIELD')
            ),
            'DESCRIPTION' => array(
                'data_type' => 'string',
                'title' => Loc::getMessage('ALTASIB_SUPPORT_CATEGORY_ENTITY_DESCRIPTION_FIELD'),
            ),
            'RESPONSIBLE_USER_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_CATEGORY_ENTITY_RESPONSIBLE_USER_ID_FIELD'),
                'validation' => function () {
                    return array(
                        function ($value, $primary, $row, $field) {
                            if ($value == 0) {
                                return Loc::getMessage('ALTASIB_SUPPORT_CATEGORY_ENTITY_RESPONSIBLE_USER_ID_FIELD_ERROR');
                            }
                            return true;
                        }
                    );
                }
            ),
            'RESPONSIBLE_USER' => array(
                'data_type' => 'ALTASIB\Support\User',
                'reference' => array('=this.RESPONSIBLE_USER_ID' => 'ref.ID')
            ),

            'USE_DEFAULT' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y'),
                'required' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_CATEGORY_ENTITY_USE_DEFAULT_FIELD')
            ),

            'NOT_CLOSE' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y'),
                'required' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_CATEGORY_ENTITY_NOT_CLOSE_FIELD')
            ),

        );
    }

    public static function add(array $data)
    {
        $data['USE_DEFAULT'] = $data['USE_DEFAULT'] == 'Y' ? 'Y' : 'N';
        $data['NOT_CLOSE'] = $data['NOT_CLOSE'] == 'Y' ? 'Y' : 'N';
        $result = parent::add($data);
        return $result;
    }

    public static function update($primary, array $data)
    {
        $data['USE_DEFAULT'] = $data['USE_DEFAULT'] == 'Y' ? 'Y' : 'N';
        $data['NOT_CLOSE'] = $data['NOT_CLOSE'] == 'Y' ? 'Y' : 'N';
        $result = parent::update($primary, $data);
        return $result;
    }
}