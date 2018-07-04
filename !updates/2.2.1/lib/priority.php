<?php
/**
 * ALTASIB
 * @site http://www.altasib.ru
 * @email dev@altasib.ru
 * @copyright 2006-2018 ALTASIB
 */

namespace ALTASIB\Support;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Priority
{
    public static function get()
    {
        $list = array(
            "1" => Loc::getMessage("ALTASIB_SUPPORT_PRIORITY_LOW"),
            "2" => Loc::getMessage("ALTASIB_SUPPORT_PRIORITY_NORMAL"),
            "3" => Loc::getMessage("ALTASIB_SUPPORT_PRIORITY_HIGHT")
        );
        return $list;
    }

    public static function getName($ID)
    {
        $list = self::get();
        return $list[(int)$ID];
    }
}