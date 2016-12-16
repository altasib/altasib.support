<?
#################################################
#        Company developer: ALTASIB
#        Developer: Evgeniy Pedan
#        Site: http://www.altasib.ru
#        E-mail: dev@altasib.ru
#        Copyright (c) 2006-2014 ALTASIB
#################################################
?>
<?
namespace ALTASIB\Support;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

Class TypeTimeTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'altasib_support_type_time';
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
                'DEFAULT' => array(
                        'data_type' => 'boolean',
                        'values' => array('N', 'Y'),
                ),                
        );
	}
    
    public static function add(array $data)
    {
        $data['DEFAULT'] = $data['DEFAULT']=='Y' ? 'Y' : 'N';
        return parent::add($data);
    }
    
    public static function update($primary, array $data)
    {
        $data['DEFAULT'] = $data['DEFAULT']=='Y' ? 'Y' : 'N';
        return parent::update($primary,$data);
    }    
}
?>