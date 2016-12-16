<?
#################################################
#        Company developer: ALTASIB
#        Developer: Evgeniy Pedan
#        Site: http://www.altasib.ru
#        E-mail: dev@altasib.ru
#        Copyright (c) 2006-2015 ALTASIB
#################################################
?>
<?
namespace ALTASIB\Support;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class QuickResponseTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'altasib_support_quick_response';
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
            ),
            'DESCRIPTION' => array(
                    'data_type' => 'string',
            ),
            'SORT' => array(
                    'data_type' => 'integer',
            ),            
        );
	}
}
?>