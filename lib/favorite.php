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

use Bitrix\Main;
use Bitrix\Main\Type;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class FavoriteTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'altasib_support_ticket_message_favorite';
	}

	public static function getMap()
	{
        return array(
                'ID' => array(
                        'data_type' => 'integer',
                        'primary' => true,
                        'autocomplete' => true,
                ),

                'TICKET_ID' => array(
                        'data_type' => 'integer',
                        'required' => true,
                ),
                
                'MESSAGE_ID' => array(
                        'data_type' => 'integer',
                        'required' => true,
                ),

                'USER_ID' => array(
                        'data_type' => 'integer',
                        'required' => true,
                ),

                'USER' => array(
                        'data_type' => 'ALTASIB\Support\User',
                        'reference' => array('=this.USER_ID' => 'ref.ID'),
                        'required' => true,
                ),
        );
	}
    
    public static function add($data)
    {
        $res = parent::add($data);
        return $res;
    }
    
    public static function onBeforeAdd(Entity\Event $event)
    {
        $result = new Entity\EventResult;
        $data = $event->getParameter("fields");
        $entity = static::getEntity();
        if(isset($data["USER_ID"]) && isset($data['TICKET_ID']))
        {
            if(self::getList(array('filter'=>array('USER_ID'=>$data["USER_ID"],'MESSAGE_ID'=>$data["MESSAGE_ID"])))->fetch())
            {
                $result->addError(new Entity\FieldError($entity->getField('MESSAGE_ID'), 'Favorite exist'));
            }
        }
        return $result;
    }    
}
?>
