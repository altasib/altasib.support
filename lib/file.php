<?
#################################################
#        Company developer: ALTASIB
#        Developer: Evgeniy Pedan
#        Site: http://www.altasib.ru
#        E-mail: dev@altasib.ru
#        Copyright (c) 2006-2013 ALTASIB
#################################################
?>
<?
namespace ALTASIB\Support;

use Bitrix\Main;
use Bitrix\Main\Entity;
use Bitrix\Main\Type;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

Class FileTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'altasib_support_file_2_message';
	}

	public static function getMap()
	{
        return array(
                'ID' => array(
                        'data_type' => 'integer',
                        'primary' => true,
                        'autocomplete' => true,
                ),

                'MESSAGE_ID' => array(
                        'data_type' => 'integer',
                        'title' => Loc::getMessage('ALTASIB_SUPPORT_FILE_ENTITY_MESSAGE_ID_FIELD')
                ),

                'FILE_ID' => array(
                        'data_type' => 'integer',
                        'required' => true,
                        'title' => Loc::getMessage('ALTASIB_SUPPORT_FILE_ENTITY_FILE_ID_FIELD')
                ),

                'TICKET_ID' => array(
                        'data_type' => 'integer',
                        'required' => true,
                        'title' => Loc::getMessage('ALTASIB_SUPPORT_FILE_ENTITY_TICKET_ID_FIELD')
                ),
                'HASH' => array(
                        'data_type' => 'string',
                        'required' => true,
                        'title' => Loc::getMessage('ALTASIB_SUPPORT_FILE_ENTITY_HASH_FIELD')
                ),                
        );
	}
    public static function add(array $data)
    {
        if(!isset($data['HASH']))
        {
            $data['HASH'] = substr(md5(uniqid(mt_rand(), true).time()),0,255);
        }
        $result = parent::add($data);
        return $result; 
    }    
}
?>