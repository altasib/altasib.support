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
use Bitrix\Main;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class SlaGroupTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}
        
	public static function getTableName()
	{
		return 'altasib_support_sla_group';
	}

	public static function getMap()
	{
        return array(
                'SLA_ID' => array(
                        'data_type' => 'integer',
                        'primary' => true
                ),
                'GROUP_ID' => array(
                        'data_type' => 'integer',
                ),                                               
        );
	}
    
    public static function set($SLA_ID,$group = array())
    {
        $data = self::getList(array(
            'filter' => array(
                'SLA_ID' => $SLA_ID
            )
        ));
        $connection = Main\Application::getConnection();
        $helper = $connection->getSqlHelper();
        
        while($sg = $data->fetch())
        {
            if(!in_array($sg['GROUP_ID'],$group))
            {
                $sql = 'DELETE FROM '.self::getTableName().' WHERE SLA_ID='.$SLA_ID.' AND GROUP_ID='.$sg['GROUP_ID'];
                $connection->queryExecute($sql);
            }
            else
            {
                unset($group[array_search($sg['GROUP_ID'],$group)]);
            }
        }
        
        foreach($group as $gid)
        {
            self::add(array(
                'SLA_ID'=>$SLA_ID,
                'GROUP_ID'=>$gid
            ));
        }
    }
}
?>